<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 space-y-4 sm:space-y-0">
        <div class="text-left">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📄 Talent Request Details</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Review the details of the talent request.</p>
        </div>
        <a href="{{ route('admin.talent-requests.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors text-sm font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Requests
        </a>
    </div>

    <!-- Session Messages -->
    @if (session('error'))
        <div class="bg-red-100 dark:bg-red-800/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Details Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="px-6 py-8 sm:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requester</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $talentRequest->user->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requester Phone</dt>
                    <dd class="mt-1 text-lg text-gray-700 dark:text-gray-300">{{ $talentRequest->user->phone_number ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Talent</dt>
                    <dd class="mt-1 text-lg text-gray-700 dark:text-gray-300">{{ $talentRequest->talent->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested On</dt>
                    <dd class="mt-1 text-lg text-gray-700 dark:text-gray-300">{{ $talentRequest->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                            @switch($talentRequest->status)
                                @case('pending_admin') bg-yellow-100 dark:bg-yellow-800/50 text-yellow-800 dark:text-yellow-300 @break
                                @case('pending_talent') bg-blue-100 dark:bg-blue-800/50 text-blue-800 dark:text-blue-300 @break
                                @case('approved') bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300 @break
                                @case('rejected_admin')
                                @case('rejected_talent') bg-red-100 dark:bg-red-800/50 text-red-800 dark:text-red-300 @break
                                @default bg-gray-100 dark:bg-gray-700/50 text-gray-800 dark:text-gray-300
                            @endswitch
                        ">
                            {{ $statuses[$talentRequest->status] ?? ucfirst(str_replace('_', ' ', $talentRequest->status)) }}
                        </span>
                    </dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Requested Competencies</dt>
                    <dd class="mt-1">
                        <div class="flex flex-wrap gap-2">
                            @forelse ($talentRequest->competencies as $competency)
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-800/50 text-blue-800 dark:text-blue-300">
                                    {{ $competency->name }} (Required Level: {{ $competency->pivot->required_proficiency_level }})
                                </span>
                            @empty
                                <span class="text-sm text-gray-500 dark:text-gray-400">No specific competencies requested.</span>
                            @endforelse
                        </div>
                    </dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Details</dt>
                    <dd class="text-base text-gray-700 dark:text-gray-300 prose dark:prose-invert max-w-none">
                        {{ $talentRequest->details ?? 'No details provided.' }}
                    </dd>
                </div>
            </div>
        </div>

        {{-- Admin Actions (Reject Only) --}}
        @if ($talentRequest->status === 'pending_admin')
            <div class="px-6 py-5 sm:px-8 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-start">
                    <form action="{{ route('admin.talent-requests.update', $talentRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors" onclick="return confirm('Are you sure you want to reject this request?');">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Reject Request
                        </button>
                    </form>
                </div>
                {{-- Optional: Add field for admin comments --}}
            </div>
        @endif

        {{-- Mark as Completed Action --}}
        @if ($talentRequest->status === 'approved')
            <div class="px-6 py-5 sm:px-8 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-start">
                    <form action="{{ route('admin.talent-requests.complete', $talentRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors" onclick="return confirm('Are you sure you want to mark this request as completed?');">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Mark as Completed
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Ranked Talent Suggestions -->
    <div class="mt-10">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">✨ Ranked Talent Suggestions</h2>
        @if ($rankedTalents->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($rankedTalents as $talent)
                        <li class="px-6 py-5 sm:px-8 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-lg font-medium text-indigo-600 dark:text-indigo-400 truncate">{{ $talent->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $talent->email }}</p>                
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($talent->competencies as $competency)
                                            <span class="px-2 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-blue-100 dark:bg-blue-800/50 text-blue-800 dark:text-blue-300">
                                                {{ $competency->name }} (Level: {{ $competency->pivot->proficiency_level }})
                                            </span>
                                        @endforeach 
                                    </div>
                                </div>
                                <div class="ml-4 text-right">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300">
                                        Score: {{ number_format($talent->dss_score, 2) }}
                                    </span>
                                    {{-- Add link to talent profile if available --}}
                                    {{-- <a href="#" class="mt-1 text-sm text-blue-600 dark:text-blue-400 hover:underline">View Profile</a> --}}

                                    {{-- Assign Talent Button --}}
                                    @if ($talentRequest->status === 'pending_admin')
                                        <form action="{{ route('admin.talent-requests.assign', $talentRequest) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="talent_id" value="{{ $talent->id }}">
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
                                                Assign
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-5 sm:px-8 text-center text-gray-500 dark:text-gray-400">
                            No suitable talents found based on the required competencies.
                        </li>
                    @endforelse
                </ul>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700 px-6 py-8 sm:px-8">
                <p class="text-center text-gray-500 dark:text-gray-400">No suitable talents found based on the required competencies.</p>
            </div>
        @endif
    </div>

</div>
</div>
</x-layouts.app>