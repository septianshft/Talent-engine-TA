<x-layouts.app>
<!-- Phase 2 Enhanced Analytics Dashboard - Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="htt    </div>
    @endif

        <!-- Expert Level Talents -->t/npm/chartjs-adapter-date-fns"></script>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Analytics Header with Real-time Status -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                MIS Analytics Dashboard
                <span id="realtime-indicator" class="ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                    <span class="animate-pulse w-2 h-2 bg-green-500 rounded-full mr-1.5"></span>
                    Live
                </span>
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Enhanced Phase 2 analytics with predictive insights and real-time data
                <span class="text-sm text-gray-500">• Last updated: <span id="last-updated">{{ now()->format('H:i:s') }}</span></span>
            </p>
        </div>
        <div class="flex gap-4 mt-4 lg:mt-0">
            <button onclick="refreshDashboard()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
            <a href="{{ route('user.talents.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to MIS
            </a>
            <button onclick="exportAnalytics()"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Report
            </button>
        </div>
    </div>    <!-- Enhanced Phase 2 Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Talents with Growth Indicator -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Total Talents</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_talents']) }}</p>
                    @if(isset($stats['trend_data']['growth_rate']))
                        <p class="text-blue-200 text-sm mt-1">
                            @if($stats['trend_data']['growth_rate'] > 0)
                                <span class="text-green-300">↗ +{{ $stats['trend_data']['growth_rate'] }}%</span> growth
                            @elseif($stats['trend_data']['growth_rate'] < 0)
                                <span class="text-red-300">↘ {{ $stats['trend_data']['growth_rate'] }}%</span> decline
                            @else
                                <span class="text-blue-200">→ Stable</span>
                            @endif
                        </p>
                    @endif
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Searches with Search Analytics -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Active Searches</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['active_searches']) }}</p>
                    @if(isset($stats['search_analytics']['search_trend']))
                        <p class="text-green-200 text-sm mt-1">
                            @if($stats['search_analytics']['search_trend'] === 'increasing')
                                <span class="text-green-200">📈 Increasing</span>
                            @else
                                <span class="text-green-200">📊 {{ ucfirst($stats['search_analytics']['search_trend']) }}</span>
                            @endif
                        </p>
                    @endif
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Performance Score -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium uppercase tracking-wide">System Efficiency</p>
                    <p class="text-3xl font-bold">{{ $stats['performance_metrics']['system_efficiency'] ?? 0 }}%</p>
                    <p class="text-purple-200 text-sm mt-1">Data Quality: {{ $stats['performance_metrics']['data_quality_score'] ?? 0 }}%</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- High Performers -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Expert Talents</p>
                    <p class="text-3xl font-bold">{{ $stats['performance_metrics']['high_performers_count'] ?? 0 }}</p>
                    <p class="text-orange-200 text-sm mt-1">{{ $stats['performance_metrics']['high_performers_percentage'] ?? 0 }}% of total</p>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 2 Enhanced Analytics Sections -->

    <!-- Predictive Insights Section -->
    @if(isset($stats['predictions']) && !empty($stats['predictions']))
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 mb-8 text-white">
        <h3 class="text-2xl font-bold mb-4 flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Predictive Insights & Strategic Recommendations
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($stats['predictions'] as $prediction)
            <div class="bg-white bg-opacity-20 backdrop-blur rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold">{{ $prediction['title'] }}</h4>
                    <span class="text-xs bg-white bg-opacity-30 px-2 py-1 rounded-full">
                        {{ $prediction['confidence'] }}% confidence
                    </span>
                </div>
                <p class="text-sm opacity-90 mb-3">{{ $prediction['description'] }}</p>
                @if(isset($prediction['data']) && is_array($prediction['data']))
                    <div class="text-xs space-y-1">
                        @foreach($prediction['data'] as $item)
                            <div class="bg-white bg-opacity-10 px-2 py-1 rounded">{{ $item }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
                <div>
                    <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Top Competency</p>
                    @if($stats['top_competencies']->isNotEmpty())
                        <p class="text-2xl font-bold">{{ $stats['top_competencies']->first()->name }}</p>
                        <p class="text-green-100 text-sm">{{ $stats['top_competencies']->first()->users_count }} talents</p>
                    @else
                        <p class="text-2xl font-bold">N/A</p>
                    @endif
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Expert Level Talents -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium uppercase tracking-wide">Expert Level</p>
                    @php
                        $expertCount = $stats['proficiency_breakdown']->where('proficiency_level', 4)->sum('count');
                    @endphp
                    <p class="text-3xl font-bold">{{ number_format($expertCount) }}</p>
                    <p class="text-purple-100 text-sm">Competencies</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recent Searches -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Your Searches</p>
                    <p class="text-3xl font-bold">{{ $stats['recent_searches']->count() }}</p>
                    <p class="text-orange-100 text-sm">Saved</p>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>    <!-- Interactive Charts Section - Phase 2 Enhancement -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Talent Pipeline Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Talent Pipeline Analysis</h3>
                <div class="flex gap-2">
                    <button onclick="togglePipelineView('monthly')" id="btn-monthly" class="px-3 py-1 text-xs bg-blue-600 text-white rounded">Monthly</button>
                    <button onclick="togglePipelineView('quarterly')" id="btn-quarterly" class="px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded">Quarterly</button>
                </div>
            </div>
            <canvas id="talentPipelineChart" width="400" height="200"></canvas>
        </div>

        <!-- Competency Gap Analysis -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Competency Gap Analysis</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">Top skill gaps</div>
            </div>
            <canvas id="competencyGapChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Geographic & Proficiency Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Geographic Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Geographic Distribution</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">Talent locations</div>
            </div>
            <canvas id="geographicChart" width="400" height="200"></canvas>
        </div>

        <!-- Proficiency Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Proficiency Distribution</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">Skill levels</div>
            </div>
            <canvas id="proficiencyChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Trend Analysis Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Talent Growth Trends</h3>
            <div class="flex gap-2">
                <button onclick="toggleTrendView('6m')" id="trend-6m" class="px-3 py-1 text-xs bg-blue-600 text-white rounded">6 Months</button>
                <button onclick="toggleTrendView('1y')" id="trend-1y" class="px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded">1 Year</button>
                <button onclick="toggleTrendView('2y')" id="trend-2y" class="px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded">2 Years</button>
            </div>
        </div>
        <canvas id="trendAnalysisChart" width="800" height="300"></canvas>
    </div>

    <!-- Location Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Location Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Location Distribution</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">Top locations</div>
            </div>
            <div class="space-y-4">
                @foreach($stats['location_distribution']->sortByDesc('count')->take(10) as $location)
                @php
                    $percentage = $stats['total_talents'] > 0 ? ($location->count / $stats['total_talents']) * 100 : 0;
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $location->domicile_city }}, {{ $location->domicile_country }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $location->count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Search Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Your Recent Searches</h3>
                <a href="{{ route('user.talents.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">View All</a>
            </div>
            @if($stats['recent_searches']->isNotEmpty())
                <div class="space-y-4">
                    @foreach($stats['recent_searches'] as $search)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $search->name }}</h4>
                            @if($search->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $search->description }}</p>
                            @endif
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                {{ $search->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('user.talents.load-search', $search) }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No saved searches yet</p>
                    <a href="{{ route('user.talents.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                        Start exploring talents
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Competency Category Breakdown -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Competency Categories</h3>
            <div class="text-sm text-gray-500 dark:text-gray-400">Distribution by category</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($stats['competency_distribution']->groupBy('category') as $category => $competencies)
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">{{ $category ?: 'Uncategorized' }}</h4>
                <div class="space-y-2">
                    @foreach($competencies->sortByDesc('count')->take(5) as $competency)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $competency['name'] }}</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $competency['count'] }}</span>
                    </div>
                    @endforeach
                    @if($competencies->count() > 5)
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        +{{ $competencies->count() - 5 }} more
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Advanced Search and Filter Panel -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                Advanced Analytics Filters
            </h3>
            <button onclick="toggleFilters()" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                <span id="filter-toggle-text">Show Filters</span>
            </button>
        </div>

        <div id="analytics-filters" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Range</label>
                <select id="timeRange" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="30d">Last 30 Days</option>
                    <option value="90d">Last 3 Months</option>
                    <option value="1y" selected>Last Year</option>
                    <option value="all">All Time</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Competency Category</label>
                <select id="categoryFilter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All Categories</option>
                    @foreach($stats['competency_distribution']->groupBy('category') as $category => $competencies)
                        <option value="{{ $category }}">{{ $category ?: 'Uncategorized' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Proficiency</label>
                <select id="proficiencyFilter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All Levels</option>
                    <option value="1">Beginner+</option>
                    <option value="2">Intermediate+</option>
                    <option value="3">Advanced+</option>
                    <option value="4">Expert Only</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Phase 2 JavaScript with Chart.js Integration -->
<script>
// Global chart variables
let talentPipelineChart, competencyGapChart, geographicChart, proficiencyChart, trendAnalysisChart;
let currentPipelineView = 'monthly';
let currentTrendView = '6m';

// Analytics data from Laravel backend
const analyticsData = {
    talentPipeline: @json($stats['talent_pipeline'] ?? []),
    competencyGaps: @json($stats['competency_gaps'] ?? []),
    geographicInsights: @json($stats['geographic_insights'] ?? []),
    proficiencyBreakdown: @json($stats['proficiency_breakdown'] ?? []),
    trendData: @json($stats['trend_data'] ?? []),
    performanceMetrics: @json($stats['performance_metrics'] ?? [])
};

// Initialize all charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Phase 2 Analytics Dashboard Loading...');
    initializeCharts();
    startRealTimeUpdates();
});

