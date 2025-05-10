<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Users</h3>
                    <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pending Requests</h3>
                    <p class="mt-1 text-3xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $pendingRequests }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Active Talents</h3>
                    <p class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400">{{ $activeTalents }}</p>
                </div>
            </div>

            <!-- Competency Distribution Chart -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-8 h-96"> {{-- Added h-96 for height --}}
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Talent Competency Distribution</h3>
                <div class="relative h-full w-full"> {{-- Added relative container for canvas --}}
                    <canvas id="competencyChart"></canvas>
                </div>
            </div>

            <!-- Existing Content -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-8 text-gray-900 dark:text-gray-100 space-y-4">
                    <livewire:admin.user-management/>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> {{-- Or import via Vite --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('competencyChart').getContext('2d');
            const competencyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($competencyLabels),
                    datasets: [{
                        label: 'Number of Talents',
                        data: @json($competencyCounts),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)', // Teal color
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1 // Ensure y-axis increments by whole numbers
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // Hide legend if only one dataset
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>

