@php
use Illuminate\Support\Facades\Auth;
use App\Models\TalentRequest;

$talent = Auth::user();
$talentName = $talent->name;
$receivedRequests = TalentRequest::where('talent_id', $talent->id)
    ->with('requestingUser') // Eager load the user who made the request
    ->latest()
    ->take(5) // Show the 5 most recent requests
    ->get();
@endphp

<x-layouts.app :title="__('Talent Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4 md:p-6">
        {{-- Welcome Message --}}
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-gray-800">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                Welcome back, {{ $talentName }}!
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Here are the latest talent requests assigned to you.
            </p>
        </div>

        {{-- Recent Received Talent Requests --}}
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Received Requests</h2>
                {{-- Optional: Link to view all received requests --}}
                {{-- <a href="#" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">View all</a> --}}
            </div>
            @if ($receivedRequests->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">You have no pending talent requests assigned to you.</p>
            @else
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($receivedRequests as $request)
                        <li class="py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Request from: {{ $request->requestingUser->name ?? 'N/A' }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Phone: {{ $request->requestingUser->phone_number ?? 'N/A' }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Received: {{ $request->created_at->diffForHumans() }}</p>
                                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">Details: {{ Str::limit($request->details, 100) }}</p>
                                </div>
                                <span @class([
                                    'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                    'bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20' => $request->status === 'pending',
                                    'bg-blue-50 text-blue-800 ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-500 dark:ring-blue-400/20' => $request->status === 'in progress',
                                    'bg-green-50 text-green-800 ring-green-600/20 dark:bg-green-400/10 dark:text-green-500 dark:ring-green-400/20' => $request->status === 'approved',
                                    'bg-red-50 text-red-800 ring-red-600/20 dark:bg-red-400/10 dark:text-red-500 dark:ring-red-400/20' => $request->status === 'rejected',
                                    'bg-gray-50 text-gray-800 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-500 dark:ring-gray-400/20' => !in_array($request->status, ['pending', 'in progress', 'approved', 'rejected']),
                                ])>
                                    {{ Str::ucfirst($request->status) }}
                                </span>
                            </div>
                            {{-- Optional: Add action buttons like 'View Details', 'Accept', 'Reject' --}}
                            {{-- <div class="mt-2 flex space-x-2">
                                <a href="#" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-500">View Details</a>
                            </div> --}}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Talent competencies --}}
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Competencies</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $talent->competencies->count() }} skills</span>
            </div>
            @if($talent->competencies->isEmpty())
                <div class="flex flex-col items-center justify-center space-y-3 py-6 text-center">
                    <svg class="h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">No competencies added yet</p>
                    <button class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" disabled>
                        Add New Competency
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($talent->competencies as $competency)
                        <div class="group relative flex items-center space-x-3 rounded-lg border border-neutral-200 bg-white p-4 transition-all hover:border-blue-200 hover:bg-blue-50 dark:border-neutral-700 dark:bg-gray-800 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                            <div class="flex-shrink-0 text-blue-600 dark:text-blue-400">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $competency->name }}</p>
                                <div class="mt-1 flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center rounded bg-green-100 px-1.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-800/30 dark:text-green-400">
                                        {{ $competency->pivot->proficiency_level ?? 'Intermediate' }}</span>
                                    </span>
                                    <span class="text-xs">·</span>
                                    <span>Your proficiency level</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>