// Initialize Chart.js charts
function initializeCharts() {
    initTalentPipelineChart();
    initCompetencyGapChart();
    initGeographicChart();
    initProficiencyChart();
    initTrendAnalysisChart();
}

// Talent Pipeline Chart
function initTalentPipelineChart() {
    const ctx = document.getElementById('talentPipelineChart').getContext('2d');
    const pipelineData = analyticsData.talentPipeline;

    talentPipelineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: pipelineData.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Talents',
                data: pipelineData.new_talents || [12, 19, 15, 22, 18, 25],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Active Talents',
                data: pipelineData.active_talents || [8, 15, 12, 18, 14, 20],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            interaction: {
                mode: 'index',
                intersect: false,
            }
        }
    });
}

// Competency Gap Analysis Chart
function initCompetencyGapChart() {
    const ctx = document.getElementById('competencyGapChart').getContext('2d');
    const gapData = analyticsData.competencyGaps;

    competencyGapChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: gapData.competencies || ['AI/ML', 'Cloud Computing', 'DevOps', 'Data Science', 'Cybersecurity', 'Mobile Dev'],
            datasets: [{
                label: 'Current Supply',
                data: gapData.current_supply || [65, 78, 45, 72, 38, 85],
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                pointBackgroundColor: 'rgb(99, 102, 241)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(99, 102, 241)'
            }, {
                label: 'Market Demand',
                data: gapData.market_demand || [95, 88, 92, 85, 90, 75],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.2)',
                pointBackgroundColor: 'rgb(239, 68, 68)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(239, 68, 68)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                r: {
                    angleLines: {
                        display: false
                    },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
    });
}

