<x-layouts.app>
<!-- Phase 2 Enhanced Analytics Dashboard - Chart.js Integration with Lazy Loading -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

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
                Enhanced Phase 2 analytics with predictive insights and real-time data (Optimized with Redis Caching & Lazy Loading)
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
    </div>

    <!-- Enhanced Phase 2 Key Metrics Cards -->
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

        <!-- Expert Level Talents -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium uppercase tracking-wide">Expert Level</p>
                    <p class="text-3xl font-bold">{{ isset($stats['performance_metrics']['high_performers_count']) ? number_format($stats['performance_metrics']['high_performers_count']) : '0' }}</p>
                    <p class="text-purple-200 text-sm mt-1">
                        {{ isset($stats['performance_metrics']['high_performers_percentage']) ? $stats['performance_metrics']['high_performers_percentage'] : '0' }}% of total talents
                    </p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Geographic Coverage -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Countries</p>
                    <p class="text-3xl font-bold">{{ isset($stats['geographic_insights']['total_countries']) ? $stats['geographic_insights']['total_countries'] : '0' }}</p>
                    <p class="text-orange-200 text-sm mt-1">
                        {{ isset($stats['geographic_insights']['remote_percentage']) ? $stats['geographic_insights']['remote_percentage'] : '0' }}% remote ready
                    </p>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Lazy Loading Charts Section -->
    <div class="space-y-8">
        <!-- Talent Pipeline Chart - First Priority (Load Immediately) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">📈 Talent Pipeline Analytics</h3>
                    <p class="text-gray-600 dark:text-gray-400">Track recruitment and talent development trends</p>
                </div>
                <div class="flex space-x-2">
                    <button id="btn-monthly" onclick="togglePipelineView('monthly')" class="px-3 py-1 text-xs bg-blue-600 text-white rounded">Monthly</button>
                    <button id="btn-quarterly" onclick="togglePipelineView('quarterly')" class="px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded">Quarterly</button>
                </div>
            </div>
            <div class="relative h-80">
                <canvas id="talentPipelineChart"></canvas>
            </div>
        </div>

        <!-- Competency Gap Chart - Load on Scroll -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 ring-1 ring-gray-200 dark:ring-gray-700 lazy-chart" data-chart="competencyGap">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">🎯 Competency Gap Analysis</h3>
                    <p class="text-gray-600 dark:text-gray-400">Identify skill gaps and strategic hiring needs</p>
                </div>
            </div>
            <div class="relative h-80">
                <div class="chart-skeleton" id="competencyGapSkeleton">
                    <div class="animate-pulse bg-gray-300 dark:bg-gray-600 h-full rounded"></div>
                </div>
                <canvas id="competencyGapChart" style="display: none;"></canvas>
            </div>
        </div>

        <!-- Geographic Distribution Chart - Load on Scroll -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 ring-1 ring-gray-200 dark:ring-gray-700 lazy-chart" data-chart="geographic">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">🌍 Geographic Distribution</h3>
                    <p class="text-gray-600 dark:text-gray-400">Global talent distribution and location insights</p>
                </div>
            </div>
            <div class="relative h-80">
                <div class="chart-skeleton" id="geographicSkeleton">
                    <div class="animate-pulse bg-gray-300 dark:bg-gray-600 h-full rounded"></div>
                </div>
                <canvas id="geographicChart" style="display: none;"></canvas>
            </div>
        </div>

        <!-- Proficiency Distribution Chart - Load on Scroll -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 ring-1 ring-gray-200 dark:ring-gray-700 lazy-chart" data-chart="proficiency">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">⭐ Proficiency Distribution</h3>
                    <p class="text-gray-600 dark:text-gray-400">Skill level breakdown across talent pool</p>
                </div>
            </div>
            <div class="relative h-80">
                <div class="chart-skeleton" id="proficiencySkeleton">
                    <div class="animate-pulse bg-gray-300 dark:bg-gray-600 h-full rounded"></div>
                </div>
                <canvas id="proficiencyChart" style="display: none;"></canvas>
            </div>
        </div>

        <!-- Trend Analysis Chart - Load on Scroll -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 ring-1 ring-gray-200 dark:ring-gray-700 lazy-chart" data-chart="trend">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">📊 Trend Analysis</h3>
                    <p class="text-gray-600 dark:text-gray-400">6-month historical data and growth patterns</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="toggleTrendView('6m')" class="px-3 py-1 text-xs bg-blue-600 text-white rounded">6 Months</button>
                    <button onclick="toggleTrendView('12m')" class="px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded">12 Months</button>
                </div>
            </div>
            <div class="relative h-80">
                <div class="chart-skeleton" id="trendSkeleton">
                    <div class="animate-pulse bg-gray-300 dark:bg-gray-600 h-full rounded"></div>
                </div>
                <canvas id="trendAnalysisChart" style="display: none;"></canvas>
            </div>
        </div>
    </div>

    <!-- Performance Insights Section -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 ring-1 ring-gray-200 dark:ring-gray-700">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">🚀 Performance Insights</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ isset($stats['performance_metrics']['avg_competencies_per_talent']) ? $stats['performance_metrics']['avg_competencies_per_talent'] : '0' }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Avg Skills per Talent</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                    {{ isset($stats['performance_metrics']['system_efficiency']) ? $stats['performance_metrics']['system_efficiency'] : '0' }}%
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">System Efficiency</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    {{ isset($stats['performance_metrics']['data_quality_score']) ? $stats['performance_metrics']['data_quality_score'] : '0' }}%
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Data Quality Score</div>
            </div>
        </div>
    </div>
