<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Manage Talent Requests</h1>
        {{-- Optional: Add button for creating requests if admin can do that --}}
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.talent-requests.index') }}" class="mb-6 bg-white shadow-md rounded px-8 pt-6 pb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                Filter by Status
            </label>
            <select name="status" id="status" class="shadow border rounded w-full md:w-1/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="this.form.submit()">
                <option value="">-- All Statuses --</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>
        @if(request('status'))
            <a href="{{ route('admin.talent-requests.index') }}" class="text-sm text-blue-500 hover:text-blue-700">Clear Filter</a>
        @endif
    </form>

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Requester</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Requester Phone</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Talent</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Details</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Requested On</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse ($requests as $request)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $request->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $request->user->phone_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $request->talent->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 border-b border-gray-200 text-sm">{{ Str::limit($request->details, 50) }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @switch($request->status)
                                    @case('pending_admin') bg-yellow-100 text-yellow-800 @break
                                    @case('pending_talent') bg-blue-100 text-blue-800 @break
                                    @case('approved') bg-green-100 text-green-800 @break
                                    @case('rejected_admin')
                                    @case('rejected_talent') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm">{{ $request->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm">
                            <a href="{{ route('admin.talent-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            {{-- Add other actions like edit/delete if needed --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 border-b border-gray-200">No talent requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $requests->appends(request()->query())->links() }} {{-- Append filter query string to pagination --}}
    </div>

</div>
</x-layouts.app>