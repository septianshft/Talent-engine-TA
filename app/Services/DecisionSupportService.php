<?php

namespace App\Services;

use App\Models\TalentRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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

        $rankedTalents = $potentialTalents->map(function ($talent) use ($normalizedCompetenciesData, $talentRequest /*, $maxProficiencyPossible, $minProficiencyPossible */) {
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

            // Add location compatibility bonus to the score
            $locationBonus = $this->calculateLocationCompatibility($talent, $talentRequest);
            $score += $locationBonus;

            $talent->dss_score = $score; // Assign the calculated SAW score to the talent
            $talent->location_compatibility = $locationBonus; // Store location bonus for display
            Log::debug(sprintf("[DSS] Scoring Talent ID: %d, Base Score: %f, Location Bonus: %f, Total Score: %f",
                $talent->id, $score - $locationBonus, $locationBonus, $talent->dss_score));

            return $talent;
        })
        ->sortByDesc('dss_score'); // Rank talents by their DSS score in descending order

        Log::info(sprintf('[DSS] Found %d ranked talents after SAW scoring with location compatibility.', $rankedTalents->count()));

        // 4. Return the top N ranked talents as per the specified limit.
        $finalTalents = $rankedTalents->take($limit);
        Log::info(sprintf('[DSS] Returning top %d talents.', $finalTalents->count()));
        return $finalTalents;
    }

    /**
     * Calculate location compatibility bonus between talent and request
     *
     * @param User $talent
     * @param TalentRequest $talentRequest
     * @return float Location compatibility bonus (0.0 to 1.0)
     */
    private function calculateLocationCompatibility(User $talent, TalentRequest $talentRequest): float
    {
        $bonus = 0.0;

        // If work is remote, location doesn't matter much - give small bonus
        if ($talentRequest->work_location_type === 'remote') {
            $bonus += 0.2;
            Log::debug(sprintf("[DSS] Remote work bonus: 0.2 for talent ID: %d", $talent->id));
            return $bonus;
        }

        // Check country compatibility
        if ($talentRequest->work_location_country && $talent->domicile_country) {
            if (strtolower($talentRequest->work_location_country) === strtolower($talent->domicile_country)) {
                $bonus += 0.5; // Same country gets significant bonus
                Log::debug(sprintf("[DSS] Same country bonus: 0.5 for talent ID: %d", $talent->id));

                // Check city compatibility within same country
                if ($talentRequest->work_location_city && $talent->domicile_city) {
                    if (strtolower($talentRequest->work_location_city) === strtolower($talent->domicile_city)) {
                        $bonus += 0.3; // Same city gets additional bonus
                        Log::debug(sprintf("[DSS] Same city bonus: 0.3 for talent ID: %d", $talent->id));
                    } else {
                        // Different city in same country - check if it's a major city nearby
                        $bonus += $this->calculateCityProximityBonus($talent->domicile_city, $talentRequest->work_location_city);
                    }
                }
            } else {
                // Different countries - check if they're in the same region
                $bonus += $this->calculateRegionalProximityBonus($talent->domicile_country, $talentRequest->work_location_country);
            }
        }

        // For hybrid work, give slight preference to those in similar time zones or regions
        if ($talentRequest->work_location_type === 'hybrid') {
            $bonus *= 0.8; // Reduce location importance for hybrid work
        }

        Log::debug(sprintf("[DSS] Total location compatibility bonus: %f for talent ID: %d", $bonus, $talent->id));
        return min($bonus, 1.0); // Cap at 1.0
    }

    /**
     * Calculate proximity bonus for cities within the same country
     *
     * @param string $talentCity
     * @param string $requestCity
     * @return float
     */
    private function calculateCityProximityBonus(string $talentCity, string $requestCity): float
    {
        // Define major city clusters (simplified)
        $cityClusters = [
            'indonesia_java' => ['jakarta', 'bandung', 'surabaya', 'semarang', 'yogyakarta', 'solo'],
            'indonesia_sumatra' => ['medan', 'palembang', 'padang', 'pekanbaru'],
            'malaysia_west' => ['kuala lumpur', 'petaling jaya', 'shah alam', 'klang', 'seremban'],
            'singapore' => ['singapore city', 'jurong west', 'woodlands', 'tampines'],
            'usa_west_coast' => ['los angeles', 'san francisco', 'seattle', 'san diego', 'san jose'],
            'usa_east_coast' => ['new york', 'boston', 'philadelphia', 'washington dc', 'miami'],
            'uk_main' => ['london', 'birmingham', 'manchester', 'liverpool', 'leeds'],
        ];

        $talentCityLower = strtolower($talentCity);
        $requestCityLower = strtolower($requestCity);

        foreach ($cityClusters as $cluster) {
            if (in_array($talentCityLower, $cluster) && in_array($requestCityLower, $cluster)) {
                return 0.15; // Cities in same cluster get small bonus
            }
        }

        return 0.0;
    }

    /**
     * Calculate regional proximity bonus for different countries
     *
     * @param string $talentCountry
     * @param string $requestCountry
     * @return float
     */
    private function calculateRegionalProximityBonus(string $talentCountry, string $requestCountry): float
    {
        // Define regional groupings
        $regions = [
            'southeast_asia' => ['indonesia', 'singapore', 'malaysia', 'thailand', 'philippines', 'vietnam', 'myanmar', 'brunei'],
            'north_america' => ['united states', 'usa', 'canada'],
            'europe_west' => ['united kingdom', 'germany', 'france', 'netherlands', 'belgium', 'switzerland'],
            'europe_north' => ['norway', 'sweden', 'denmark', 'finland'],
            'oceania' => ['australia', 'new zealand'],
            'east_asia' => ['japan', 'south korea', 'china', 'taiwan'],
            'middle_east' => ['uae', 'saudi arabia', 'qatar', 'kuwait', 'palestine'],
        ];

        $talentCountryLower = strtolower($talentCountry);
        $requestCountryLower = strtolower($requestCountry);

        foreach ($regions as $region) {
            if (in_array($talentCountryLower, $region) && in_array($requestCountryLower, $region)) {
                return 0.2; // Same region gets moderate bonus
            }
        }

        return 0.0; // Different regions get no bonus
    }
}