// Geographic Distribution Chart
function initGeographicChart() {
    const ctx = document.getElementById('geographicChart').getContext('2d');
    const geoData = analyticsData.geographicInsights;

    geographicChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: geoData.locations || ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Makassar', 'Others'],
            datasets: [{
                data: geoData.talent_counts || [145, 87, 65, 43, 32, 78],
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)',
                    'rgb(139, 92, 246)',
                    'rgb(107, 114, 128)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
}

// Proficiency Distribution Chart
function initProficiencyChart() {
    const ctx = document.getElementById('proficiencyChart').getContext('2d');
    const profData = analyticsData.proficiencyBreakdown;

    proficiencyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Beginner', 'Intermediate', 'Advanced', 'Expert'],
            datasets: [{
                label: 'Number of Competencies',
                data: [
                    profData.filter(p => p.proficiency_level === 1).reduce((sum, p) => sum + p.count, 0),
                    profData.filter(p => p.proficiency_level === 2).reduce((sum, p) => sum + p.count, 0),
                    profData.filter(p => p.proficiency_level === 3).reduce((sum, p) => sum + p.count, 0),
                    profData.filter(p => p.proficiency_level === 4).reduce((sum, p) => sum + p.count, 0)
                ],
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)'
                ],
                borderColor: [
                    'rgb(239, 68, 68)',
                    'rgb(245, 158, 11)',
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Trend Analysis Chart
function initTrendAnalysisChart() {
    const ctx = document.getElementById('trendAnalysisChart').getContext('2d');
    const trendData = analyticsData.trendData;

    trendAnalysisChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.timeline || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Total Talents',
                data: trendData.total_growth || [120, 135, 142, 158, 165, 180],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Active Searches',
                data: trendData.searches_growth || [25, 32, 28, 45, 38, 52],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'New Registrations',
                data: trendData.registrations || [15, 23, 14, 28, 22, 35],
                borderColor: 'rgb(245, 158, 11)',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            interaction: {
                mode: 'index',
                intersect: false,
            }
        }
    });
}

