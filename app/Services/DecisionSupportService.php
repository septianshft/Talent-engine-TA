<?php

namespace App\Services;

use App\Models\TalentRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DecisionSupportService
{
    /**
     * Find and rank suitable talents for a given talent request using a Simple Additive Weighting (SAW) approach.
     *
     * @param TalentRequest $talentRequest The request to find talents for.
     * @param int $limit The maximum number of talents to return.
     * @return Collection A collection of ranked talents (User models) with their DSS scores.
     */
    public function findAndRankTalents(TalentRequest $talentRequest, int $limit = 5): Collection
    {
        Log::info(sprintf('[DSS] Processing TalentRequest ID: %d', $talentRequest->id));

        // 1. Extract required competencies, their proficiency levels, and user-defined weights from the request.
        // Note: The TalentRequest model\'s \'competencies\' relationship must be set up
        // to correctly pivot `required_proficiency_level` and `weight`.
        $requiredCompetenciesData = $talentRequest->competencies->map(function ($competency) {
            return (object) [
                'id' => $competency->id,
                'required_proficiency_level' => $competency->pivot->required_proficiency_level,
                'weight' => $competency->pivot->weight, // User-defined weight
            ];
        });

        Log::info('[DSS] Required Competencies (ID, Required Level, User Weight):', $requiredCompetenciesData->toArray());

        if ($requiredCompetenciesData->isEmpty()) {
            Log::info('[DSS] No competencies specified in the request. Returning empty collection.');
            return collect();
        }

        // Normalize user-defined weights so they sum to 1 (a core step in SAW).
        $sumOfRawWeights = $requiredCompetenciesData->sum('weight');
        Log::debug(sprintf('[DSS] Sum of raw (user-defined) weights: %f', $sumOfRawWeights));

        // The \'use\' keyword makes $sumOfRawWeights and $requiredCompetenciesData (for its count) available in the closure.
        $normalizedCompetenciesData = $requiredCompetenciesData->map(function ($reqComp) use ($sumOfRawWeights, $requiredCompetenciesData) {
            $normalizedWeight = 0;
            if ($sumOfRawWeights > 0) {
                $normalizedWeight = $reqComp->weight / $sumOfRawWeights;
            } elseif ($requiredCompetenciesData->count() > 0) {
                // If sum of raw weights is 0 but competencies exist (e.g., all weights are 0, or only one competency with weight 0),
                // assign equal weight to each. This prevents division by zero and ensures all competencies are considered.
                $normalizedWeight = 1 / $requiredCompetenciesData->count();
            }
            // If $requiredCompetenciesData is empty (though checked above), $normalizedWeight remains 0.

            return (object) [
                'id' => $reqComp->id,
                'required_proficiency_level' => $reqComp->required_proficiency_level,
                'original_weight' => $reqComp->weight, // Keep original user-defined weight for reference
                'weight' => $normalizedWeight,         // Normalized weight for calculations
            ];
        });
        Log::debug('[DSS] Normalized Competencies Data (ID, Required Level, Original Weight, Normalized Weight):', $normalizedCompetenciesData->toArray());


        // 2. Identify potential talents: users with the \'talent\' role who possess ALL required competencies
        //    at or above the specified proficiency level.
        $potentialTalentsQuery = User::whereHas('roles', function ($query) {
            $query->where('name', 'talent'); // Filter for users with the \'talent\' role
        });

        // Chain a whereHas condition for each required competency.
        // We iterate over $normalizedCompetenciesData for consistency, though only id and required_proficiency_level are used here.
        foreach ($normalizedCompetenciesData as $reqComp) {
            $potentialTalentsQuery->whereHas('competencies', function ($query) use ($reqComp) {
                $query->where('competencies.id', $reqComp->id)
                      ->where('competency_user.proficiency_level', '>=', $reqComp->required_proficiency_level);
            });
        }

        // Log the generated SQL query and bindings for debugging purposes.
        try {
            Log::debug('[DSS] Potential Talents Query SQL: ' . $potentialTalentsQuery->toSql());
            Log::debug('[DSS] Potential Talents Query Bindings: ', $potentialTalentsQuery->getBindings());
        } catch (\Exception $e) { // Corrected: Removed leading backslash for Exception
            Log::error('[DSS] Error generating SQL log for potential talents query: ' . $e->getMessage());
        }

        // Eager load the specific competencies relevant to this request for the potential talents.
        // This optimizes the subsequent scoring step by avoiding N+1 queries.
        $requiredCompetencyIds = $normalizedCompetenciesData->pluck('id')->all();
        $potentialTalents = $potentialTalentsQuery->with(['competencies' => function ($query) use ($requiredCompetencyIds) {
            $query->whereIn('competencies.id', $requiredCompetencyIds); // Load only the competencies relevant to this request
        }])->get();

        Log::info(sprintf('[DSS] Found %d potential talents after initial filtering.', $potentialTalents->count()));

        // 3. Score the potential talents using the normalized weights and their proficiency levels.
        //    Proficiency levels (performance scores in SAW) could also be normalized (e.g., to a 0-1 scale).
        //    Example: If proficiency is 1-5, normalized_proficiency = (current_proficiency - 1) / (max_proficiency - 1).
        //    For this implementation, we are using raw proficiency levels with normalized weights.
        //    $maxProficiencyPossible = 5; // Define if normalizing proficiency
        //    $minProficiencyPossible = 1; // Define if normalizing proficiency

        $rankedTalents = $potentialTalents->map(function ($talent) use ($normalizedCompetenciesData /*, $maxProficiencyPossible, $minProficiencyPossible */) {
            $score = 0;

            foreach ($normalizedCompetenciesData as $reqComp) {
                $talentCompetency = $talent->competencies->firstWhere('id', $reqComp->id);

                if ($talentCompetency) {
                    $talentProficiency = $talentCompetency->pivot->proficiency_level;

                    // Optional: Normalize talent\'s proficiency level (performance score for SAW)
                    // $isNormalizable = ($maxProficiencyPossible - $minProficiencyPossible) > 0;
                    // $normalizedTalentProficiency = $isNormalizable ?
                    //    ($talentProficiency - $minProficiencyPossible) / ($maxProficiencyPossible - $minProficiencyPossible) : 0;

                    // Calculate weighted score for this competency.
                    // If using normalized proficiency: $score += ($normalizedTalentProficiency * $reqComp->weight);
                    $score += ($talentProficiency * $reqComp->weight); // Using raw proficiency * normalized weight
                }
            }

            $talent->dss_score = $score; // Assign the calculated SAW score to the talent
            Log::debug(sprintf("[DSS] Scoring Talent ID: %d, Calculated DSS Score: %f", $talent->id, $talent->dss_score));

            return $talent;
        })
        ->sortByDesc('dss_score'); // Rank talents by their DSS score in descending order

        Log::info(sprintf('[DSS] Found %d ranked talents after SAW scoring.', $rankedTalents->count()));

        // 4. Return the top N ranked talents as per the specified limit.
        $finalTalents = $rankedTalents->take($limit);
        Log::info(sprintf('[DSS] Returning top %d talents.', $finalTalents->count()));
        return $finalTalents;
    }
}
