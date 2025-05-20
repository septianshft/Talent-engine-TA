<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Assigned Talent Requests
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Review and respond to talent requests assigned to you.</p>
        </div>
        {{-- Optional: Add filter or other actions here if needed --}}
    </div>

    <!-- Session Messages -->
    @if (session('success'))
        <div class="bg-green-50 dark:bg-green-900 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline ml-2">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 dark:bg-red-900 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline ml-2">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Table Container -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">From User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User Phone</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Details</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Your Assignment Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assigned At</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $request->requestingUser->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $request->requestingUser->phone_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate" title="{{ $request->details }}">{{ Str::limit($request->details, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{-- Display the status from the pivot table --}}
                                @php $assignmentStatus = $request->pivot->status ?? 'unknown'; @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($assignmentStatus)
                                        @case('pending_assignment_response') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                                        @case('approved_by_talent') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                                        @case('rejected_by_talent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endswitch
                                ">
                                    {{ Str::title(str_replace('_', ' ', $assignmentStatus)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $request->pivot->created_at ? $request->pivot->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('talent.requests.show', $request->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-offset-gray-800 transition-colors">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"></path></svg>
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                    <p class="text-lg font-semibold mb-1">No Talent Requests Assigned</p>
                                    <p class="text-sm">You currently have no talent requests assigned to you that require action.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($requests->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 border-t border-gray-200 dark:border-gray-700">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>
</x-layouts.app>
