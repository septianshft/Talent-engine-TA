<x-layouts.app>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section with Better Spacing -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12">
            <div class="mb-4 sm:mb-0">
                <div class="flex items-center mb-2">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">
                        Talent Request Details
                    </h1>
                </div>
                <p class="text-lg text-gray-600 dark:text-gray-400 ml-14">Review and respond to your assignment</p>
            </div>
            <a href="{{ route('talent.requests.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-200 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-200 group">
                <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Requests
            </a>
        </div>

        <!-- Main Content Card with Enhanced Design -->
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <!-- Card Header -->
            <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Request Information</h2>
                </div>
            </div>

            <!-- Content Grid with Better Spacing -->
            <div class="px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- User Information Section -->
                    <div class="space-y-6">
                        <!-- From User -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-100 dark:border-gray-600">
                            <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">From User</dt>
                            <dd class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                {{ $talentRequest->requestingUser->name ?? 'N/A' }}
                            </dd>
                        </div>

                        <!-- User Phone -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-100 dark:border-gray-600">
                            <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Contact Phone</dt>
                            <dd class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                {{ $talentRequest->requestingUser->phone_number ?? 'N/A' }}
                            </dd>
                        </div>

                        <!-- Requester Domicile -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-100 dark:border-gray-600">
                            <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Requester Location</dt>
                            <dd class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                {{ $talentRequest->requestingUser->domicile_city ?? 'N/A' }}@if($talentRequest->requestingUser->domicile_city && $talentRequest->requestingUser->domicile_country), @endif{{ $talentRequest->requestingUser->domicile_country ?? '' }}
                            </dd>
                        </div>
                    </div>

                    <!-- Request Details Section -->
                    <div class="space-y-6">
                        <!-- Request Submitted -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-100 dark:border-gray-600">
                            <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Request Submitted</dt>
                            <dd class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/50 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                {{ $talentRequest->created_at->format('M d, Y H:i') }}
                            </dd>
                        </div>

                        <!-- Assignment Status -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-100 dark:border-gray-600">
                            <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Your Assignment Status</dt>
                            <dd class="flex items-center">
                                @php
                                    $assignmentStatus = $currentAssignmentStatus ?? 'unknown';
                                    $isDirectOffer = ($assignmentType ?? 'standard') === 'direct_offer';
                                @endphp
                                <span class="px-4 py-2 inline-flex text-sm leading-5 font-bold rounded-full border-2
                                    @switch($assignmentStatus)
                                        @case('direct_offer_pending')
                                            bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-600
                                            @break
                                        @case('pending_assignment_response')
                                            bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-600
                                            @break
                                        @case('approved_by_talent')
                                            bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-600
                                            @break
                                        @case('rejected_by_talent')
                                            bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-600
                                            @break
                                        @default
                                            bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600
                                    @endswitch
                                ">
                                    @switch($assignmentStatus)
                                        @case('direct_offer_pending')
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.79 4 4s-1.79 4-4 4c-1.742 0-3.223-.835-3.772-2M12 12H4m4 4H4m4-8H4"/></svg>
                                            @break
                                        @case('pending_assignment_response')
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            @break
                                        @case('approved_by_talent')
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            @break
                                        @case('rejected_by_talent')
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            @break
                                    @endswitch
                                    @if($isDirectOffer && $assignmentStatus === 'direct_offer_pending')
                                        Direct Offer Pending
                                    @elseif($isDirectOffer && $assignmentStatus === 'pending_assignment_response') {{-- Should not happen with correct logic --}}
                                        Direct Offer: {{ Str::title(str_replace('_', ' ', $assignmentStatus)) }}
                                    @else
                                        {{ Str::title(str_replace('_', ' ', $assignmentStatus)) }}
                                    @endif
                                </span>
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Request Details Full Width -->
                <div class="mt-8 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700/50 dark:to-gray-600/50 rounded-xl p-8 border border-gray-100 dark:border-gray-600">
                    <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Request Details
                    </dt>
                    <dd class="text-lg text-gray-900 dark:text-white leading-relaxed whitespace-pre-wrap bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-600 shadow-sm">
                        {{ $talentRequest->details }}
                    </dd>
                </div>

                <!-- Work Location and Competencies Section -->
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Work Location Type -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-8 border border-indigo-100 dark:border-indigo-700">
                        <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Work Location Type
                        </dt>
                        <dd class="flex items-center flex-wrap gap-3">
                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ Str::title(str_replace('_', ' ', $talentRequest->work_location_type)) }}
                            </span>
                            @if($talentRequest->work_location_type === 'remote')
                                <span class="px-4 py-2 text-sm font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 border-2 border-green-200 dark:border-green-600">
                                    🌍 Work from anywhere
                                </span>
                            @elseif($talentRequest->work_location_type === 'hybrid')
                                <span class="px-4 py-2 text-sm font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 border-2 border-blue-200 dark:border-blue-600">
                                    🏢🏠 Flexible arrangement
                                </span>
                            @else
                                <span class="px-4 py-2 text-sm font-bold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300 border-2 border-orange-200 dark:border-orange-600">
                                    🏢 Office required
                                </span>
                            @endif
                        </dd>

                        @if ($talentRequest->work_location_type !== 'remote')
                        <div class="mt-6 pt-6 border-t border-indigo-200 dark:border-indigo-700">
                            <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Work Location</dt>
                            <dd class="flex items-center flex-wrap gap-3">
                                <div class="flex items-center text-xl font-bold text-gray-900 dark:text-white">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $talentRequest->work_location_city ?? 'N/A' }}{{ $talentRequest->work_location_city && $talentRequest->work_location_country ? ', ' : '' }}{{ $talentRequest->work_location_country ?? 'N/A' }}
                                </div>
                                @php
                                    $user = Auth::user();
                                    $sameCountry = $talentRequest->work_location_country && $user->domicile_country &&
                                                  strtolower($talentRequest->work_location_country) === strtolower($user->domicile_country);
                                    $sameCity = $talentRequest->work_location_city && $user->domicile_city &&
                                               strtolower($talentRequest->work_location_city) === strtolower($user->domicile_city);
                                @endphp
                                @if($sameCity)
                                    <span class="px-4 py-2 text-sm font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 border-2 border-green-200 dark:border-green-600">
                                        📍 Same city as you
                                    </span>
                                @elseif($sameCountry)
                                    <span class="px-4 py-2 text-sm font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 border-2 border-blue-200 dark:border-blue-600">
                                        🗺️ Same country as you
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @endif
                    </div>

                    <!-- Requested Competencies -->
                    @if ($talentRequest->competencies && $talentRequest->competencies->count() > 0)
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-8 border border-emerald-100 dark:border-emerald-700">
                        <dt class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Requested Competencies
                        </dt>
                        <dd class="space-y-3">
                            @foreach ($talentRequest->competencies as $competency)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-emerald-200 dark:border-emerald-600 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $competency->name }}
                                        </span>
                                        <span class="px-3 py-1 text-sm font-bold rounded-full
                                            @if($competency->pivot->level_required <= 3)
                                                bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                                            @elseif($competency->pivot->level_required <= 7)
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300
                                            @else
                                                bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                            @endif
                                        ">
                                            Level {{ $competency->pivot->level_required }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </dd>
                    </div>
                    @else
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-8 border border-gray-200 dark:border-gray-600 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            <p class="text-lg font-medium text-gray-500 dark:text-gray-400">No specific competencies requested</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        <!-- Action Section with Enhanced Design -->
        @if ($currentAssignmentStatus === 'pending_assignment_response' || $currentAssignmentStatus === 'direct_offer_pending')
            <div class="px-8 py-8 bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 border-t border-gray-200 dark:border-gray-600">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Respond to Your Assignment</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Please review the request details and choose your response</p>
                </div>

                <!-- Action Buttons Section -->
                @if ($currentAssignmentStatus === 'pending_assignment_response' || $currentAssignmentStatus === 'direct_offer_pending')
                <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Respond to this {{ $isDirectOffer ? 'Direct Offer' : 'Request' }}</h3>
                    <form action="{{ route('talent.requests.respond', $talentRequest->id) }}" method="POST" class="space-y-6">
                        @csrf
                        {{-- Optional: Add a comments section here in the future --}}
                        {{-- <div class="mb-6">
                            <label for="comments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comments (Optional)</label>
                            <textarea name="comments" id="comments" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"></textarea>
                        </div> --}}
                        <div class="flex flex-col sm:flex-row items-center sm:justify-end space-y-4 sm:space-y-0 sm:space-x-4">
                            <button type="submit" name="action" value="reject"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 border border-red-300 dark:border-red-600 shadow-sm text-base font-medium rounded-xl text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-700/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-900 transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Reject {{ $isDirectOffer ? 'Offer' : 'Assignment' }}
                            </button>
                            <button type="submit" name="action" value="approve"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 border border-transparent shadow-sm text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-offset-gray-900 transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Approve {{ $isDirectOffer ? 'Offer' : 'Assignment' }}
                            </button>
                        </div>
                    </form>
                </div>
                @elseif($currentAssignmentStatus === 'approved_by_talent' || $currentAssignmentStatus === 'rejected_by_talent')
                <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Your Response</h3>
                    <div class="p-6 rounded-xl
                        @if($currentAssignmentStatus === 'approved_by_talent') bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-600
                        @else bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-600 @endif">
                        <p class="text-lg font-medium
                            @if($currentAssignmentStatus === 'approved_by_talent') text-green-700 dark:text-green-200
                            @else text-red-700 dark:text-red-200 @endif">
                            You have already responded to this {{ $isDirectOffer ? 'direct offer' : 'assignment' }}. You chose to
                            <span class="font-bold">{{ $currentAssignmentStatus === 'approved_by_talent' ? 'approve' : 'reject' }}</span> it.
                        </p>
                        {{-- You can add display for comments here if they are implemented --}}
                    </div> {{-- Closes the p-6 rounded-xl div (green/red box) --}}
                </div> {{-- Closes the mt-10 pt-8 div (Your Response section) --}}
                @endif {{-- Closes the inner @if directive for action buttons/response display --}}

            </div> {{-- Closes the div for "Action Section with Enhanced Design" (the one with class starting "px-8 py-8 bg-gradient-to-r...") --}}
        @endif {{-- Closes the outer @if directive for the entire Action Section --}}

        </div> {{-- This closes "Main Content Card" (bg-white dark:bg-gray-800 shadow-2xl...) --}}
    </div> {{-- Closes container mx-auto --}}
</div> {{-- Closes min-h-screen bg-gradient-to-br --}}
</x-layouts.app>
