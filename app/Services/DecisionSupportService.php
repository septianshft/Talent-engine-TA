<?php

namespace App\Services;

use App\Models\TalentRequest;
use App\Models\User;
use App\Models\Competency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Add Log facade

class DecisionSupportService
{
    /**
     * Find and rank suitable talents for a given talent request.
     *
     * @param TalentRequest $talentRequest The request to find talents for.
     * @param int $limit The maximum number of talents to return.
     * @return Collection A collection of ranked talents (User models) with scores.
     */
    public function findAndRankTalents(TalentRequest $talentRequest, int $limit = 5): Collection
    {
        Log::info('[DSS] Processing Request ID: ' . $talentRequest->id);

        // 1. Get required competencies, proficiency levels, and weights from the request
        // The TalentRequest model's 'competencies' relationship should have `withPivot('required_proficiency_level', 'weight')`
        $requiredCompetenciesData = $talentRequest->competencies->map(function ($competency) {
            return (object) [ // Cast to object for easier access like $reqComp->id
                'id' => $competency->id,
                'required_proficiency_level' => $competency->pivot->required_proficiency_level,
                'weight' => $competency->pivot->weight,
            ];
        });

        Log::info('[DSS] Required Competencies (ID, Level, Weight):', $requiredCompetenciesData->toArray());

        if ($requiredCompetenciesData->isEmpty()) {
            Log::info('[DSS] No competencies required, returning empty collection.');
            return collect();
        }

        // 2. Find talents who have ALL the required competencies at or above the required level
        $potentialTalentsQuery = User::whereHas('roles', function ($query) {
            $query->where('name', 'talent'); // Ensure the user has the 'talent' role
        });

        // Chain a whereHas condition for each required competency
        foreach ($requiredCompetenciesData as $reqComp) {
            $potentialTalentsQuery->whereHas('competencies', function ($query) use ($reqComp) {
                $query->where('competencies.id', $reqComp->id)
                      ->where('competency_user.proficiency_level', '>=', $reqComp->required_proficiency_level);
            });
        }

        // Log the generated SQL query (for debugging)
        try {
            Log::debug('[DSS] Potential Talents Query SQL: ' . $potentialTalentsQuery->toSql());
            Log::debug('[DSS] Potential Talents Query Bindings: ', $potentialTalentsQuery->getBindings());
        } catch (\Exception $e) {
            Log::error('[DSS] Error generating SQL log: ' . $e->getMessage());
        }

        // Eager load the relevant competencies for scoring after filtering
        $requiredCompetencyIds = $requiredCompetenciesData->pluck('id')->all();
        $potentialTalents = $potentialTalentsQuery->with(['competencies' => function ($query) use ($requiredCompetencyIds) {
            $query->whereIn('competencies.id', $requiredCompetencyIds); // Load only the competencies relevant to the request
        }])->get();

        Log::info('[DSS] Found ' . $potentialTalents->count() . ' potential talents after initial query.');

        // 3. Score the potential talents using weights
        // Max proficiency is typically 4 (Expert), but not directly used in this weighted sum if weights handle relative importance.
        // $maxProficiency = 4; // Kept for reference if normalization is added later

        $rankedTalents = $potentialTalents->map(function ($talent) use ($requiredCompetenciesData) {
            $score = 0;

            foreach ($requiredCompetenciesData as $reqComp) {
                $talentCompetency = $talent->competencies->firstWhere('id', $reqComp->id);

                // The main query already ensures the talent meets the minimum required_proficiency_level.
                // Here, we just need to ensure the talent has the competency (which they should)
                // and then calculate the weighted score.
                if ($talentCompetency) {
                    // Weighted score: talent's proficiency level * weight for that competency in this request
                    $score += ($talentCompetency->pivot->proficiency_level * $reqComp->weight);
                }
            }

            $talent->dss_score = $score; // Store the calculated weighted score
            Log::debug("[DSS] Scoring Talent ID: {$talent->id}, Weighted Score: {$talent->dss_score}");

            return $talent;
        })
        ->sortByDesc('dss_score'); // Rank by the new weighted score descending

        Log::info('[DSS] Found ' . $rankedTalents->count() . ' ranked talents after weighted scoring.');

        // 4. Return the top N ranked talents
        $finalTalents = $rankedTalents->take($limit);
        Log::info('[DSS] Returning top ' . $finalTalents->count() . ' talents.');
        return $finalTalents;
    }
}
