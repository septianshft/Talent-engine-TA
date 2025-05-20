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

        // Normalize weights to sum to 1 (Formal SAW step)
        $sumOfRawWeights = $requiredCompetenciesData->sum('weight');
        Log::debug('[DSS] Sum of raw weights: ' . $sumOfRawWeights);

        // Pass $requiredCompetenciesData to the closure using 'use' to make its count accessible
        $normalizedCompetenciesData = $requiredCompetenciesData->map(function ($reqComp) use ($sumOfRawWeights, $requiredCompetenciesData) {
            $normalizedWeight = 0;
            if ($sumOfRawWeights > 0) {
                $normalizedWeight = $reqComp->weight / $sumOfRawWeights;
            } elseif ($requiredCompetenciesData->count() > 0) { // Check if the original collection had items
                // If sum is 0 but there are competencies (e.g., all user-defined weights were 0, or only one competency with weight 0),
                // assign equal weight. This prevents division by zero if sumOfRawWeights is 0
                // and ensures competencies are still considered.
                $normalizedWeight = 1 / $requiredCompetenciesData->count();
            }
            // If $requiredCompetenciesData->count() is 0, $normalizedWeight remains 0, which is fine as the loop won't run.

            return (object) [
                'id' => $reqComp->id,
                'required_proficiency_level' => $reqComp->required_proficiency_level,
                'original_weight' => $reqComp->weight, // Keep original for reference if needed
                'weight' => $normalizedWeight, // This 'weight' will now be the normalized weight
            ];
        });
        Log::debug('[DSS] Normalized Competencies Data:', $normalizedCompetenciesData->toArray());


        // 2. Find talents who have ALL the required competencies at or above the required level
        $potentialTalentsQuery = User::whereHas('roles', function ($query) {
            $query->where('name', 'talent'); // Ensure the user has the 'talent' role
        });

        // Chain a whereHas condition for each required competency using normalized data for consistency in checks if needed, though not strictly necessary for the DB query itself here
        foreach ($normalizedCompetenciesData as $reqComp) { // Iterate using normalized data
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
        $requiredCompetencyIds = $normalizedCompetenciesData->pluck('id')->all(); // Use IDs from normalized data
        $potentialTalents = $potentialTalentsQuery->with(['competencies' => function ($query) use ($requiredCompetencyIds) {
            $query->whereIn('competencies.id', $requiredCompetencyIds); // Load only the competencies relevant to the request
        }])->get();

        Log::info('[DSS] Found ' . $potentialTalents->count() . ' potential talents after initial query.');

        // 3. Score the potential talents using normalized weights
        // Max proficiency is typically 4 or 5 (Expert).
        // For a more formal SAW, proficiency levels (performance scores) could also be normalized, e.g., to a 0-1 scale.
        // Example: if proficiency is 1-5, normalized_proficiency = (current_proficiency - 1) / (5 - 1)
        // For now, we are only normalizing weights as per the immediate feedback.
        // $maxProficiencyPossible = 5; // Assuming max possible proficiency is 5
        // $minProficiencyPossible = 1; // Assuming min possible proficiency is 1

        $rankedTalents = $potentialTalents->map(function ($talent) use ($normalizedCompetenciesData) { // Use $normalizedCompetenciesData here
            $score = 0;

            foreach ($normalizedCompetenciesData as $reqComp) { // Iterate using normalized data
                $talentCompetency = $talent->competencies->firstWhere('id', $reqComp->id);

                if ($talentCompetency) {
                    // Current proficiency level of the talent for the competency
                    $talentProficiency = $talentCompetency->pivot->proficiency_level;

                    // Optional: Normalize proficiency level (performance score)
                    // $normalizedTalentProficiency = ($maxProficiencyPossible - $minProficiencyPossible) > 0 ?
                    //    ($talentProficiency - $minProficiencyPossible) / ($maxProficiencyPossible - $minProficiencyPossible) : 0;

                    // Weighted score: talent's proficiency level * NORMALIZED weight for that competency
                    // If performance scores were normalized: $score += ($normalizedTalentProficiency * $reqComp->weight);
                    $score += ($talentProficiency * $reqComp->weight);
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