// Toggle Pipeline View
function togglePipelineView(view) {
    currentPipelineView = view;

    // Update button states
    document.getElementById('btn-monthly').className = view === 'monthly' ? 'px-3 py-1 text-xs bg-blue-600 text-white rounded' : 'px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded';
    document.getElementById('btn-quarterly').className = view === 'quarterly' ? 'px-3 py-1 text-xs bg-blue-600 text-white rounded' : 'px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded';

    // Update chart data based on view
    const newLabels = view === 'monthly' ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'] : ['Q1', 'Q2', 'Q3', 'Q4'];
    const newData = view === 'monthly' ? [12, 19, 15, 22, 18, 25] : [46, 57, 33, 67];

    talentPipelineChart.data.labels = newLabels;
    talentPipelineChart.data.datasets[0].data = newData;
    talentPipelineChart.update();
}

// Toggle Trend View
function toggleTrendView(period) {
    currentTrendView = period;

    // Update button states
    ['6m', '1y', '2y'].forEach(p => {
        document.getElementById(`trend-${p}`).className = p === period ? 'px-3 py-1 text-xs bg-blue-600 text-white rounded' : 'px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded';
    });

    // Update chart data based on period
    let newLabels, newData;
    switch(period) {
        case '6m':
            newLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            newData = [120, 135, 142, 158, 165, 180];
            break;
        case '1y':
            newLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            newData = [120, 135, 142, 158, 165, 180, 195, 210, 225, 240, 255, 270];
            break;
        case '2y':
            newLabels = ['2022 Q1', '2022 Q2', '2022 Q3', '2022 Q4', '2023 Q1', '2023 Q2', '2023 Q3', '2023 Q4'];
            newData = [80, 95, 110, 125, 140, 165, 190, 220];
            break;
    }

    trendAnalysisChart.data.labels = newLabels;
    trendAnalysisChart.data.datasets[0].data = newData;
    trendAnalysisChart.update();
}

// Real-time Dashboard Updates
function startRealTimeUpdates() {
    // Update every 30 seconds
    setInterval(refreshDashboard, 30000);
}

// Refresh Dashboard Function
function refreshDashboard() {
    console.log('Refreshing dashboard data...');

    // Show loading indicator
    const indicator = document.getElementById('realtime-indicator');
    indicator.innerHTML = '<span class="animate-spin w-2 h-2 border border-white border-t-transparent rounded-full mr-1.5"></span>Updating...';
    indicator.className = 'ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';

    // Simulate API call to refresh data
    fetch(window.location.href, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update charts with new data
        updateChartsWithNewData(data);

        // Update timestamp
        document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();

        // Reset indicator
        indicator.innerHTML = '<span class="animate-pulse w-2 h-2 bg-green-500 rounded-full mr-1.5"></span>Live';
        indicator.className = 'ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';

        console.log('Dashboard updated successfully');
    })
    .catch(error => {
        console.error('Error refreshing dashboard:', error);

        // Error indicator
        indicator.innerHTML = '<span class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span>Error';
        indicator.className = 'ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    });
}

// Update charts with new data
function updateChartsWithNewData(data) {
    // This would update all charts with fresh data from the server
    // For now, we'll simulate minor changes
    if (talentPipelineChart) {
        talentPipelineChart.update();
    }
    if (competencyGapChart) {
        competencyGapChart.update();
    }
    // Update other charts similarly...
}

// Export Analytics Function
function exportAnalytics() {
    const exportData = {
        timestamp: new Date().toISOString(),
        analytics: analyticsData,
        charts: {
            talentPipeline: currentPipelineView,
            trendAnalysis: currentTrendView
        }
    };

    // Create downloadable JSON file
    const dataStr = JSON.stringify(exportData, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);

    const exportFileDefaultName = `MIS_Analytics_Report_${new Date().toISOString().split('T')[0]}.json`;

    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();

    // Show success message
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    notification.textContent = 'Analytics report exported successfully!';
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Chart.js Error Handler
Chart.register({
    id: 'errorHandler',
    beforeInit: function(chart) {
        chart.options.plugins = chart.options.plugins || {};
        chart.options.plugins.legend = chart.options.plugins.legend || {};
    }
});

console.log('Phase 2 MIS Analytics Dashboard JavaScript Loaded Successfully');
</script>
</x-layouts.app>
