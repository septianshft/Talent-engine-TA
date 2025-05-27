<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 space-y-4 sm:space-y-0">
        <div class="text-left">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">🤝 Manage Talent Requests</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Review and manage requests for talent.</p>
        </div>
        {{-- Optional: Add button for creating requests if admin can do that --}}
    </div>

    <!-- Session Messages -->
    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-800/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 dark:bg-red-800/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.talent-requests.index') }}" class="mb-8 bg-white dark:bg-gray-800 shadow-md rounded-lg px-6 py-4 ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2" for="status">
                    Filter by Status:
                </label>
                <select name="status" id="status" class="block w-full shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- All Statuses --</option>
                    @foreach ($statuses as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" {{ request('status') == $statusValue ? 'selected' : '' }}>
                            {{ $statusLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2" for="work_location_type">
                    Work Location Type:
                </label>
                <select name="work_location_type" id="work_location_type" class="block w-full shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- All Types --</option>
                    <option value="remote" {{ request('work_location_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                    <option value="on_site" {{ request('work_location_type') == 'on_site' ? 'selected' : '' }}>On-site</option>
                    <option value="hybrid" {{ request('work_location_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2" for="work_location_country">
                    Work Location Country:
                </label>
                <input type="text" name="work_location_country" id="work_location_country" value="{{ request('work_location_country') }}"
                       placeholder="Enter country..."
                       class="block w-full shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">&nbsp;</label>
                <div class="flex items-center space-x-3">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['status', 'work_location_type', 'work_location_country']))
                        <a href="{{ route('admin.talent-requests.index') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Clear All
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <!-- Talent Requests Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Requester</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Requester Domicile</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Phone</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Talent</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Work Location</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Requested Competencies</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Details</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Requested On</th>
                        <th scope="col" class="px-6 py-4 text-right text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $request->requestingUser->name ?? 'N/A' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $request->requestingUser->domicile_city ?? 'N/A' }}@if($request->requestingUser->domicile_city && $request->requestingUser->domicile_country), @endif{{ $request->requestingUser->domicile_country ?? '' }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $request->requestingUser->phone_number ?? 'N/A' }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                @if($request->assignedTalents && $request->assignedTalents->count() > 0)
                                    @if($request->assignedTalents->count() === 1)
                                        {{ $request->assignedTalents->first()->name }}
                                    @else
                                        <div class="flex flex-col space-y-1">
                                            @foreach($request->assignedTalents as $talent)
                                                <span class="text-xs">{{ $talent->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 italic">Not assigned yet</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ Str::title(str_replace('_', ' ', $request->work_location_type)) }}
                                @if($request->work_location_type !== 'remote')
                                    <br><span class="text-xs text-gray-500 dark:text-gray-400">({{ $request->work_location_city ?? 'N/A' }}, {{ $request->work_location_country ?? 'N/A' }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-600 dark:text-gray-400">
                                <div class="max-w-xs">
                                    @forelse ($request->competencies as $competency)
                                        @if($loop->first)
                                            <div class="space-y-1">
                                        @endif
                                        <div class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg px-3 py-2 border border-blue-200 dark:border-blue-700/50">
                                            <span class="text-xs font-medium text-blue-800 dark:text-blue-300 truncate">
                                                {{ $competency->name }}
                                            </span>
                                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 ml-2 flex-shrink-0">
                                                L{{ $competency->pivot->required_proficiency_level }}
                                            </span>
                                        </div>
                                        @if($loop->last)
                                            </div>
                                        @endif
                                        @if($loop->iteration >= 3 && !$loop->last)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 italic mt-1">
                                                +{{ $request->competencies->count() - 3 }} more...
                                            </div>
                                            @break
                                        @endif
                                    @empty
                                        <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                            <span class="text-xs italic">No specific requirements</span>
                                        </div>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-600 dark:text-gray-400 max-w-xs">
                                <div class="truncate" title="{{ $request->details }}">{{ Str::limit($request->details, 50) }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full mr-3
                                        @switch($request->status)
                                            @case('pending_admin')
                                                bg-yellow-400
                                                @if($request->isPendingAdminAfterDirectOfferRejection) ring-2 ring-yellow-600 dark:ring-yellow-300 ring-offset-1 dark:ring-offset-gray-800 @endif
                                                @break
                                            @case('pending_talent') bg-blue-400 @break
                                            @case('approved') bg-green-400 @break
                                            @case('rejected_admin')
                                            @case('rejected_talent') bg-red-400 @break
                                            @case('completed') bg-emerald-400 @break
                                            @default bg-gray-400
                                        @endswitch
                                    "></div>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $statuses[$request->status] ?? ucfirst(str_replace('_', ' ', $request->status)) }}
                                        @if($request->isPendingAdminAfterDirectOfferRejection)
                                            <span class="block text-xs text-yellow-600 dark:text-yellow-400 font-normal">(Direct Offer Rejected)</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $request->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- View Details Button -->
                                    <a href="{{ route('admin.talent-requests.show', $request) }}"
                                       class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-indigo-700 bg-indigo-100 border border-indigo-200 rounded-lg hover:bg-indigo-200 hover:text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800 dark:hover:bg-indigo-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>

                                    <!-- Mark as Completed Button -->
                                    @if ($request->status === 'approved')
                                        <form action="{{ route('admin.talent-requests.complete', $request) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-green-700 bg-green-100 border border-green-200 rounded-lg hover:bg-green-200 hover:text-green-800 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800 dark:hover:bg-green-900/50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-all duration-200"
                                                    onclick="return confirm('Are you sure you want to mark this request as completed?');">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Complete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div class="text-center">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No talent requests found</h3>
                                        <p class="text-gray-500 dark:text-gray-400">There are no talent requests matching your current filters.</p>
                                        @if(request()->hasAny(['status', 'work_location_type', 'work_location_country']))
                                            <a href="{{ route('admin.talent-requests.index') }}" class="inline-flex items-center mt-4 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Clear all filters
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            {{ $requests->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
</x-layouts.app>
