<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                My Talent Requests
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">View and manage your requests for talent.</p>
        </div>
        <a href="{{ route('user.requests.create') }}" 
           class="mt-4 sm:mt-0 inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 rounded-lg shadow-md transition-all duration-300 ease-in-out">
            <svg class="w-4 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
            Create New Request
        </a>
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Talent</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Details</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Talent Contact</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requested At</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            {{-- Corrected relationship name from talent to assignedTalent --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $request->assignedTalent?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $request->details }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($request->status)
                                        @case('pending_admin') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @break
                                        @case('pending_talent') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                                        @case('approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                                        @case('rejected_admin')
                                        @case('rejected_talent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endswitch
                                ">
                                    {{ Str::title(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if ($request->status == 'approved' && $request->assignedTalent && $request->assignedTalent->phone_number)
                                    {{ $request->assignedTalent->phone_number }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $request->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                {{-- Add view details link if needed --}}
                                {{-- <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a> --}}
                                
                                @if($request->status == 'pending_admin') {{-- Allow deletion only if pending admin approval --}}
                                    <form action="{{ route('user.requests.destroy', $request) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this request? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center text-gray-400 dark:text-gray-500 cursor-not-allowed" title="Deletion not allowed for this status">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Delete
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                    <p class="text-lg font-semibold mb-1">No Talent Requests Found</p>
                                    <p class="text-sm">You haven't created any talent requests yet.</p>
                                    <a href="{{ route('user.requests.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                                        Create Your First Request
                                    </a>
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