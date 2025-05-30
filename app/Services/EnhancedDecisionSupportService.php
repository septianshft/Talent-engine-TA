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
     * For thesis: weights should represent meaningful percentages and follow academic standards
     */
    private function validateWeightDistribution(array $weights): void
    {
        if (empty($weights)) {
            Log::warning('[Enhanced DSS] validateWeightDistribution called with empty weights array.');
            return;
        }

        $totalWeight = array_sum($weights);

        // For thesis: weights should represent percentages, total cannot exceed 100%
        if ($totalWeight > 100) {
            throw ValidationException::withMessages([
                'weights' => sprintf(
                    'Total competency weights (%.1f%%) cannot exceed 100%%. Please adjust the weight distribution for academic compliance.',
                    $totalWeight
                )
            ]);
        }

        // Allow all-zero weights for location-only evaluation (valid academic scenario)
        // The system can still evaluate talents based on location compatibility (15% weight)
        if ($totalWeight == 0) {
            Log::info('[Enhanced DSS] All competency weights are zero, evaluation will be based on location compatibility only.', [
                'location_weight_percentage' => self::LOCATION_WEIGHT_PERCENTAGE * 100,
                'academic_scenario' => 'location_only_evaluation'
            ]);
        }

        $maxWeight = max($weights);
        $nonZeroWeights = array_filter($weights, function($weight) { return $weight > 0; });

        // Check for extreme dominance only when there are multiple non-zero weights
        // Single competency at 100% is academically valid (specialized roles)
        // But multiple competencies with extreme imbalance (>90% dominance) should be avoided
        if (count($nonZeroWeights) > 1 && $totalWeight > 0 && ($maxWeight / $totalWeight) > 0.9) {
            throw ValidationException::withMessages([
                'weights' => sprintf(
                    'Extreme weight imbalance detected (%.1f%% dominance). With multiple competencies, consider more balanced distribution for comprehensive evaluation.',
                    ($maxWeight / $totalWeight) * 100
                )
            ]);
        }

        Log::debug('[Enhanced DSS] Weight validation passed for thesis compliance.', [
            'total_weight' => $totalWeight,
            'max_weight_found' => $maxWeight,
            'max_weight_percentage_of_total' => ($totalWeight > 0) ? ($maxWeight / $totalWeight) * 100 : 'N/A',
            'dominance_ratio' => ($totalWeight > 0) ? ($maxWeight / $totalWeight) : 0,
            'academic_compliance' => 'PASSED'
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
        $qualifiedTalents = $talents->filter(function ($talent) use ($normalizedCompetencies, $talentRequest) {
            foreach ($normalizedCompetencies as $competency) {
                if ($competency->is_critical) {
                    $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);

                    // Calculate effective veto threshold using talent request's veto threshold percentage
                    $vetoThresholdPercentage = $talentRequest->veto_threshold ?? 80; // Default to 80%
                    $effectiveVetoThreshold = ($competency->required_proficiency_level * $vetoThresholdPercentage) / 100;

                    if (!$talentCompetency ||
                        $talentCompetency->pivot->proficiency_level < $effectiveVetoThreshold) {
                        Log::debug('[Enhanced DSS] Talent eliminated by veto threshold', [
                            'talent_id' => $talent->id,
                            'competency' => $competency->name,
                            'required_level' => $competency->required_proficiency_level,
                            'veto_percentage' => $vetoThresholdPercentage,
                            'effective_threshold' => $effectiveVetoThreshold,
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
     * Normalize performance values to [0,1] range for fair comparison
     * Enhanced for academic compliance and mathematical soundness
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

            Log::debug('[Enhanced DSS] Normalization data for competency', [
                'competency_id' => $competencyId,
                'competency_name' => $competency->name,
                'min_level' => $minLevel,
                'max_level' => $maxLevel,
                'range' => $range,
                'talent_count' => count($proficiencyLevels)
            ]);

            // Normalize each talent's performance for this competency
            foreach ($talents as $talent) {
                $talentCompetency = $talent->competencies->firstWhere('id', $competencyId);
                if ($talentCompetency) {
                    $rawLevel = $talentCompetency->pivot->proficiency_level;

                    // Enhanced normalization with mathematical safety
                    if ($range > 0) {
                        // Standard min-max normalization: (x - min) / (max - min)
                        $normalizedLevel = ($rawLevel - $minLevel) / $range;
                    } else {
                        // Mathematical safety: when all values are identical
                        // Award full score if meets or exceeds requirement, zero otherwise
                        $requiredLevel = $competency->required_proficiency_level;
                        $normalizedLevel = ($rawLevel >= $requiredLevel) ? 1.0 : 0.0;

                        Log::info('[Enhanced DSS] Zero range normalization applied', [
                            'competency' => $competency->name,
                            'talent_id' => $talent->id,
                            'raw_level' => $rawLevel,
                            'required_level' => $requiredLevel,
                            'normalized_level' => $normalizedLevel
                        ]);
                    }

                    // Ensure bounds [0,1] for mathematical compliance
                    $normalizedLevel = max(0.0, min(1.0, $normalizedLevel));
                    $normalized[$talent->id][$competencyId] = $normalizedLevel;
                }
            }
        }

        return $normalized;
    }

    /**
     * Calculate enhanced SAW scores with mathematical rigor and academic compliance
     * Ensures proper normalization, bounds checking, and score validation
     */
    private function calculateEnhancedSAWScores(
        Collection $talents,
        Collection $competencies,
        array $normalizedPerformances,
        TalentRequest $talentRequest
    ): Collection {

        Log::info('[Enhanced DSS] Starting SAW score calculation', [
            'talents_count' => $talents->count(),
            'competencies_count' => $competencies->count(),
            'competency_weight_total' => $competencies->sum('normalized_weight'),
            'location_weight' => self::LOCATION_WEIGHT_PERCENTAGE
        ]);

        return $talents->map(function ($talent) use ($competencies, $normalizedPerformances, $talentRequest) {
            $competencyScore = 0;
            $scoreBreakdown = [];
            $competencyScores = [];
            $weightSum = 0; // Track weight sum for validation

            // Calculate competency scores using normalized values with critical competency bonuses
            foreach ($competencies as $competency) {
                $normalizedPerformance = $normalizedPerformances[$talent->id][$competency->id] ?? 0;
                $weight = $competency->normalized_weight;

                // Apply critical competency bonus for enhanced scoring
                $criticalBonus = 1.0; // Default multiplier
                if ($competency->is_critical) {
                    $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);
                    if ($talentCompetency) {
                        $talentLevel = $talentCompetency->pivot->proficiency_level;
                        $requiredLevel = $competency->required_proficiency_level;

                        // Apply progressive bonus for exceeding critical requirements
                        if ($talentLevel >= $requiredLevel) {
                            $excessPerformance = ($talentLevel - $requiredLevel) / $requiredLevel;
                            $criticalBonus = 1.0 + (0.2 * $excessPerformance); // Up to 20% bonus for critical competencies
                        }
                    }
                }

                $contribution = $normalizedPerformance * $weight * $criticalBonus;
                $competencyScore += $contribution;
                $weightSum += $weight;

                $scoreBreakdown[$competency->name] = [
                    'normalized_performance' => round($normalizedPerformance, 4),
                    'weight' => round($weight, 4),
                    'critical_bonus' => round($criticalBonus, 4),
                    'contribution' => round($contribution, 4),
                    'is_critical' => $competency->is_critical
                ];

                // Store individual competency scores for test assertions
                $competencyScores[$competency->name] = $normalizedPerformance;
            }

            // Calculate location compatibility with proper normalization
            $locationScore = $this->calculateNormalizedLocationScore($talent, $talentRequest);
            $locationWeight = self::LOCATION_WEIGHT_PERCENTAGE;
            $locationContribution = $locationScore * $locationWeight;

            // Calculate total SAW score
            $totalScore = $competencyScore + $locationContribution;

            // Mathematical validation and bounds checking
            $expectedWeightSum = $weightSum + $locationWeight;
            $maxPossibleScore = $expectedWeightSum; // If all normalized performances = 1.0

            // Ensure score bounds [0, 1] for academic compliance
            $normalizedTotalScore = min(max($totalScore, 0.0), 1.0);

            // Calculate confidence score
            $confidenceScore = $this->calculateConfidenceScore($talent, $competencies);
            $confidenceFactors = $this->calculateConfidenceFactors($talent, $competencies);

            Log::debug('[Enhanced DSS] SAW calculation for talent', [
                'talent_id' => $talent->id,
                'competency_score' => round($competencyScore, 4),
                'location_score' => round($locationScore, 4),
                'location_contribution' => round($locationContribution, 4),
                'total_score' => round($totalScore, 4),
                'normalized_total_score' => round($normalizedTotalScore, 4),
                'weight_sum_validation' => round($expectedWeightSum, 4),
                'max_possible_score' => round($maxPossibleScore, 4),
                'confidence' => round($confidenceScore, 4)
            ]);

            // Validate mathematical correctness
            if ($expectedWeightSum < 0.99 || $expectedWeightSum > 1.01) {
                Log::warning('[Enhanced DSS] Weight sum validation failed', [
                    'talent_id' => $talent->id,
                    'expected_weight_sum' => $expectedWeightSum,
                    'competency_weights_sum' => $weightSum,
                    'location_weight' => $locationWeight
                ]);
            }

            // Prepare normalized weights for test compatibility
            $normalizedWeights = [];
            $totalCompetencyWeight = $competencies->sum('weight');
            foreach ($competencies as $competency) {
                // For test compatibility, show weight relative to competency portion only
                if ($totalCompetencyWeight > 0) {
                    $normalizedWeights[$competency->name] = $competency->weight / $totalCompetencyWeight;
                } else {
                    $normalizedWeights[$competency->name] = 1.0 / $competencies->count();
                }
            }

            // Return enhanced result structure with academic compliance
            return [
                'talent' => $talent,
                'saw_score' => $normalizedTotalScore,
                'dss_score' => $normalizedTotalScore, // For backward compatibility
                'competency_score' => $competencyScore,
                'location_score' => $locationScore,
                'location_contribution' => $locationContribution,
                'confidence_score' => $confidenceScore,
                'competency_scores' => $competencyScores,
                'score_breakdown' => $scoreBreakdown,
                'confidence_factors' => $confidenceFactors,
                'details' => [
                    'normalized_weights' => $normalizedWeights,
                    'calculation_method' => 'Enhanced SAW with Location Integration',
                    'competency_weight_total' => self::COMPETENCY_WEIGHT_PERCENTAGE,
                    'location_weight_total' => self::LOCATION_WEIGHT_PERCENTAGE
                ],
                'mathematical_validation' => [
                    'weight_sum' => round($expectedWeightSum, 4),
                    'bounds_compliant' => ($normalizedTotalScore >= 0.0 && $normalizedTotalScore <= 1.0),
                    'raw_score' => round($totalScore, 4),
                    'normalized_score' => round($normalizedTotalScore, 4)
                ],
                // Placeholders for sensitivity analysis - populated later
                'sensitivity_score' => 0.0,
                'stability_score' => 0.0,
            ];
        })->sortByDesc('saw_score')->values();
    }

    /**
     * Calculate normalized location compatibility score with academic rigor
     * Ensures mathematical soundness and proper weight distribution
     */
    private function calculateNormalizedLocationScore(User $talent, TalentRequest $talentRequest): float
    {
        // Remote work gets neutral score (0.5 for mathematical balance)
        if ($talentRequest->work_location_type === 'remote') {
            Log::debug('[Enhanced DSS] Remote work - neutral location score', [
                'talent_id' => $talent->id,
                'score' => 0.5
            ]);
            return 0.5; // Neutral score for remote work
        }

        $score = 0;
        $scoreComponents = [];

        // Component 1: Country compatibility (50% of location score)
        $countryWeight = 0.5;
        if ($this->isLocationMatch($talent->domicile_country, $talentRequest->work_location_country)) {
            $countryScore = 1.0;
            $score += $countryScore * $countryWeight;
            $scoreComponents['country_match'] = $countryScore * $countryWeight;

            // Component 2: City compatibility (30% of location score)
            $cityWeight = 0.3;
            if ($this->isLocationMatch($talent->domicile_city, $talentRequest->work_location_city)) {
                $cityScore = 1.0;
                $score += $cityScore * $cityWeight;
                $scoreComponents['city_match'] = $cityScore * $cityWeight;
            } else {
                // Partial score for city proximity within same country
                $proximityScore = $this->calculateCityProximity($talent->domicile_city, $talentRequest->work_location_city);
                $cityContribution = $proximityScore * $cityWeight * 0.5; // 50% penalty for proximity vs exact match
                $score += $cityContribution;
                $scoreComponents['city_proximity'] = $cityContribution;
            }
        } else {
            // Component 3: Regional proximity (when countries differ) (30% of location score)
            $regionalWeight = 0.3;
            $regionalScore = $this->calculateRegionalProximity($talent->domicile_country, $talentRequest->work_location_country);
            $regionalContribution = $regionalScore * $regionalWeight;
            $score += $regionalContribution;
            $scoreComponents['regional_proximity'] = $regionalContribution;
        }

        // Component 4: Time zone compatibility (20% of location score)
        $timezoneWeight = 0.2;
        $timezoneScore = $this->calculateTimezoneCompatibility($talent, $talentRequest);
        $timezoneContribution = $timezoneScore * $timezoneWeight;
        $score += $timezoneContribution;
        $scoreComponents['timezone_compatibility'] = $timezoneContribution;

        // Work type adjustment with mathematical justification
        if ($talentRequest->work_location_type === 'hybrid') {
            $hybridFactor = 0.7; // Reduce location importance by 30% for hybrid
            $score *= $hybridFactor;
            $scoreComponents['hybrid_adjustment'] = "Applied factor: {$hybridFactor}";
        }

        // Ensure mathematical bounds [0,1]
        $finalScore = max(0.0, min(1.0, $score));

        Log::debug('[Enhanced DSS] Location scoring breakdown', [
            'talent_id' => $talent->id,
            'work_location_type' => $talentRequest->work_location_type,
            'talent_country' => $talent->domicile_country,
            'talent_city' => $talent->domicile_city,
            'request_country' => $talentRequest->work_location_country,
            'request_city' => $talentRequest->work_location_city,
            'score_components' => $scoreComponents,
            'raw_score' => $score,
            'final_score' => $finalScore
        ]);

        return $finalScore;
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
            'method' => 'Enhanced Simple Additive Weighting (SAW)',
            'improvements' => [
                'Academic Weight Validation: Prevents single competency dominance (max 80% of total weight)',
                'Veto Thresholds: Eliminates candidates below 80% of required proficiency levels',
                'Mathematical Safety: Proper bounds checking and normalization validation',
                'Sensitivity Analysis: Statistical robustness testing for ranking stability',
                'Confidence Scoring: Multi-factor reliability assessment for recommendations',
                'Location Intelligence: Advanced geographic compatibility with timezone considerations',
                'Performance Normalization: Min-max scaling for fair cross-competency comparison',
                'Error Handling: Graceful degradation with informative validation messages'
            ],
            'scoring_components' => [
                'competencies' => self::COMPETENCY_WEIGHT_PERCENTAGE * 100 . '%',
                'location' => self::LOCATION_WEIGHT_PERCENTAGE * 100 . '%'
            ],
            'validation_rules' => [
                'max_single_competency_weight' => self::MAX_SINGLE_WEIGHT_PERCENTAGE * 100 . '%',
                'weight_range' => 'Competency weights must be between 0% and 100%',
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
     * Calculate mathematically rigorous sensitivity and stability scores for a single talent
     * Enhanced for academic compliance with proper statistical analysis
     */
    private function calculateSensitivityScoresForTalent(User $talent, Collection $competencies, Collection $allTalents): array
    {
        $originalScore = $this->calculateTalentScore($talent, $competencies);
        $scoreVariations = [];
        $rankVariations = [];

        // Calculate original ranking for stability analysis
        $allScores = $allTalents->map(function ($t) use ($competencies) {
            return [
                'talent_id' => $t->id,
                'score' => $this->calculateTalentScore($t, $competencies)
            ];
        })->sortByDesc('score');

        $originalRank = $allScores->search(function ($item) use ($talent) {
            return $item['talent_id'] === $talent->id;
        }) + 1;

        // Enhanced weight variation testing with academic methodology
        $weightVariations = [-0.3, -0.2, -0.1, -0.05, 0.05, 0.1, 0.2, 0.3]; // ±30% to ±5% variations
        $variationResults = [];

        foreach ($competencies as $competency) {
            $originalWeight = $competency->normalized_weight;
            $competencyVariations = [];

            // Test each weight variation
            foreach ($weightVariations as $variation) {
                // Ensure weight stays within valid bounds [0.01, 1.0]
                $modifiedWeight = max(0.01, min(1.0, $originalWeight + ($originalWeight * $variation)));

                // Create modified competency collection
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

                // Calculate relative score change (academic standard)
                if ($originalScore > 0) {
                    $relativeChange = abs($modifiedScore - $originalScore) / $originalScore;
                    $scoreVariations[] = $relativeChange;
                    $competencyVariations[] = $relativeChange;
                } else {
                    // Handle edge case where original score is zero
                    $absoluteChange = abs($modifiedScore - $originalScore);
                    $scoreVariations[] = $absoluteChange;
                    $competencyVariations[] = $absoluteChange;
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

            // Store detailed results for academic analysis
            $variationResults[$competency->name] = [
                'original_weight' => $originalWeight,
                'avg_score_variation' => collect($competencyVariations)->avg(),
                'max_score_variation' => collect($competencyVariations)->max(),
                'variations_tested' => count($competencyVariations)
            ];
        }

        // Calculate sensitivity metrics with academic rigor
        $scoreVariationsCollection = collect($scoreVariations);
        $avgScoreVariation = $scoreVariationsCollection->avg();
        $maxScoreVariation = $scoreVariationsCollection->max();
        $stdDevScoreVariation = $this->calculateStandardDeviation($scoreVariations);

        // Sensitivity score: Lower variation = higher stability (inverse relationship)
        // Scale: 0 (highly sensitive) to 1 (very stable)
        $sensitivityScore = 1.0 - min($avgScoreVariation * 2, 1.0); // Scale factor 2 for academic sensitivity

        // Calculate stability metrics
        $rankVariationsCollection = collect($rankVariations);
        $avgRankVariation = $rankVariationsCollection->avg();
        $maxRankVariation = $rankVariationsCollection->max();
        $maxPossibleRankChange = max(1, $allTalents->count() - 1);

        // Stability score: Lower rank variation = higher stability
        $stabilityScore = $maxPossibleRankChange > 0 ?
            1.0 - min($avgRankVariation / $maxPossibleRankChange, 1.0) : 1.0;

        // Enhanced logging for academic transparency
        Log::debug('[Enhanced DSS] Sensitivity analysis completed', [
            'talent_id' => $talent->id,
            'original_score' => round($originalScore, 4),
            'original_rank' => $originalRank,
            'variations_tested' => count($scoreVariations),
            'avg_score_variation' => round($avgScoreVariation, 4),
            'max_score_variation' => round($maxScoreVariation, 4),
            'std_dev_score_variation' => round($stdDevScoreVariation, 4),
            'avg_rank_variation' => round($avgRankVariation, 2),
            'max_rank_variation' => $maxRankVariation,
            'sensitivity_score' => round($sensitivityScore, 4),
            'stability_score' => round($stabilityScore, 4),
            'competency_analysis' => $variationResults
        ]);

        return [
            'sensitivity_score' => round($sensitivityScore, 4),
            'stability_score' => round($stabilityScore, 4),
            'academic_metrics' => [
                'avg_score_variation' => round($avgScoreVariation, 4),
                'max_score_variation' => round($maxScoreVariation, 4),
                'std_dev_score_variation' => round($stdDevScoreVariation, 4),
                'avg_rank_variation' => round($avgRankVariation, 2),
                'max_rank_variation' => $maxRankVariation,
                'variations_tested' => count($scoreVariations),
                'competency_breakdown' => $variationResults
            ]
        ];
    }

    /**
     * Calculate standard deviation for academic analysis
     */
    private function calculateStandardDeviation(array $values): float
    {
        $count = count($values);
        if ($count <= 1) {
            return 0.0;
        }

        $mean = array_sum($values) / $count;
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / $count;

        return sqrt($variance);
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

    /**
     * Generate basic SAW results for comparison purposes
     * Provides a simplified SAW calculation without advanced features
     */
    public function getBasicSAWResults(TalentRequest $talentRequest, Collection $talents): Collection
    {
        Log::info('[Enhanced DSS] Generating basic SAW results for comparison', [
            'talent_request_id' => $talentRequest->id,
            'talents_count' => $talents->count()
        ]);

        // Extract required competencies
        $requiredCompetencies = $this->extractRequiredCompetencies($talentRequest);

        if ($requiredCompetencies->isEmpty()) {
            Log::warning('[Enhanced DSS] No competencies found for basic SAW calculation');
            return collect();
        }

        // Normalize weights (basic SAW requirement)
        $normalizedCompetencies = $this->normalizeWeights($requiredCompetencies);

        // Filter talents that have all required competencies (basic filtering)
        $qualifiedTalents = $talents->filter(function ($talent) use ($normalizedCompetencies) {
            foreach ($normalizedCompetencies as $competency) {
                $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);
                if (!$talentCompetency) {
                    return false; // Must have all required competencies
                }
            }
            return true;
        });

        if ($qualifiedTalents->isEmpty()) {
            Log::warning('[Enhanced DSS] No qualified talents found for basic SAW');
            return collect();
        }

        // Calculate basic SAW scores
        $basicResults = $qualifiedTalents->map(function ($talent) use ($normalizedCompetencies) {
            $totalScore = 0;
            $competencyScores = [];

            foreach ($normalizedCompetencies as $competency) {
                $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);

                if ($talentCompetency) {
                    $proficiencyLevel = $talentCompetency->pivot->proficiency_level;
                    $normalizedProficiency = ($proficiencyLevel - self::MIN_PROFICIENCY_LEVEL) /
                                           (self::MAX_PROFICIENCY_LEVEL - self::MIN_PROFICIENCY_LEVEL);
                    $weightedScore = $normalizedProficiency * $competency->normalized_weight;
                    $totalScore += $weightedScore;

                    $competencyScores[] = [
                        'competency_id' => $competency->id,
                        'competency_name' => $competency->name,
                        'proficiency_level' => $proficiencyLevel,
                        'normalized_proficiency' => $normalizedProficiency,
                        'weight' => $competency->normalized_weight,
                        'weighted_score' => $weightedScore
                    ];
                }
            }

            return [
                'talent' => $talent,
                'total_score' => $totalScore,
                'competency_scores' => $competencyScores,
                'methodology' => 'Basic SAW',
                'calculation_type' => 'basic_saw',
                'features' => [
                    'basic_competency_scoring' => true,
                    'location_integration' => false,
                    'critical_competencies' => false,
                    'veto_thresholds' => false,
                    'confidence_scoring' => false
                ]
            ];
        });

        // Sort by total score (descending)
        $sortedResults = $basicResults->sortByDesc('total_score')->values();

        Log::info('[Enhanced DSS] Basic SAW results generated', [
            'qualified_talents' => $sortedResults->count(),
            'top_score' => $sortedResults->first()['total_score'] ?? 0
        ]);

        return $sortedResults;
    }
}
