<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Enhanced DSS Results</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Critical Competency-Enhanced Talent Ranking</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('user.requests.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700">
                        <svg class="w-4 h-3 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0l4 4M1 5l4-4"/>
                        </svg>
                        Back to Requests
                    </a>
                </div>
            </div>

            {{-- Critical Competency Impact Summary --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Critical Competencies</p>
                            <p class="text-2xl font-bold">{{ $criticalCompetenciesCount ?? 0 }}</p>
                            <p class="text-orange-100 text-xs">With {{ $vetoThreshold ?? 80 }}% veto threshold</p>
                        </div>
                        <svg class="w-8 h-8 text-orange-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Qualified Talents</p>
                            <p class="text-2xl font-bold">{{ $qualifiedTalentsCount ?? 0 }}</p>
                            <p class="text-blue-100 text-xs">Passed veto threshold</p>
                        </div>
                        <svg class="w-8 h-8 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Enhancement Impact</p>
                            <p class="text-2xl font-bold">{{ $eliminatedTalentsCount ?? 0 }}</p>
                            <p class="text-green-100 text-xs">Talents filtered out</p>
                        </div>
                        <svg class="w-8 h-8 text-green-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Enhanced vs Basic SAW Comparison --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Enhanced DSS vs Basic SAW Comparison</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">Basic SAW Ranking</h4>
                        <div class="space-y-2">
                            @forelse($basicRankings ?? [] as $index => $talent)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-700 text-xs font-medium rounded-full">{{ $index + 1 }}</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $talent['name'] ?? 'Unknown' }}</span>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format(($talent['score'] ?? 0) * 100, 1) }}%</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No basic rankings available</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-medium text-orange-700 dark:text-orange-300 mb-3">Enhanced DSS Ranking</h4>
                        <div class="space-y-2">
                            @forelse($enhancedRankings ?? [] as $index => $talent)
                                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-md border border-orange-200 dark:border-orange-800">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center w-6 h-6 bg-orange-500 text-white text-xs font-medium rounded-full">{{ $index + 1 }}</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $talent['name'] ?? 'Unknown' }}</span>
                                        @if($talent['critical_bonus'] ?? false)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Critical
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-orange-600 dark:text-orange-400 font-medium">{{ number_format(($talent['score'] ?? 0) * 100, 1) }}%</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No enhanced rankings available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detailed Score Breakdown --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detailed Score Breakdown</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Talent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Competency Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Critical Bonus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rank</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($detailedBreakdown ?? [] as $index => $talent)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $talent['name'] ?? 'Unknown' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ number_format(($talent['competency_score'] ?? 0) * 100, 1) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($talent['critical_bonus'] ?? 0 > 1.0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                +{{ number_format((($talent['critical_bonus'] ?? 1.0) - 1.0) * 100, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ number_format(($talent['location_score'] ?? 0) * 100, 1) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ number_format(($talent['total_score'] ?? 0) * 100, 1) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 text-sm font-medium rounded-full dark:bg-blue-900 dark:text-blue-200">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No detailed breakdown available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- DSS Algorithm Insights --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Enhanced DSS Algorithm Insights</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">Veto Threshold Filtering</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Talents below {{ $vetoThreshold ?? 80 }}% of required level for critical competencies are automatically eliminated,
                                ensuring only qualified candidates are considered.
                            </p>
                        </div>
                        <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-200 mb-2">Critical Competency Bonuses</h4>
                            <p class="text-sm text-orange-700 dark:text-orange-300">
                                Talents exceeding requirements in critical competencies receive up to 20% scoring bonus,
                                rewarding exceptional performance in key areas.
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-green-800 dark:text-green-200 mb-2">Enhanced SAW Methodology</h4>
                            <p class="text-sm text-green-700 dark:text-green-300">
                                Combines traditional Simple Additive Weighting with critical competency logic
                                for improved decision support accuracy.
                            </p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-purple-800 dark:text-purple-200 mb-2">Academic Compliance</h4>
                            <p class="text-sm text-purple-700 dark:text-purple-300">
                                Implements mathematically rigorous normalization and validation
                                ensuring academic standards for research applications.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