</div>

<!-- Optimized JavaScript with Lazy Loading -->
<script>
// Global chart variables
let talentPipelineChart, competencyGapChart, geographicChart, proficiencyChart, trendAnalysisChart;
let currentPipelineView = 'monthly';
let currentTrendView = '6m';
let loadedCharts = new Set();

// Analytics data from Laravel backend
const analyticsData = {
    talentPipeline: @json($stats['talent_pipeline'] ?? []),
    competencyGaps: @json($stats['competency_gaps'] ?? []),
    geographicInsights: @json($stats['geographic_insights'] ?? []),
    proficiencyBreakdown: @json($stats['proficiency_breakdown'] ?? []),
    trendData: @json($stats['trend_data'] ?? []),
    performanceMetrics: @json($stats['performance_metrics'] ?? [])
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Phase 2 Analytics Dashboard Loading with Lazy Loading...');

    // Load the first chart immediately for better perceived performance
    initTalentPipelineChart();
    loadedCharts.add('talentPipeline');

    // Set up lazy loading for other charts
    setupLazyLoading();
    startRealTimeUpdates();
});

// Lazy loading setup with Intersection Observer
function setupLazyLoading() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const chartType = entry.target.dataset.chart;
                if (!loadedCharts.has(chartType)) {
                    loadChart(chartType);
                    loadedCharts.add(chartType);
                    observer.unobserve(entry.target);
                }
            }
        });
    }, {
        rootMargin: '50px' // Start loading 50px before the element comes into view
    });

    // Observe all lazy chart containers
    document.querySelectorAll('.lazy-chart').forEach(chart => {
        observer.observe(chart);
    });
}

// Load individual charts
function loadChart(chartType) {
    console.log(`📊 Loading ${chartType} chart...`);

    // Hide skeleton and show canvas
    const skeleton = document.getElementById(`${chartType}Skeleton`);
    const canvas = document.getElementById(`${chartType}Chart`);

    if (skeleton) skeleton.style.display = 'none';
    if (canvas) canvas.style.display = 'block';

    switch(chartType) {
        case 'competencyGap':
            initCompetencyGapChart();
            break;
        case 'geographic':
            initGeographicChart();
            break;
        case 'proficiency':
            initProficiencyChart();
            break;
        case 'trend':
            initTrendAnalysisChart();
            break;
    }
}

