@php
use Illuminate\Support\Facades\Auth;
use App\Models\TalentRequest;
use App\Models\Competency;

$user = Auth::user();
$userName = $user->name;
$recentRequests = TalentRequest::where('user_id', $user->id)
    ->latest()
    ->take(5)
    ->get();

$competencies = Competency::withCount('users')->get();
$competencyNames = $competencies->pluck('name')->toJson();
$competencyTalentCounts = $competencies->pluck('users_count')->toJson();

@endphp

<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Welcome Message and Create Button --}}
            <div
                class="relative z-0 flex flex-col items-start justify-between gap-4 overflow-hidden rounded-lg border border-neutral-200 bg-gray-50 p-6 shadow-sm dark:border-neutral-700 dark:bg-gray-800 md:col-span-1"
                style="background-image: url('{{ asset('images/user_recruit_add.png') }}'); background-size: contain; background-position: bottom right; background-repeat: no-repeat;"
            >
                {{-- Content --}}
                <div class="relative z-10">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Welcome back, {{ $userName }}!
                    </h1>
                </div>
                <div class="relative z-10 mt-auto">
                    <a href="{{ route('user.requests.create') }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create New Request
                    </a>
                </div>
            </div>

            {{-- Contact Information Card --}}
            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-gray-800 md:col-span-1">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-md font-medium text-gray-900 dark:text-white">
                        Contact Information
                    </h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="contact_name" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Name</label>
                        <div class="mt-1 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-gray-400 dark:text-gray-500 mr-2">
                                <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
                            </svg>
                            <p class="text-sm text-gray-900 dark:text-white">Informasi Kontak Layanan</p>
                        </div>
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Phone</label>
                        <div class="mt-1 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-gray-400 dark:text-gray-500 mr-2">
                                <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm text-gray-900 dark:text-white">+62 821-3817-3919</p>
                        </div>
                    </div>
                    <div>
                        <label for="contact_email" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <div class="mt-1 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-gray-400 dark:text-gray-500 mr-2">
                                <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                            </svg>
                            <p class="text-sm text-gray-900 dark:text-white">intelligentsensingiot@telkomuniversity.ac.id</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Competency Chart --}}
            <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-gray-800 md:col-span-1">
                <h2 class="mb-3 text-md font-medium text-gray-900 dark:text-white">
                    Talents per Competency
                </h2>
                <div class="relative h-32 md:max-h-64 lg:max-h-96">
                    <canvas id="competencyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Recent Talent Requests --}}
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Recent Talent Requests
                </h2>
                <a href="{{ route('user.requests.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    View all
                </a>
            </div>
            <div class="mt-4 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentRequests as $request)
                                <li class="py-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $request->title }}
                                            </p>
                                            <p class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.75 3a.75.75 0 01.75.75v.5a.75.75 0 01-1.5 0v-.5A.75.75 0 015.75 3zm-1.5 3.5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5a.75.75 0 01-.75-.75zM4 10a.75.75 0 01.75-.75h5.5a.75.75 0 010 1.5h-5.5A.75.75 0 014 10zm2 3a.75.75 0 01.75-.75h2.5a.75.75 0 010 1.5h-2.5A.75.75 0 016 13z" clip-rule="evenodd" />
                                                </svg>
                                                Created {{ $request->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div>
                                            <span @class([
                                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => $request->status === 'Pending',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => $request->status === 'Approved',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $request->status === 'Rejected',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' => $request->status === 'In Progress',
                                                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' => !in_array($request->status, ['Pending', 'Approved', 'Rejected', 'In Progress']),
                                            ])>
                                                {{ $request->status }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    You haven't created any talent requests yet.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('competencyChart').getContext('2d');
            const isDarkMode = document.documentElement.classList.contains('dark');
            const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            const labelColor = isDarkMode ? '#cbd5e1' : '#6b7280'; // slate-300 : gray-500

            const competencyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! $competencyNames !!},
                    datasets: [{
                        label: '# of Talents',
                        data: {!! $competencyTalentCounts !!},
                        backgroundColor: 'rgba(79, 70, 229, 0.5)', // Indigo-600 with 50% opacity
                        borderColor: 'rgba(79, 70, 229, 1)', // Indigo-600
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
                                color: labelColor,
                                // Ensure only integers are shown on the Y axis
                                precision: 0
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        x: {
                           ticks: {
                                color: labelColor
                           },
                           grid: {
                                display: false // Hide X-axis grid lines
                           }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true, // Show legend now
                            position: 'top',
                            labels: {
                                color: labelColor
                            }
                        }
                    }
                }
            });

            // Update chart colors on theme change
            const observer = new MutationObserver((mutationsList) => {
                for(let mutation of mutationsList) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        const isDarkModeNow = document.documentElement.classList.contains('dark');
                        const newGridColor = isDarkModeNow ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                        const newLabelColor = isDarkModeNow ? '#cbd5e1' : '#6b7280';

                        competencyChart.options.scales.x.ticks.color = newLabelColor;
                        competencyChart.options.scales.y.ticks.color = newLabelColor;
                        competencyChart.options.scales.y.grid.color = newGridColor;
                        competencyChart.options.plugins.legend.labels.color = newLabelColor;

                        competencyChart.update();
                    }
                }
            });
            observer.observe(document.documentElement, { attributes: true });
        });
    </script>
    @endpush
</x-layouts.app>
