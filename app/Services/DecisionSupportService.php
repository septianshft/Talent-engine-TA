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

        // 1. Get required competencies and proficiency levels from the request
        $requiredCompetencies = $talentRequest->competencies()->pluck('required_proficiency_level', 'competency_id');
        Log::info('[DSS] Required Competencies:', $requiredCompetencies->toArray());

        if ($requiredCompetencies->isEmpty()) {
            Log::info('[DSS] No competencies required, returning empty collection.');
            return collect(); // No competencies required, return empty collection
        }

        // 2. Find talents who have ALL the required competencies at or above the required level
        // This query is complex. We need users who have a competency_user record for *each* required competency
        // where their proficiency_level meets or exceeds the required level.

        // 2. Find talents who have ALL the required competencies at or above the required level
        $potentialTalentsQuery = User::whereHas('roles', function ($query) {
            $query->where('name', 'talent'); // Ensure the user has the 'talent' role
        });

        // Chain a whereHas condition for each required competency
        foreach ($requiredCompetencies as $competencyId => $requiredLevel) {
            $potentialTalentsQuery->whereHas('competencies', function ($query) use ($competencyId, $requiredLevel) {
                $query->where('competencies.id', $competencyId)
                      ->where('competency_user.proficiency_level', '>=', $requiredLevel);
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
        $potentialTalents = $potentialTalentsQuery->with(['competencies' => function ($query) use ($requiredCompetencies) {
            $query->whereIn('competencies.id', $requiredCompetencies->keys());
        }])->get();

        Log::info('[DSS] Found ' . $potentialTalents->count() . ' potential talents after initial query.');

        // 3. Score the potential talents (Placeholder: Simple Additive Weighting - SAW)
        // For SAW, we might need weights for each competency (assuming equal weight for now)
        // Score = Sum(talent_proficiency / max_proficiency * weight)
        // Max proficiency is typically 4 (Expert)
        $maxProficiency = 4;
        $rankedTalents = $potentialTalents->map(function ($talent) use ($requiredCompetencies, $maxProficiency) {
            $score = 0;
            $matchedCompetenciesCount = 0;

            foreach ($requiredCompetencies as $competencyId => $requiredLevel) {
                $talentCompetency = $talent->competencies->firstWhere('id', $competencyId);
                if ($talentCompetency && $talentCompetency->pivot->proficiency_level >= $requiredLevel) {
                    // Simple score: sum of proficiency levels for required competencies
                    // More sophisticated scoring could involve weights, normalization, etc.
                    $score += $talentCompetency->pivot->proficiency_level;
                    $matchedCompetenciesCount++;
                }
            }

            // Add score to the talent object (could also use a DTO)
            $talent->dss_score = $score; // Store the calculated score

            Log::debug("[DSS] Scoring Talent ID: {$talent->id}, Score: {$talent->dss_score}");

            return $talent;
        })
        // The initial query already ensures talents meet all requirements, so no need for further filtering here.
        ->sortByDesc('dss_score'); // Rank by score descending

        Log::info('[DSS] Found ' . $rankedTalents->count() . ' ranked talents after scoring and filtering.');

        // 4. Return the top N ranked talents
        $finalTalents = $rankedTalents->take($limit);
        Log::info('[DSS] Returning top ' . $finalTalents->count() . ' talents.');
        return $finalTalents;
    }
}