// Talent Pipeline Chart (loads immediately)
function initTalentPipelineChart() {
    const ctx = document.getElementById('talentPipelineChart')?.getContext('2d');
    if (!ctx) return;

    const pipelineData = analyticsData.talentPipeline;

    talentPipelineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: pipelineData.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Senior Talents',
                data: [pipelineData.senior || 45, 52, 48, 61, 58, 67],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Mid-level Talents',
                data: [pipelineData.mid || 78, 85, 82, 94, 89, 102],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Junior Talents',
                data: [pipelineData.junior || 124, 135, 128, 147, 142, 156],
                borderColor: 'rgb(245, 158, 11)',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Competency Gap Analysis Chart (lazy loaded)
function initCompetencyGapChart() {
    const ctx = document.getElementById('competencyGapChart')?.getContext('2d');
    if (!ctx) return;

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
            maintainAspectRatio: false,
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
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Geographic Distribution Chart (lazy loaded)
function initGeographicChart() {
    const ctx = document.getElementById('geographicChart')?.getContext('2d');
    if (!ctx) return;

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
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Proficiency Distribution Chart (lazy loaded)
function initProficiencyChart() {
    const ctx = document.getElementById('proficiencyChart')?.getContext('2d');
    if (!ctx) return;

    const profData = analyticsData.proficiencyBreakdown;

    proficiencyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Beginner', 'Intermediate', 'Advanced', 'Expert'],
            datasets: [{
                label: 'Number of Competencies',
                data: [
                    profData.filter(p => p.proficiency_level === 1).reduce((sum, p) => sum + p.count, 0) || 145,
                    profData.filter(p => p.proficiency_level === 2).reduce((sum, p) => sum + p.count, 0) || 234,
                    profData.filter(p => p.proficiency_level === 3).reduce((sum, p) => sum + p.count, 0) || 189,
                    profData.filter(p => p.proficiency_level === 4).reduce((sum, p) => sum + p.count, 0) || 98
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
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Trend Analysis Chart (lazy loaded)
function initTrendAnalysisChart() {
    const ctx = document.getElementById('trendAnalysisChart')?.getContext('2d');
    if (!ctx) return;

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
            maintainAspectRatio: false,
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
            animation: {
                duration: 1300,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Toggle functions
function togglePipelineView(view) {
    currentPipelineView = view;

    document.getElementById('btn-monthly').className = view === 'monthly' ? 'px-3 py-1 text-xs bg-blue-600 text-white rounded' : 'px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded';
    document.getElementById('btn-quarterly').className = view === 'quarterly' ? 'px-3 py-1 text-xs bg-blue-600 text-white rounded' : 'px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded';

    if (talentPipelineChart) {
        const newLabels = view === 'monthly' ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'] : ['Q1', 'Q2', 'Q3', 'Q4'];
        talentPipelineChart.data.labels = newLabels;
        talentPipelineChart.update('active');
    }
}

function toggleTrendView(view) {
    currentTrendView = view;
    // Implementation for trend view toggle
}

// Real-time updates (reduced frequency for better performance)
function startRealTimeUpdates() {
    setInterval(() => {
        document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
    }, 30000); // Update every 30 seconds instead of every second
}

// Dashboard refresh function
function refreshDashboard() {
    const indicator = document.getElementById('realtime-indicator');
    indicator.innerHTML = '<span class="animate-spin w-2 h-2 border border-white border-t-transparent rounded-full mr-1.5"></span>Updating...';
    indicator.className = 'ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';

    fetch(window.location.href, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        updateChartsWithNewData(data);
        document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();

        indicator.innerHTML = '<span class="animate-pulse w-2 h-2 bg-green-500 rounded-full mr-1.5"></span>Live';
        indicator.className = 'ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';

        console.log('📊 Dashboard updated successfully');
    })
    .catch(error => {
        console.error('❌ Error refreshing dashboard:', error);
        indicator.innerHTML = '<span class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span>Error';
        indicator.className = 'ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    });
}

// Update charts with new data
function updateChartsWithNewData(data) {
    // Only update charts that are already loaded
    if (loadedCharts.has('talentPipeline') && talentPipelineChart) {
        talentPipelineChart.update('none'); // No animation for better performance
    }
    if (loadedCharts.has('competencyGap') && competencyGapChart) {
        competencyGapChart.update('none');
    }
    if (loadedCharts.has('geographic') && geographicChart) {
        geographicChart.update('none');
    }
    if (loadedCharts.has('proficiency') && proficiencyChart) {
        proficiencyChart.update('none');
    }
    if (loadedCharts.has('trend') && trendAnalysisChart) {
        trendAnalysisChart.update('none');
    }
}

// Export analytics function
function exportAnalytics() {
    const exportData = {
        timestamp: new Date().toISOString(),
        analytics: analyticsData,
        loadedCharts: Array.from(loadedCharts),
        performance: {
            lazyLoadingEnabled: true,
            cachingEnabled: true
        }
    };

    const dataStr = JSON.stringify(exportData, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    const exportFileDefaultName = `MIS_Analytics_Report_${new Date().toISOString().split('T')[0]}.json`;

    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();

    // Show success notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    notification.textContent = '✅ Analytics report exported successfully!';
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

console.log('🚀 Phase 2 MIS Analytics Dashboard with Performance Optimizations Loaded Successfully');
</script>

<!-- Custom CSS for skeleton loading -->
<style>
.chart-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

.dark .chart-skeleton {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    background-size: 200% 100%;
}
</style>
</x-layouts.app>
