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