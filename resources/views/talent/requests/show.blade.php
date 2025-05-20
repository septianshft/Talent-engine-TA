<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Talent Request Details
            </h1>
        </div>
        <a href="{{ route('talent.requests.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Requests
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="px-6 py-5 sm:px-8 sm:py-6 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Request Information</h2>
        </div>
        <div class="px-6 py-5 sm:px-8 sm:py-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">From User</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $talentRequest->requestingUser->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User Phone</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $talentRequest->requestingUser->phone_number ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Request Submitted</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $talentRequest->created_at->format('M d, Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Your Assignment Status</dt>
                <dd class="mt-1 text-lg">
                    @php $assignmentStatus = $currentAssignmentStatus ?? 'unknown'; @endphp
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        @switch($assignmentStatus)
                            @case('pending_assignment_response') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                            @case('approved_by_talent') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                            @case('rejected_by_talent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                            @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                        @endswitch
                    ">
                        {{ Str::title(str_replace('_', ' ', $assignmentStatus)) }}
                    </span>
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Request Details</dt>
                <dd class="mt-1 text-lg text-gray-900 dark:text-white whitespace-pre-wrap">{{ $talentRequest->details }}</dd>
            </div>

            @if ($talentRequest->competencies && $talentRequest->competencies->count() > 0)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Requested Competencies</dt>
                <dd class="mt-1 text-gray-900 dark:text-white">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($talentRequest->competencies as $competency)
                            <li class="text-lg">{{ $competency->name }} (Level: {{ $competency->pivot->level_required }})</li>
                        @endforeach
                    </ul>
                </dd>
            </div>
            @endif
        </div>

        {{-- Action buttons if assignment status is pending_assignment_response --}}
        @if ($currentAssignmentStatus === 'pending_assignment_response')
            <div class="px-6 py-5 sm:px-8 sm:py-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Respond to Your Assignment</h2>
                <form action="{{ route('talent.requests.update', $talentRequest->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    {{-- Optional: Add a comment field here if talents can comment --}}
                    {{--
                    <div class="mb-4">
                        <label for="talent_comments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comments (Optional)</label>
                        <textarea name="talent_comments" id="talent_comments" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    --}}

                    <div class="flex items-center space-x-4">
                        <button type="submit" name="action" value="approve" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Approve Assignment
                        </button>
                        <button type="submit" name="action" value="reject" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors" onclick="return confirm('Are you sure you want to reject this assignment?');">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reject Assignment
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="px-6 py-5 sm:px-8 sm:py-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                 <p class="text-gray-700 dark:text-gray-300">You have already responded to this assignment. Your response was: <strong class="uppercase">{{ str_replace('_', ' ', $currentAssignmentStatus) }}</strong>.</p>
            </div>
        @endif

    </div>
</div>
</x-layouts.app>
