<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Competency;
use App\Models\TalentSearch;
use App\Models\TalentShortlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TalentController extends Controller
{
    /**
     * Display a listing of available talents with advanced MIS filtering capabilities.
     */
    public function index(Request $request)
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->with(['competencies', 'roles']);

        // Get all competencies grouped by category for advanced filtering
        $competencies = Competency::orderBy('category')->orderBy('name')->get();
        $competencyCategories = $competencies->groupBy('category');

        // Get unique provinces and cities for location filters
        $provinces = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->whereNotNull('domicile_country')
          ->distinct()
          ->pluck('domicile_country')
          ->sort()
          ->values();

        $cities = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->whereNotNull('domicile_city')
          ->distinct()
          ->pluck('domicile_city')
          ->sort()
          ->values();

        // MIS ADVANCED FILTERING - Football Manager Style

        // 1. Multi-competency filtering with required proficiency levels
        if ($request->filled('competencies') && is_array($request->competencies)) {
            foreach ($request->competencies as $competencyId => $minProficiency) {
                if (!empty($competencyId) && !empty($minProficiency)) {
                    $query->whereHas('competencies', function ($q) use ($competencyId, $minProficiency) {
                        $q->where('competencies.id', $competencyId)
                          ->where('competency_user.proficiency_level', '>=', $this->getProficiencyNumber($minProficiency));
                    });
                }
            }
        }

        // 2. Single competency filter (backward compatibility)
        if ($request->filled('competency_id')) {
            $query->whereHas('competencies', function ($q) use ($request) {
                $q->where('competencies.id', $request->competency_id);
            });
        }

        // 3. Minimum competency count filter
        if ($request->filled('min_competencies')) {
            $query->whereHas('competencies', function ($q) {}, '>=', (int)$request->min_competencies);
        }

        // 4. Advanced proficiency filtering
        if ($request->filled('min_proficiency')) {
            $proficiencyNum = $this->getProficiencyNumber($request->min_proficiency);
            $query->whereHas('competencies', function ($q) use ($proficiencyNum) {
                $q->where('competency_user.proficiency_level', '>=', $proficiencyNum);
            });
        }

        // 5. Expert-level talent filter
        if ($request->filled('experts_only') && $request->experts_only) {
            $query->whereHas('competencies', function ($q) {
                $q->where('competency_user.proficiency_level', '>=', 4); // Expert level
            });
        }

        // 6. Location filtering with multiple options
        if ($request->filled('province')) {
            $query->where('domicile_country', $request->province);
        }

        if ($request->filled('city')) {
            $query->where('domicile_city', $request->city);
        }

        // 7. Enhanced search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhereHas('competencies', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // 8. Sorting options
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        switch ($sortBy) {
            case 'competency_count':
                $query->withCount('competencies')->orderBy('competencies_count', $sortDirection);
                break;
            case 'name':
            default:
                $query->orderBy('name', $sortDirection);
                break;
        }

        // 9. Results per page
        $perPage = $request->get('per_page', 12);
        $perPage = in_array($perPage, [12, 24, 48]) ? $perPage : 12;

        $talents = $query->paginate($perPage);

        // Add statistics for MIS dashboard
        $totalTalents = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->count();

        $stats = [
            'total' => $totalTalents,
            'filtered' => $talents->total(),
            'competency_breakdown' => $this->getCompetencyBreakdown(),
            'location_breakdown' => $this->getLocationBreakdown()
        ];

        // Get saved searches for current user
        $savedSearches = Auth::check() ? TalentSearch::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        // Get shortlist items for current user
        $shortlistCount = Auth::check() ? TalentShortlist::where('user_id', Auth::id())->count() : 0;

        return view('user.talents.index', compact(
            'talents',
            'competencies',
            'competencyCategories',
            'provinces',
            'cities',
            'stats',
            'savedSearches',
            'shortlistCount'
        ));
    }

    /**
     * Convert proficiency level string to number for database queries
     */
    private function getProficiencyNumber($proficiency)
    {
        $levels = [
            'beginner' => 1,
            'intermediate' => 2,
            'advanced' => 3,
            'expert' => 4
        ];

        return $levels[$proficiency] ?? 1;
    }

    /**
     * Get competency breakdown for MIS analytics
     */
    private function getCompetencyBreakdown()
    {
        return Competency::withCount('users')->get()->map(function ($competency) {
            return [
                'name' => $competency->name,
                'category' => $competency->category,
                'count' => $competency->users_count
            ];
        });
    }

    /**
     * Get location breakdown for MIS analytics
     */
    private function getLocationBreakdown()
    {
        return User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->selectRaw('domicile_country, domicile_city, COUNT(*) as count')
          ->groupBy('domicile_country', 'domicile_city')
          ->get();
    }

    /**
     * Display the specified talent profile.
     */
    public function show(User $talent)
    {
        // Ensure the user is actually a talent
        if (!$talent->hasRole('talent')) {
            abort(404, 'Talent not found.');
        }

        // Load competencies for the talent
        $talent->load('competencies');

        return view('user.talents.show', compact('talent'));
    }    /**
     * Handle AJAX search requests for real-time talent discovery.
     */
    public function search(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->route('user.talents.index');
        }

        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->with(['competencies', 'roles']);

        // Apply the same advanced filtering logic as index method

        // Multi-competency filtering
        if ($request->filled('competencies') && is_array($request->competencies)) {
            foreach ($request->competencies as $competencyId => $minProficiency) {
                if (!empty($competencyId) && !empty($minProficiency)) {
                    $query->whereHas('competencies', function ($q) use ($competencyId, $minProficiency) {
                        $q->where('competencies.id', $competencyId)
                          ->where('competency_user.proficiency_level', '>=', $this->getProficiencyNumber($minProficiency));
                    });
                }
            }
        }

        // Single competency filter (backward compatibility)
        if ($request->filled('competency_id')) {
            $query->whereHas('competencies', function ($q) use ($request) {
                $q->where('competencies.id', $request->competency_id);
            });
        }

        // Minimum competency count filter
        if ($request->filled('min_competencies')) {
            $query->whereHas('competencies', function ($q) {}, '>=', (int)$request->min_competencies);
        }

        // Advanced proficiency filtering
        if ($request->filled('min_proficiency')) {
            $proficiencyNum = $this->getProficiencyNumber($request->min_proficiency);
            $query->whereHas('competencies', function ($q) use ($proficiencyNum) {
                $q->where('competency_user.proficiency_level', '>=', $proficiencyNum);
            });
        }

        // Expert-level talent filter
        if ($request->filled('experts_only') && $request->experts_only) {
            $query->whereHas('competencies', function ($q) {
                $q->where('competency_user.proficiency_level', '>=', 4);
            });
        }

        // Location filtering
        if ($request->filled('province')) {
            $query->where('domicile_country', $request->province);
        }

        if ($request->filled('city')) {
            $query->where('domicile_city', $request->city);
        }

        // Enhanced search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhereHas('competencies', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        switch ($sortBy) {
            case 'competency_count':
                $query->withCount('competencies')->orderBy('competencies_count', $sortDirection);
                break;
            case 'name':
            default:
                $query->orderBy('name', $sortDirection);
                break;
        }

        // Results per page
        $perPage = $request->get('per_page', 12);
        $perPage = in_array($perPage, [12, 24, 48]) ? $perPage : 12;

        $talents = $query->paginate($perPage);

        // Return partial view for AJAX requests
        $html = view('user.talents.partials.search-results', compact('talents'))->render();

        return response()->json([
            'html' => $html,
            'total' => $talents->total(),
            'current_page' => $talents->currentPage(),
            'last_page' => $talents->lastPage()
        ]);
    }

    /**
     * Save current search filters as a saved search
     */
    public function saveSearch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        // Get current search parameters
        $filters = $request->only([
            'search', 'competency_id', 'competencies', 'min_proficiency',
            'experts_only', 'province', 'city', 'min_competencies',
            'sort_by', 'sort_direction', 'per_page'
        ]);

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value) && $value !== null;
        });

        $search = TalentSearch::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'filters' => $filters
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Search saved successfully!',
            'search' => $search
        ]);
    }

    /**
     * Load a saved search
     */
    public function loadSearch(TalentSearch $search)
    {
        if ($search->user_id !== Auth::id()) {
            abort(403);
        }

        return redirect()->route('user.talents.index', $search->filters);
    }

    /**
     * Delete a saved search
     */
    public function deleteSearch(TalentSearch $search)
    {
        if ($search->user_id !== Auth::id()) {
            abort(403);
        }

        $search->delete();

        return response()->json([
            'success' => true,
            'message' => 'Search deleted successfully!'
        ]);
    }

    /**
     * Add talent to shortlist
     */
    public function addToShortlist(Request $request, User $talent)
    {
        if (!$talent->hasRole('talent')) {
            return response()->json(['error' => 'Invalid talent'], 404);
        }

        $request->validate([
            'list_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'nullable|integer|in:0,1,2'
        ]);

        $shortlist = TalentShortlist::updateOrCreate([
            'user_id' => Auth::id(),
            'talent_id' => $talent->id,
            'list_name' => $request->get('list_name', 'default')
        ], [
            'notes' => $request->notes,
            'priority' => $request->get('priority', 0)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Talent added to shortlist!',
            'shortlist' => $shortlist
        ]);
    }

    /**
     * Remove talent from shortlist
     */
    public function removeFromShortlist(Request $request, User $talent)
    {
        $listName = $request->get('list_name', 'default');

        TalentShortlist::where([
            'user_id' => Auth::id(),
            'talent_id' => $talent->id,
            'list_name' => $listName
        ])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Talent removed from shortlist!'
        ]);
    }

    /**
     * View shortlisted talents
     */
    public function shortlist(Request $request)
    {
        $listName = $request->get('list', 'default');

        $shortlists = TalentShortlist::where('user_id', Auth::id())
            ->where('list_name', $listName)
            ->with(['talent.competencies'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all list names for the user
        $listNames = TalentShortlist::where('user_id', Auth::id())
            ->distinct()
            ->pluck('list_name');

        return view('user.talents.shortlist', compact('shortlists', 'listNames', 'listName'));
    }

    /**
     * Compare multiple talents side by side
     */
    public function compare(Request $request)
    {
        $talentIds = $request->get('talents', []);

        if (is_string($talentIds)) {
            $talentIds = explode(',', $talentIds);
        }

        if (count($talentIds) < 2 || count($talentIds) > 4) {
            return redirect()->back()->with('error', 'Please select 2-4 talents to compare.');
        }

        $talents = User::whereIn('id', $talentIds)
                      ->whereHas('roles', function ($q) {
                          $q->where('name', 'talent');
                      })
                      ->with(['competencies'])
                      ->get();

        if ($talents->count() != count($talentIds)) {
            return redirect()->back()->with('error', 'Some selected talents were not found.');
        }

        // Get all competencies for comparison matrix
        $allCompetencies = Competency::whereIn('id',
            $talents->flatMap(function($talent) {
                return $talent->competencies->pluck('id');
            })->unique()
        )->get();

        return view('user.talents.compare', compact('talents', 'allCompetencies'));
    }

    /**
     * Get MIS analytics dashboard with enhanced Phase 2 features
     */
    public function analytics(Request $request)
    {
        // Handle AJAX requests for real-time updates
        if ($request->ajax()) {
            return response()->json($this->getAnalyticsData());
        }

        $stats = $this->getAnalyticsData();

        return view('user.talents.analytics', compact('stats'));
    }

    /**
     * Get comprehensive analytics data for Phase 2 MIS
     */
    private function getAnalyticsData()
    {
        $talentQuery = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        });

        $stats = [
            // Core Metrics
            'total_talents' => $talentQuery->count(),
            'active_searches' => TalentSearch::where('created_at', '>=', now()->subDays(30))->count(),
            'competency_distribution' => $this->getCompetencyBreakdown(),
            'location_distribution' => $this->getLocationBreakdown(),

            // Enhanced Proficiency Analytics
            'proficiency_breakdown' => User::whereHas('roles', function ($q) {
                $q->where('name', 'talent');
            })->join('competency_user', 'users.id', '=', 'competency_user.user_id')
              ->selectRaw('proficiency_level, COUNT(*) as count')
              ->groupBy('proficiency_level')
              ->orderBy('proficiency_level')
              ->get(),

            // Top Competencies with Trend Data
            'top_competencies' => Competency::withCount(['users' => function($query) {
                $query->whereHas('roles', function($q) {
                    $q->where('name', 'talent');
                });
            }])->orderBy('users_count', 'desc')->take(15)->get(),

            // Recent Activity
            'recent_searches' => Auth::check() ? TalentSearch::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get() : collect(),

            // NEW PHASE 2 ANALYTICS

            // Competency Gap Analysis
            'competency_gaps' => $this->getCompetencyGapAnalysis(),

            // Talent Pipeline Analytics
            'talent_pipeline' => $this->getTalentPipelineData(),

            // Geographic Distribution
            'geographic_insights' => $this->getGeographicInsights(),

            // Trend Analysis (last 6 months)
            'trend_data' => $this->getTrendAnalysis(),

            // Performance Metrics
            'performance_metrics' => $this->getPerformanceMetrics(),

            // Predictive Insights
            'predictions' => $this->getPredictiveInsights(),

            // Search Analytics
            'search_analytics' => $this->getSearchAnalytics(),

            // Competency Categories with Advanced Stats
            'category_insights' => $this->getCategoryInsights()
        ];

        return $stats;
    }

    /**
     * Get competency gap analysis for strategic planning
     */
    private function getCompetencyGapAnalysis()
    {
        // Analyze competencies that are in high demand but low supply
        $competencyDemand = Competency::withCount(['users' => function($query) {
            $query->whereHas('roles', function($q) {
                $q->where('name', 'talent');
            });
        }])->get()->map(function($competency) {
            $avgProficiency = $competency->users()
                ->whereHas('roles', function($q) {
                    $q->where('name', 'talent');
                })
                ->avg('competency_user.proficiency_level') ?: 0;

            return [
                'name' => $competency->name,
                'category' => $competency->category,
                'talent_count' => $competency->users_count,
                'avg_proficiency' => round($avgProficiency, 2),
                'gap_score' => $this->calculateGapScore($competency->users_count, $avgProficiency)
            ];
        })->sortByDesc('gap_score')->take(10);

        return $competencyDemand;
    }

    /**
     * Calculate competency gap score (higher = bigger gap)
     */
    private function calculateGapScore($talentCount, $avgProficiency)
    {
        // Formula: Lower talent count + lower proficiency = higher gap score
        $talentWeight = max(0, (50 - $talentCount) / 50); // Normalized inverse talent count
        $proficiencyWeight = max(0, (4 - $avgProficiency) / 4); // Normalized inverse proficiency

        return round(($talentWeight * 0.6 + $proficiencyWeight * 0.4) * 100, 2);
    }

    /**
     * Get talent pipeline data for recruitment planning
     */
    private function getTalentPipelineData()
    {
        $totalTalents = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->count();

        // Categorize talents by experience level (based on avg proficiency)
        $pipeline = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->with('competencies')->get()->map(function($talent) {
            $avgProficiency = $talent->competencies->avg('pivot.proficiency_level') ?: 0;

            if ($avgProficiency >= 3.5) return 'senior';
            if ($avgProficiency >= 2.5) return 'mid';
            return 'junior';
        })->countBy();

        return [
            'total' => $totalTalents,
            'junior' => $pipeline->get('junior', 0),
            'mid' => $pipeline->get('mid', 0),
            'senior' => $pipeline->get('senior', 0),
            'junior_percentage' => $totalTalents > 0 ? round(($pipeline->get('junior', 0) / $totalTalents) * 100, 1) : 0,
            'mid_percentage' => $totalTalents > 0 ? round(($pipeline->get('mid', 0) / $totalTalents) * 100, 1) : 0,
            'senior_percentage' => $totalTalents > 0 ? round(($pipeline->get('senior', 0) / $totalTalents) * 100, 1) : 0,
        ];
    }

    /**
     * Get geographic insights for talent distribution analysis
     */
    private function getGeographicInsights()
    {
        $totalTalents = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->count();

        $countryData = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->selectRaw('domicile_country, COUNT(*) as count')
          ->whereNotNull('domicile_country')
          ->groupBy('domicile_country')
          ->orderBy('count', 'desc')
          ->get();

        $remoteCapable = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->where('can_work_remote', true)->count();

        return [
            'total_countries' => $countryData->count(),
            'top_countries' => $countryData->take(8),
            'country_distribution' => $countryData->map(function($country) use ($totalTalents) {
                return [
                    'country' => $country->domicile_country,
                    'count' => $country->count,
                    'percentage' => $totalTalents > 0 ? round(($country->count / $totalTalents) * 100, 1) : 0
                ];
            }),
            'remote_capable' => $remoteCapable,
            'remote_percentage' => $totalTalents > 0 ? round(($remoteCapable / $totalTalents) * 100, 1) : 0
        ];
    }

    /**
     * Get trend analysis data for the last 6 months
     */
    private function getTrendAnalysis()
    {
        // For now, we'll simulate trend data. In a real scenario, this would track historical data
        $months = [];
        $talentGrowth = [];
        $searchActivity = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            // Simulate growth data (in reality, you'd track this in a separate table)
            $baseTalents = User::whereHas('roles', function ($q) {
                $q->where('name', 'talent');
            })->count();

            $talentGrowth[] = $baseTalents + random_int(-5, 15); // Simulate variance

            $searchActivity[] = TalentSearch::where('created_at', '>=', $date->startOfMonth())
                ->where('created_at', '<=', $date->endOfMonth())
                ->count();
        }

        return [
            'months' => $months,
            'talent_growth' => $talentGrowth,
            'search_activity' => $searchActivity,
            'growth_rate' => $this->calculateGrowthRate($talentGrowth)
        ];
    }

    /**
     * Calculate growth rate from data points
     */
    private function calculateGrowthRate($data)
    {
        if (count($data) < 2) return 0;

        $firstValue = $data[0];
        $lastValue = end($data);

        if ($firstValue == 0) return 0;

        return round((($lastValue - $firstValue) / $firstValue) * 100, 1);
    }

    /**
     * Get performance metrics for MIS dashboard
     */
    private function getPerformanceMetrics()
    {
        $totalTalents = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->count();

        // Fix: Calculate average competencies per talent using proper aggregation
        $avgCompetenciesPerTalent = 0;
        if ($totalTalents > 0) {
            // Get the total number of competency relationships
            $totalCompetencyRelations = \DB::table('competency_user')
                ->join('users', 'competency_user.user_id', '=', 'users.id')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('roles.name', 'talent')
                ->count();

            $avgCompetenciesPerTalent = round($totalCompetencyRelations / $totalTalents, 1);
        }

        $highPerformers = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->whereHas('competencies', function($q) {
            $q->where('competency_user.proficiency_level', '>=', 4);
        })->distinct()->count();

        return [
            'avg_competencies_per_talent' => $avgCompetenciesPerTalent,
            'high_performers_count' => $highPerformers,
            'high_performers_percentage' => $totalTalents > 0 ? round(($highPerformers / $totalTalents) * 100, 1) : 0,
            'system_efficiency' => $this->calculateSystemEfficiency(),
            'data_quality_score' => $this->calculateDataQualityScore()
        ];
    }

    /**
     * Calculate system efficiency metric
     */
    private function calculateSystemEfficiency()
    {
        $totalUsers = User::count();
        $totalSearches = TalentSearch::count();
        $totalTalents = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->count();

        // Simple efficiency formula based on usage patterns
        $efficiency = 0;
        if ($totalUsers > 0) {
            $efficiency += min(($totalSearches / $totalUsers) * 20, 40); // Search activity weight
        }
        if ($totalTalents > 0) {
            $efficiency += min(($totalTalents / max($totalUsers, 1)) * 60, 60); // Talent density weight
        }

        return round($efficiency, 1);
    }

    /**
     * Calculate data quality score
     */
    private function calculateDataQualityScore()
    {
        $totalTalents = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->count();

        if ($totalTalents == 0) return 0;

        $talentsWithLocation = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->whereNotNull('domicile_country')->whereNotNull('domicile_city')->count();

        $talentsWithCompetencies = User::whereHas('roles', function ($q) {
            $q->where('name', 'talent');
        })->has('competencies')->count();

        $locationScore = ($talentsWithLocation / $totalTalents) * 50;
        $competencyScore = ($talentsWithCompetencies / $totalTalents) * 50;

        return round($locationScore + $competencyScore, 1);
    }

    /**
     * Get predictive insights based on current data
     */
    private function getPredictiveInsights()
    {
        $insights = [];

        // Predict in-demand competencies
        $topCompetencies = Competency::withCount('users')->orderBy('users_count', 'desc')->take(5)->pluck('name');
        $insights[] = [
            'type' => 'demand_forecast',
            'title' => 'High Demand Competencies',
            'description' => 'Based on current talent distribution, these competencies show strong market presence.',
            'data' => $topCompetencies,
            'confidence' => 85
        ];

        // Predict talent gaps
        $gapAnalysis = $this->getCompetencyGapAnalysis();
        $topGaps = $gapAnalysis->take(3)->pluck('name');
        $insights[] = [
            'type' => 'gap_prediction',
            'title' => 'Potential Skill Gaps',
            'description' => 'These competencies may face shortage based on current supply vs. proficiency levels.',
            'data' => $topGaps,
            'confidence' => 78
        ];

        // Geographic expansion opportunities
        $geoInsights = $this->getGeographicInsights();
        $insights[] = [
            'type' => 'geographic_opportunity',
            'title' => 'Geographic Expansion',
            'description' => "Current talent spans {$geoInsights['total_countries']} countries with {$geoInsights['remote_percentage']}% remote-capable talents.",
            'data' => ['remote_percentage' => $geoInsights['remote_percentage']],
            'confidence' => 92
        ];

        return $insights;
    }

    /**
     * Get search analytics for user behavior insights
     */
    private function getSearchAnalytics()
    {
        $totalSearches = TalentSearch::count();
        $recentSearches = TalentSearch::where('created_at', '>=', now()->subDays(30))->count();

        $topSearchTerms = TalentSearch::selectRaw('filters, COUNT(*) as count')
            ->groupBy('filters')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->map(function($search) {
                $filters = json_decode($search->filters, true);
                return [
                    'term' => $search->name ?? 'Unknown',
                    'count' => $search->count,
                    'filters' => $filters
                ];
            });

        return [
            'total_searches' => $totalSearches,
            'recent_searches' => $recentSearches,
            'search_trend' => $recentSearches > 0 ? 'increasing' : 'stable',
            'top_search_terms' => $topSearchTerms,
            'avg_searches_per_user' => User::count() > 0 ? round($totalSearches / User::count(), 1) : 0
        ];
    }

    /**
     * Get advanced category insights
     */
    private function getCategoryInsights()
    {
        $competencies = Competency::with('users')->get();
        $categories = $competencies->groupBy('category');

        return $categories->map(function($categoryCompetencies, $category) {
            $totalTalents = $categoryCompetencies->sum(function($comp) {
                return $comp->users->count();
            });

            $avgProficiency = $categoryCompetencies->avg(function($comp) {
                return $comp->users->avg('pivot.proficiency_level') ?: 0;
            });

            return [
                'name' => $category ?: 'Uncategorized',
                'competency_count' => $categoryCompetencies->count(),
                'total_talents' => $totalTalents,
                'avg_proficiency' => round($avgProficiency, 2),
                'competencies' => $categoryCompetencies->map(function($comp) {
                    return [
                        'name' => $comp->name,
                        'talent_count' => $comp->users->count(),
                        'avg_proficiency' => round($comp->users->avg('pivot.proficiency_level') ?: 0, 2)
                    ];
                })->sortByDesc('talent_count')->values()
            ];
        })->sortByDesc('total_talents')->values();
    }
}
