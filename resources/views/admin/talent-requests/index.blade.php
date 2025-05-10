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
        <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium" for="status">
                Filter by Status:
            </label>
            <select name="status" id="status" class="block w-full sm:w-auto shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                <option value="">-- All Statuses --</option>
                @foreach ($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ request('status') == $statusValue ? 'selected' : '' }}>
                        {{ $statusLabel }}
                    </option>
                @endforeach
            </select>
            @if(request('status'))
                <a href="{{ route('admin.talent-requests.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                    Clear Filter
                </a>
            @endif
        </div>
    </form>

    <!-- Talent Requests Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Requester</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Phone</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Talent</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $request->requestingUser->name ?? 'N/A' }}</td> {{-- Corrected relationship --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $request->requestingUser->phone_number ?? 'N/A' }}</td> {{-- Corrected relationship --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $request->assignedTalent->name ?? 'N/A' }}</td> {{-- Corrected relationship --}}
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-md">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($request->competencies as $competency)
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-blue-100 dark:bg-blue-800/50 text-blue-800 dark:text-blue-300">
                                            {{ $competency->name }} (Lvl: {{ $competency->pivot->required_proficiency_level }})
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-500 dark:text-gray-400">No specific competencies requested.</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate" title="{{ $request->details }}">{{ Str::limit($request->details, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($request->status)
                                        @case('pending_admin') bg-yellow-100 dark:bg-yellow-800/50 text-yellow-800 dark:text-yellow-300 @break
                                        @case('pending_talent') bg-blue-100 dark:bg-blue-800/50 text-blue-800 dark:text-blue-300 @break
                                        @case('approved') bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300 @break
                                        @case('rejected_admin')
                                        @case('rejected_talent') bg-red-100 dark:bg-red-800/50 text-red-800 dark:text-red-300 @break
                                        @default bg-gray-100 dark:bg-gray-700/50 text-gray-800 dark:text-gray-300
                                    @endswitch
                                ">
                                    {{ $statuses[$request->status] ?? ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $request->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.talent-requests.show', $request) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Details
                                </a>

                                {{-- Mark as Completed Button --}}
                                @if ($request->status === 'approved')
                                    <form action="{{ route('admin.talent-requests.complete', $request) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors" onclick="return confirm('Are you sure you want to mark this request as completed?');">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Complete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">No talent requests found matching the criteria.</td>
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