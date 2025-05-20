<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 md:py-10"> {{-- Adjusted py for consistency --}}
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6 sm:space-y-8 lg:space-y-10"> {{-- Adjusted px and space-y --}}

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"> {{-- Adjusted gap --}}
                <x-dashboard.card title="Total Users" value="{{ $totalUsers }}" color="indigo" />
                <x-dashboard.card title="Pending Requests" value="{{ $pendingRequests }}" color="yellow" />
                <x-dashboard.card title="Active Talents" value="{{ $activeTalents }}" color="green" />
            </div>

            {{-- Chart Section --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-2xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6 h-72 sm:h-80 md:h-96"> {{-- Adjusted padding and made height responsive --}}
                <h3 class="text-md sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">Talent Competency Distribution</h3> {{-- Adjusted text size and margin --}}
                <div class="relative h-full w-full">
                    <canvas id="competencyChart"></canvas>
                </div>
            </div>

            {{-- User Management Section --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-2xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6"> {{-- Adjusted padding --}}
                <h3 class="text-md sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">User Management</h3> {{-- Adjusted text size and margin --}}
                <livewire:admin.user-management />
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('competencyChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($competencyLabels),
                        datasets: [{
                            label: 'Number of Talents',
                            data: @json($competencyCounts),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
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
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-layouts.app>
