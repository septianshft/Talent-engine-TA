<?php

namespace App\Services;

use App\Models\TalentRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EnhancedDecisionSupportService
{
    // Constants for enhanced DSS configuration
    const MAX_SINGLE_WEIGHT_PERCENTAGE = 1.0; // Maximum 100% for any single competency
    // const MAX_WEIGHT_VARIANCE = 0.3; // This was for competency weights, now removed for that purpose.
                                        // The calculateVariance method itself is kept if used elsewhere.
    const COMPETENCY_WEIGHT_PERCENTAGE = 0.85; // 85% weight for competencies
    const LOCATION_WEIGHT_PERCENTAGE = 0.15; // 15% weight for location
    const MIN_PROFICIENCY_LEVEL = 1;
    const MAX_PROFICIENCY_LEVEL = 5;
    const VETO_THRESHOLD_PERCENTAGE = 0.8; // 80% of required level as veto threshold
    const MIN_CONFIDENCE_SCORE = 0.6; // Minimum confidence score for results

    /**
     * Find and rank suitable talents for a given talent request using Enhanced SAW approach.
     *
     * @param TalentRequest $talentRequest The request to find talents for.
     * @param int $limit The maximum number of talents to return.
     * @return Collection A collection of ranked talents with enhanced scoring.
     */    public function findAndRankTalents(TalentRequest $talentRequest, int $limit = 10): Collection
    {
        Log::info('[Enhanced DSS] Starting enhanced talent ranking process', [
            'request_id' => $talentRequest->id,
            'limit' => $limit,
            'competencies_count' => $talentRequest->competencies->count()
        ]);

        // Step 1: Extract and validate required competencies
        $requiredCompetencies = $this->extractRequiredCompetencies($talentRequest);
        Log::info('[Enhanced DSS] Extracted competencies', [
            'count' => $requiredCompetencies->count(),
            'competencies' => $requiredCompetencies->map(function($comp) {
                return [
                    'id' => $comp->id,
                    'name' => $comp->name,
                    'required_level' => $comp->required_proficiency_level,
                    'weight' => $comp->weight,
                    'is_critical' => $comp->is_critical
                ];
            })->toArray()
        ]);

        if ($requiredCompetencies->isEmpty()) {
            Log::warning('[Enhanced DSS] No competencies specified in the request');
            return collect();
        }

        // Step 2: Validate weight distribution (only if we have valid competencies)
        $weights = $requiredCompetencies->pluck('weight')->toArray();
        if (empty($weights)) {
            Log::warning('[Enhanced DSS] No valid competencies found after filtering');
            return collect();
        }

        // Skip weight validation if we only have one competency after filtering
        // This handles the edge case where invalid competencies are filtered out
        if (count($weights) > 1) {
            Log::info('[Enhanced DSS] Validating weights', ['weights' => $weights]);
            $this->validateWeightDistribution($weights);
        } else {
            Log::info('[Enhanced DSS] Single competency remaining, skipping weight validation', ['weight' => $weights[0]]);
        }

        // Step 3: Normalize weights including location factor
        $normalizedCompetencies = $this->normalizeWeights($requiredCompetencies);
        Log::info('[Enhanced DSS] Normalized competencies', [
            'normalized' => $normalizedCompetencies->map(function($comp) {
                return [
                    'name' => $comp->name,
                    'normalized_weight' => $comp->normalized_weight ?? 'not_set'
                ];
            })->toArray()
        ]);

        // Step 4: Filter qualified talents with veto thresholds
        $qualifiedTalents = $this->filterQualifiedTalents($normalizedCompetencies, $talentRequest);
        Log::info('[Enhanced DSS] Filtered qualified talents', [
            'count' => $qualifiedTalents->count(),
            'talent_ids' => $qualifiedTalents->pluck('id')->toArray()
        ]);

        if ($qualifiedTalents->isEmpty()) {
            Log::warning('[Enhanced DSS] No qualified talents found after filtering');
            return collect();
        }

        // Step 5: Normalize performance values for fair comparison
        $normalizedPerformances = $this->normalizePerformanceValues($qualifiedTalents, $normalizedCompetencies);

        // Step 6: Calculate SAW scores with proper normalization
        $rankedTalents = $this->calculateEnhancedSAWScores(
            $qualifiedTalents,
            $normalizedCompetencies,
            $normalizedPerformances,
            $talentRequest
        );

        // Step 7: Perform sensitivity analysis for top candidates
        $topTalents = $rankedTalents->take($limit);

        // Calculate sensitivity and stability scores for each talent
        $talentsWithSensitivity = $topTalents->map(function ($result) use ($normalizedCompetencies, $qualifiedTalents) {
            $sensitivityData = $this->calculateSensitivityScoresForTalent(
                $result['talent'],
                $normalizedCompetencies,
                $qualifiedTalents
            );

            $result['sensitivity_score'] = $sensitivityData['sensitivity_score'];
            $result['stability_score'] = $sensitivityData['stability_score'];

            return $result;
        });

        Log::info('[Enhanced DSS] Process completed', [
            'qualified_talents' => $qualifiedTalents->count(),
            'returned_talents' => $talentsWithSensitivity->count()
        ]);

        return $talentsWithSensitivity;
    }

    /**
     * Extract required competencies with validation
     */
    private function extractRequiredCompetencies(TalentRequest $talentRequest): Collection
    {
        return $talentRequest->competencies->map(function ($competency) {
            $weight = $competency->pivot->weight ?? 0; // Default to 0 if not set, allowing 0 as a valid user choice.
            $requiredLevel = $competency->pivot->required_proficiency_level ?? self::MIN_PROFICIENCY_LEVEL;

            // Validate inputs
            if ($weight < 0) { // Weight must be non-negative. 0 is allowed.
                throw ValidationException::withMessages(['weight' => "Weight must be non-negative for competency: {$competency->name}"]);
            }

            // Handle invalid proficiency levels gracefully by skipping this competency
            if ($requiredLevel < self::MIN_PROFICIENCY_LEVEL || $requiredLevel > self::MAX_PROFICIENCY_LEVEL) {
                Log::warning('[Enhanced DSS] Invalid proficiency level', [
                    'competency' => $competency->name,
                    'level' => $requiredLevel,
                    'min' => self::MIN_PROFICIENCY_LEVEL,
                    'max' => self::MAX_PROFICIENCY_LEVEL
                ]);
                return null; // This will be filtered out
            }

            return (object) [
                'id' => $competency->id,
                'name' => $competency->name,
                'required_proficiency_level' => $requiredLevel,
                'weight' => $weight,
                'is_critical' => $competency->pivot->is_critical ?? false,
                'veto_threshold' => $competency->pivot->veto_threshold ?? $requiredLevel
            ];
        })->filter(); // Remove null values (invalid competencies)
    }

    /**
     * Validate weight distribution to prevent extreme distributions
     */    private function validateWeightDistribution(array $weights): void
    {
        if (empty($weights)) {
            Log::warning('[Enhanced DSS] validateWeightDistribution called with empty weights array.');
            // If no competencies are specified, this might be valid depending on overall logic.
            return;
        }

        $totalWeight = array_sum($weights);

        // If all weights are zero (totalWeight is 0), it's a valid scenario.
        // User might not want to weigh competencies, or only one competency with weight 0.
        if ($totalWeight == 0) {
            Log::debug('[Enhanced DSS] All competency weights sum to zero. Skipping dominance check.');
            return;
        }

        $maxWeight = max($weights);

        // Check if single criterion dominates. MAX_SINGLE_WEIGHT_PERCENTAGE is 1.0 (100%).
        // This means a single competency can take up all the weight if totalWeight > 0.
        // If maxWeight == totalWeight (e.g., one competency has 100, others 0),
        // then $maxWeight / $totalWeight will be 1.0, which is <= self::MAX_SINGLE_WEIGHT_PERCENTAGE (1.0).
        if (($maxWeight / $totalWeight) > self::MAX_SINGLE_WEIGHT_PERCENTAGE) {
            // This condition should not be met if MAX_SINGLE_WEIGHT_PERCENTAGE is 1.0
            // and weights are non-negative, as maxWeight cannot be greater than totalWeight.
            // Keeping for robustness.
            throw ValidationException::withMessages([
                'weights' => sprintf(
                    'Single competency weight (%.2f) exceeds the maximum allowed percentage (%.0f%%) of total non-zero weights (%.2f).',
                    $maxWeight,
                    self::MAX_SINGLE_WEIGHT_PERCENTAGE * 100,
                    $totalWeight
                )
            ]);
        }

        // The MAX_WEIGHT_VARIANCE check for competency weights has been removed from this method.
        // The calculateVariance() method itself is kept as it might be used for other purposes.

        Log::debug('[Enhanced DSS] Weight validation passed for competency weights.', [
            'total_weight' => $totalWeight,
            'max_weight_found' => $maxWeight,
            'max_weight_percentage_of_total' => ($totalWeight > 0) ? ($maxWeight / $totalWeight) * 100 : 'N/A',
        ]);
    }

    /**
     * Calculate variance of a given array of values.
     * This method is kept as it might be used for other calculations (e.g., proficiency variance).
     */
    private function calculateVariance(array $values): float
    {
        $count = count($values);
        if ($count <= 1) { // Variance is 0 or undefined for single/no values.
            return 0.0;
        }
        $mean = array_sum($values) / $count;
        $sumSquaredDiffs = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values));

        return $sumSquaredDiffs / $count;
    }

    /**
     * Normalize weights including location factor
     */
    private function normalizeWeights(Collection $competencies): Collection
    {
        $totalCompetencyWeight = $competencies->sum('weight');

        return $competencies->map(function ($competency) use ($totalCompetencyWeight) {
            // Competencies get 85% of total weight, location gets 15%
            // Handle case where totalCompetencyWeight is 0 to avoid division by zero.
            $normalizedWeight = 0;
            if ($totalCompetencyWeight > 0) {
                $normalizedWeight = ($competency->weight / $totalCompetencyWeight) * self::COMPETENCY_WEIGHT_PERCENTAGE;
            }

            return (object) [
                'id' => $competency->id,
                'name' => $competency->name,
                'required_proficiency_level' => $competency->required_proficiency_level,
                'original_weight' => $competency->weight,
                'normalized_weight' => $normalizedWeight,
                'is_critical' => $competency->is_critical,
                'veto_threshold' => $competency->veto_threshold
            ];
        });
    }

    /**
     * Filter talents meeting minimum requirements with veto thresholds
     */
    private function filterQualifiedTalents(Collection $normalizedCompetencies, TalentRequest $talentRequest): Collection
    {
        Log::info('[Enhanced DSS] Starting talent filtering', [
            'competencies_to_check' => $normalizedCompetencies->count(),
            'competency_details' => $normalizedCompetencies->map(function($comp) {
                return [
                    'id' => $comp->id,
                    'name' => $comp->name,
                    'required_level' => $comp->required_proficiency_level,
                    'is_critical' => $comp->is_critical,
                    'veto_threshold' => $comp->veto_threshold
                ];
            })->toArray()
        ]);

        // First, check if we have any talents with the 'talent' role
        $allTalents = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->count();

        Log::info('[Enhanced DSS] Total talents with role', ['count' => $allTalents]);

        // Check if talents have any competencies at all
        $talentsWithCompetencies = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->whereHas('competencies')->count();

        Log::info('[Enhanced DSS] Talents with competencies', ['count' => $talentsWithCompetencies]);

        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        });

        // Apply hard filters for each competency
        foreach ($normalizedCompetencies as $competency) {
            Log::debug('[Enhanced DSS] Adding competency filter', [
                'competency_id' => $competency->id,
                'competency_name' => $competency->name,
                'required_level' => $competency->required_proficiency_level
            ]);

            $query->whereHas('competencies', function ($q) use ($competency) {
                $q->where('competencies.id', $competency->id)
                  ->where('competency_user.proficiency_level', '>=', $competency->required_proficiency_level);
            });
        }

        // Log the SQL query and bindings
        Log::debug('[Enhanced DSS] Final SQL Query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $requiredCompetencyIds = $normalizedCompetencies->pluck('id')->toArray();
        $talents = $query->with(['competencies' => function ($q) use ($requiredCompetencyIds) {
            $q->whereIn('competencies.id', $requiredCompetencyIds);
        }])->get();

        Log::info('[Enhanced DSS] Initial query results', [
            'talents_found' => $talents->count(),
            'talent_ids' => $talents->pluck('id')->toArray()
        ]);

        // Log details about each talent's competencies
        foreach ($talents as $talent) {
            $competencyDetails = $talent->competencies->map(function($comp) {
                return [
                    'id' => $comp->id,
                    'name' => $comp->name,
                    'proficiency_level' => $comp->pivot->proficiency_level
                ];
            })->toArray();

            Log::debug('[Enhanced DSS] Talent competency details', [
                'talent_id' => $talent->id,
                'talent_name' => $talent->name,
                'competencies' => $competencyDetails
            ]);
        }

        // Apply veto thresholds (eliminate talents below critical thresholds)
        $qualifiedTalents = $talents->filter(function ($talent) use ($normalizedCompetencies) {
            foreach ($normalizedCompetencies as $competency) {
                if ($competency->is_critical) {
                    $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);
                    if (!$talentCompetency ||
                        $talentCompetency->pivot->proficiency_level < $competency->veto_threshold) {
                        Log::debug('[Enhanced DSS] Talent eliminated by veto threshold', [
                            'talent_id' => $talent->id,
                            'competency' => $competency->name,
                            'required_threshold' => $competency->veto_threshold,
                            'talent_level' => $talentCompetency->pivot->proficiency_level ?? 0
                        ]);
                        return false;
                    }
                }
            }
            return true;
        });

        Log::info('[Enhanced DSS] Talent filtering completed', [
            'initial_talents' => $talents->count(),
            'qualified_after_veto' => $qualifiedTalents->count()
        ]);

        return $qualifiedTalents;
    }

    /**
     * Normalize performance values for fair comparison across talents
     */
    private function normalizePerformanceValues(Collection $talents, Collection $competencies): array
    {
        $normalized = [];

        foreach ($competencies as $competency) {
            $competencyId = $competency->id;
            $proficiencyLevels = [];

            // Collect all proficiency levels for this competency
            foreach ($talents as $talent) {
                $talentCompetency = $talent->competencies->firstWhere('id', $competencyId);
                if ($talentCompetency) {
                    $proficiencyLevels[] = $talentCompetency->pivot->proficiency_level;
                }
            }

            if (empty($proficiencyLevels)) {
                continue;
            }

            $minLevel = min($proficiencyLevels);
            $maxLevel = max($proficiencyLevels);
            $range = $maxLevel - $minLevel;

            // Normalize each talent's performance for this competency
            foreach ($talents as $talent) {
                $talentCompetency = $talent->competencies->firstWhere('id', $competencyId);
                if ($talentCompetency) {
                    $rawLevel = $talentCompetency->pivot->proficiency_level;
                    // Normalize to 0-1 scale
                    $normalizedLevel = $range > 0 ? ($rawLevel - $minLevel) / $range : 1.0;
                    $normalized[$talent->id][$competencyId] = $normalizedLevel;
                }
            }
        }

        return $normalized;
    }

    /**
     * Calculate enhanced SAW scores with proper normalization
     */
    private function calculateEnhancedSAWScores(
        Collection $talents,
        Collection $competencies,
        array $normalizedPerformances,
        TalentRequest $talentRequest
    ): Collection {

        return $talents->map(function ($talent) use ($competencies, $normalizedPerformances, $talentRequest) {
            $competencyScore = 0;
            $scoreBreakdown = [];
            $competencyScores = [];

            // Calculate competency scores using normalized values
            foreach ($competencies as $competency) {
                $normalizedPerformance = $normalizedPerformances[$talent->id][$competency->id] ?? 0;
                $contribution = $normalizedPerformance * $competency->normalized_weight;
                $competencyScore += $contribution;

                $scoreBreakdown[$competency->name] = [
                    'normalized_performance' => $normalizedPerformance,
                    'weight' => $competency->normalized_weight,
                    'contribution' => $contribution
                ];

                // Store individual competency scores for the test assertions
                $competencyScores[$competency->name] = $normalizedPerformance;
            }

            // Calculate location compatibility with proper normalization
            $locationScore = $this->calculateNormalizedLocationScore($talent, $talentRequest);
            $locationContribution = $locationScore * self::LOCATION_WEIGHT_PERCENTAGE;
            $totalScore = $competencyScore + $locationContribution;

            // Calculate confidence score
            $confidenceScore = $this->calculateConfidenceScore($talent, $competencies);

            // Calculate confidence factors for detailed breakdown
            $confidenceFactors = $this->calculateConfidenceFactors($talent, $competencies);

            Log::debug('[Enhanced DSS] Talent scored', [
                'talent_id' => $talent->id,
                'competency_score' => $competencyScore,
                'location_score' => $locationScore,
                'total_score' => $totalScore,
                'confidence' => $confidenceScore
            ]);

            // Return array structure that matches test expectations
            return [
                'talent' => $talent,
                'saw_score' => $totalScore,
                'dss_score' => $totalScore, // For backward compatibility
                'competency_score' => $competencyScore,
                'location_score' => $locationScore,
                'confidence_score' => $confidenceScore,
                'competency_scores' => $competencyScores,
                'score_breakdown' => $scoreBreakdown,
                'confidence_factors' => $confidenceFactors,
                // Placeholder for sensitivity analysis - will be populated later
                'sensitivity_score' => 0.0,
                'stability_score' => 0.0,
            ];
        })->sortByDesc('saw_score')->values();
    }

    /**
     * Calculate normalized location compatibility score
     */
    private function calculateNormalizedLocationScore(User $talent, TalentRequest $talentRequest): float
    {
        // Remote work gets neutral score
        if ($talentRequest->work_location_type === 'remote') {
            return 0.5; // Neutral score for remote work
        }

        $score = 0;

        // Country compatibility (40% of location score)
        if ($this->isLocationMatch($talent->domicile_country, $talentRequest->work_location_country)) {
            $score += 0.4;

            // City compatibility (30% of location score)
            if ($this->isLocationMatch($talent->domicile_city, $talentRequest->work_location_city)) {
                $score += 0.3;
            } else {
                // Partial score for city proximity
                $score += $this->calculateCityProximity($talent->domicile_city, $talentRequest->work_location_city) * 0.15;
            }
        } else {
            // Regional proximity (20% of location score)
            $score += $this->calculateRegionalProximity($talent->domicile_country, $talentRequest->work_location_country) * 0.2;
        }

        // Time zone compatibility (20% of location score)
        $score += $this->calculateTimezoneCompatibility($talent, $talentRequest) * 0.2;

        // Hybrid work adjustment
        if ($talentRequest->work_location_type === 'hybrid') {
            $score *= 0.8; // Reduce location importance for hybrid
        }

        return min($score, 1.0); // Ensure score doesn't exceed 1.0
    }

    /**
     * Check if two locations match (case-insensitive)
     */
    private function isLocationMatch(?string $location1, ?string $location2): bool
    {
        if (!$location1 || !$location2) {
            return false;
        }

        return strtolower(trim($location1)) === strtolower(trim($location2));
    }

    /**
     * Calculate city proximity within same country/region
     */
    private function calculateCityProximity(?string $city1, ?string $city2): float
    {
        if (!$city1 || !$city2) {
            return 0;
        }

        // City clusters for proximity calculation
        $cityClusters = [
            'jakarta_region' => ['jakarta', 'tangerang', 'bekasi', 'depok', 'bogor'],
            'bandung_region' => ['bandung', 'cimahi', 'sumedang'],
            'surabaya_region' => ['surabaya', 'sidoarjo', 'gresik'],
            'malaysia_klang_valley' => ['kuala lumpur', 'petaling jaya', 'shah alam', 'subang jaya'],
            'singapore_region' => ['singapore', 'jurong', 'woodlands', 'tampines'],
        ];

        $city1Lower = strtolower($city1);
        $city2Lower = strtolower($city2);

        foreach ($cityClusters as $cluster) {
            if (in_array($city1Lower, $cluster) && in_array($city2Lower, $cluster)) {
                return 0.8; // High proximity within cluster
            }
        }

        return 0; // No proximity
    }

    /**
     * Calculate regional proximity for different countries
     */
    private function calculateRegionalProximity(?string $country1, ?string $country2): float
    {
        if (!$country1 || !$country2) {
            return 0;
        }

        $regions = [
            'southeast_asia' => ['indonesia', 'singapore', 'malaysia', 'thailand', 'philippines'],
            'asia_pacific' => ['japan', 'south korea', 'australia', 'new zealand'],
            'north_america' => ['united states', 'usa', 'canada'],
            'europe' => ['united kingdom', 'germany', 'france', 'netherlands'],
        ];

        $country1Lower = strtolower($country1);
        $country2Lower = strtolower($country2);

        foreach ($regions as $region) {
            if (in_array($country1Lower, $region) && in_array($country2Lower, $region)) {
                return 0.6; // Moderate proximity within region
            }
        }

        return 0; // No regional proximity
    }

    /**
     * Calculate timezone compatibility (simplified)
     */
    private function calculateTimezoneCompatibility(User $talent, TalentRequest $talentRequest): float
    {
        // Simplified timezone compatibility based on country
        $timezoneMap = [
            'indonesia' => 7, 'singapore' => 8, 'malaysia' => 8,
            'japan' => 9, 'australia' => 10, 'usa' => -5,
            'united kingdom' => 0, 'germany' => 1
        ];

        $talentTz = $timezoneMap[strtolower($talent->domicile_country ?? '')] ?? 0;
        $requestTz = $timezoneMap[strtolower($talentRequest->work_location_country ?? '')] ?? 0;

        $timeDifference = abs($talentTz - $requestTz);

        if ($timeDifference <= 2) {
            return 1.0; // Excellent timezone compatibility
        } elseif ($timeDifference <= 6) {
            return 0.6; // Good timezone compatibility
        } else {
            return 0.2; // Poor timezone compatibility
        }
    }

    /**
     * Calculate confidence score based on various factors
     */
    private function calculateConfidenceScore(User $talent, Collection $competencies): float
    {
        $factors = $this->calculateConfidenceFactors($talent, $competencies);

        // Combine factors
        $confidenceScore = ($factors['competency_count'] * 0.4) +
                          ($factors['skill_consistency'] * 0.4) +
                          ($factors['overqualification'] * 0.2) +
                          ($factors['completeness'] * 0.1);

        return min($confidenceScore, 1.0);
    }

    /**
     * Calculate detailed confidence factors
     */
    private function calculateConfidenceFactors(User $talent, Collection $competencies): array
    {
        $factors = [];

        // Factor 1: Number of competencies (more criteria = higher confidence)
        $competencyCount = $competencies->count();
        $factors['competency_count'] = min($competencyCount / 5, 1.0);

        // Factor 2: Skill consistency (how consistent are the talent's skill levels)
        $proficiencyLevels = [];
        foreach ($competencies as $competency) {
            $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);
            if ($talentCompetency) {
                $proficiencyLevels[] = $talentCompetency->pivot->proficiency_level;
            }
        }

        if (!empty($proficiencyLevels)) {
            $variance = $this->calculateVariance($proficiencyLevels);
            $factors['skill_consistency'] = max(0, 1 - ($variance / 4)); // Lower variance = higher confidence
        } else {
            $factors['skill_consistency'] = 0;
        }

        // Factor 3: Over-qualification bonus
        $overqualificationBonus = 0;
        foreach ($competencies as $competency) {
            $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);
            if ($talentCompetency) {
                $excess = $talentCompetency->pivot->proficiency_level - $competency->required_proficiency_level;
                if ($excess > 0) {
                    $overqualificationBonus += $excess * $competency->normalized_weight;
                }
            }
        }
        $factors['overqualification'] = min($overqualificationBonus / 2, 0.5); // Cap at 0.5

        // Factor 4: Completeness (percentage of competencies talent possesses)
        $totalCompetencies = $competencies->count();
        $possessedCompetencies = 0;
        foreach ($competencies as $competency) {
            $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);
            if ($talentCompetency) {
                $possessedCompetencies++;
            }
        }
        $factors['completeness'] = $totalCompetencies > 0 ? $possessedCompetencies / $totalCompetencies : 0;

        return $factors;
    }

    /**
     * Get methodology explanation for transparency
     */
    public function getMethodologyExplanation(): array
    {
        return [
            'method' => 'Enhanced Simple Additive Weighting (SAW) with Veto Thresholds and Sensitivity Analysis',
            'scoring_components' => [
                'competencies' => self::COMPETENCY_WEIGHT_PERCENTAGE * 100 . '%',
                'location_proximity' => self::LOCATION_WEIGHT_PERCENTAGE * 100 . '%'
            ],
            'validation_rules' => [
                'max_single_competency_weight' => self::MAX_SINGLE_WEIGHT_PERCENTAGE * 100 . '%',
                // 'max_weight_variance' => 'Removed for competency weights',
                'min_proficiency_level' => self::MIN_PROFICIENCY_LEVEL,
                'max_proficiency_level' => self::MAX_PROFICIENCY_LEVEL,
                'veto_threshold_percentage' => self::VETO_THRESHOLD_PERCENTAGE * 100 . '%',
                'min_confidence_score' => self::MIN_CONFIDENCE_SCORE * 100 . '%'
            ],
            'normalization_approach' => 'Min-Max normalization for performance values, Proportional normalization for weights',
            'ranking_stability_metrics' => 'Sensitivity Score (impact of weight changes), Stability Score (consistency of rank)',
            'steps' => [
                'Step 1: Extract and validate required competencies and their user-defined weights (0-100% allowed per competency).',
                'Step 2: Validate overall competency weight distribution (single competency can be up to 100% of total user-defined weight).',
                'Step 3: Normalize user-defined competency weights and combine with system-defined category weights (e.g., Competencies: ' . (self::COMPETENCY_WEIGHT_PERCENTAGE * 100) . '%, Location: ' . (self::LOCATION_WEIGHT_PERCENTAGE * 100) . '%).',
                'Step 4: Filter qualified talents based on meeting minimum proficiency for all required competencies (veto for critical misses).',
                'Step 5: Normalize talent performance scores for each competency (0-1 scale).',
                'Step 6: Calculate weighted sum for each talent based on normalized scores and combined weights.',
                'Step 7: Perform sensitivity analysis for ranking stability.'
            ]
        ];
    }

    /**
     * Calculate sensitivity and stability scores for a single talent
     */
    private function calculateSensitivityScoresForTalent(User $talent, Collection $competencies, Collection $allTalents): array
    {
        $originalScore = $this->calculateTalentScore($talent, $competencies);
        $scoreVariations = [];
        $rankVariations = [];

        // Calculate original ranking
        $allScores = $allTalents->map(function ($t) use ($competencies) {
            return [
                'talent_id' => $t->id,
                'score' => $this->calculateTalentScore($t, $competencies)
            ];
        })->sortByDesc('score');

        $originalRank = $allScores->search(function ($item) use ($talent) {
            return $item['talent_id'] === $talent->id;
        }) + 1;

        // Test weight variations for each competency
        foreach ($competencies as $competency) {
            $originalWeight = $competency->normalized_weight;
            $weightVariations = [-0.2, -0.1, 0.1, 0.2]; // ±20%, ±10% variations

            foreach ($weightVariations as $variation) {
                $modifiedWeight = max(0.01, min(1.0, $originalWeight + ($originalWeight * $variation)));

                // Create temporary competency collection with modified weight
                $modifiedCompetencies = $competencies->map(function ($comp) use ($competency, $modifiedWeight) {
                    if ($comp->id === $competency->id) {
                        $modified = clone $comp;
                        $modified->normalized_weight = $modifiedWeight;
                        return $modified;
                    }
                    return $comp;
                });

                // Recalculate score with modified weight
                $modifiedScore = $this->calculateTalentScore($talent, $modifiedCompetencies);
                if ($originalScore > 0) { // Add this check
                    $scoreVariations[] = abs($modifiedScore - $originalScore) / $originalScore;
                } else {
                    $scoreVariations[] = 0; // Or handle as appropriate, e.g., a large number if any change is significant
                }

                // Recalculate ranking with modified weights
                $modifiedAllScores = $allTalents->map(function ($t) use ($modifiedCompetencies) {
                    return [
                        'talent_id' => $t->id,
                        'score' => $this->calculateTalentScore($t, $modifiedCompetencies)
                    ];
                })->sortByDesc('score');

                $modifiedRank = $modifiedAllScores->search(function ($item) use ($talent) {
                    return $item['talent_id'] === $talent->id;
                }) + 1;

                $rankVariations[] = abs($modifiedRank - $originalRank);
            }
        }

        // Calculate sensitivity score (lower is more stable)
        $avgScoreVariation = collect($scoreVariations)->avg();
        $sensitivityScore = 1.0 - min($avgScoreVariation * 5, 1.0); // Scale to 0-1

        // Calculate stability score (lower rank variation means higher stability)
        $avgRankVariation = collect($rankVariations)->avg();
        $maxPossibleRankChange = $allTalents->count() - 1;
        $stabilityScore = $maxPossibleRankChange > 0 ? 1.0 - min($avgRankVariation / $maxPossibleRankChange, 1.0) : 1.0;

        return [
            'sensitivity_score' => round($sensitivityScore, 4),
            'stability_score' => round($stabilityScore, 4)
        ];
    }

    /**
     * Calculate basic talent score for sensitivity analysis
     */
    private function calculateTalentScore(User $talent, Collection $competencies): float
    {
        $score = 0;
        foreach ($competencies as $competency) {
            $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);
            if ($talentCompetency) {
                $score += $talentCompetency->pivot->proficiency_level * $competency->normalized_weight;
            }
        }
        return $score;
    }
}
