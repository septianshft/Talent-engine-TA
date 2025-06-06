<x-layouts.app>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 space-y-4 sm:space-y-0">
            <div class="text-left">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📄 Talent Request Details</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Review the details of the talent request and assign talents.</p>
            </div>
            <a href="{{ route('admin.talent-requests.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors text-sm font-medium">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Requests
            </a>
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



        {{-- Notice for Direct Offer Pending --}}
        @if($hasDirectOfferPending)
            <div class="bg-blue-100 dark:bg-blue-800/30 border border-blue-400 dark:border-blue-600 text-blue-700 dark:text-blue-300 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
                <p class="font-bold">Direct Offer Pending:</p>
                <p>A direct offer has been made to a talent by the requester. You cannot assign other talents until the talent responds to this offer.</p>
            </div>
        @endif

        {{-- Notice for Rejected Direct Offer --}}
        @if($isPendingAdminAfterDirectOfferRejection)
            <div class="bg-orange-100 dark:bg-orange-800/30 border border-orange-400 dark:border-orange-600 text-orange-700 dark:text-orange-300 px-4 py-3 rounded-lg relative mb-6 shadow-sm" role="alert">
                <p class="font-bold">Action Required: Direct Offer Rejected</p>
                <p>The direct offer made by the requester was rejected by the talent. This request is now pending your review. You can proceed to assign talents using the talent assignment system below or take other actions.</p>
            </div>
        @endif

        <!-- Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700 mb-10">
            <div class="px-6 py-8 sm:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requester</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $talentRequest->requestingUser->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requester Phone</dt>
                        <dd class="mt-1 text-lg text-gray-700 dark:text-gray-300">{{ $talentRequest->requestingUser->phone_number ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requester Domicile</dt>
                        <dd class="mt-1 text-lg text-gray-700 dark:text-gray-300">
                            {{ $talentRequest->requestingUser->domicile_city ?? 'N/A' }}@if($talentRequest->requestingUser->domicile_city && $talentRequest->requestingUser->domicile_country), @endif{{ $talentRequest->requestingUser->domicile_country ?? '' }}
                        </dd>
                    </div>

                    {{-- section status --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1 flex items-center">
                            @php
                                $statusConfig = [
                                    'pending_admin' => ['color' => 'yellow', 'icon' => '⏳', 'label' => 'Pending Admin Review'],
                                    'pending_talent' => ['color' => 'blue', 'icon' => '👤', 'label' => 'Pending Talent Response'],
                                    'approved' => ['color' => 'green', 'icon' => '✅', 'label' => 'Approved'],
                                    'rejected_admin' => ['color' => 'red', 'icon' => '❌', 'label' => 'Rejected by Admin'],
                                    'rejected_talent' => ['color' => 'red', 'icon' => '❌', 'label' => 'Rejected by Talent'],
                                    'completed' => ['color' => 'purple', 'icon' => '🎉', 'label' => 'Completed']
                                ];
                                $config = $statusConfig[$talentRequest->status] ?? ['color' => 'gray', 'icon' => '📋', 'label' => ucfirst(str_replace('_', ' ', $talentRequest->status))];
                            @endphp

                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                @if($config['color'] === 'yellow') bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-300 dark:border-yellow-800/50
                                @elseif($config['color'] === 'blue') bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800/50
                                @elseif($config['color'] === 'green') bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800/50
                                @elseif($config['color'] === 'red') bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800/50
                                @elseif($config['color'] === 'purple') bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-900/20 dark:text-purple-300 dark:border-purple-800/50
                                @else bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-900/20 dark:text-gray-300 dark:border-gray-800/50
                                @endif
                            ">
                                <span class="mr-2">{{ $config['icon'] }}</span>
                                {{ $config['label'] }}
                                @if($isPendingAdminAfterDirectOfferRejection && $talentRequest->status === 'pending_admin')
                                    <span class="ml-2 text-xs font-normal text-orange-600 dark:text-orange-400">(Rejected Direct Offer)</span>
                                @endif
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested On</dt>
                        <dd class="mt-1 text-lg text-gray-700 dark:text-gray-300">{{ $talentRequest->created_at->format('M d, Y H:i') }}</dd>
                    </div>

                    {{-- section work location type --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Work Location Type</dt>
                        <dd class="mt-1 flex items-center">
                            <span class="text-lg text-gray-700 dark:text-gray-300">{{ Str::title(str_replace('_', ' ', $talentRequest->work_location_type)) }}</span>
                            @if($talentRequest->work_location_type === 'remote')
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">🌍 Global</span>
                            @elseif($talentRequest->work_location_type === 'hybrid')
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">🏢🏠 Flexible</span>
                            @else
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">🏢 On-site</span>
                            @endif
                        </dd>
                    </div>

                    {{-- section work location --}}
                    @if ($talentRequest->work_location_type !== 'remote')
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Work Location</dt>
                        <dd class="mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-lg text-gray-700 dark:text-gray-300">
                                {{ $talentRequest->work_location_city ?? 'N/A' }}{{ $talentRequest->work_location_city && $talentRequest->work_location_country ? ', ' : '' }}{{ $talentRequest->work_location_country ?? 'N/A' }}
                            </span>
                        </dd>
                    </div>
                    @endif

                    {{-- section requested competencies --}}
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Required Competencies</dt>
                        <dd class="mt-1">
                            @forelse ($talentRequest->competencies as $competency)
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-3 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                                                {{ $competency->name }}
                                            </h4>
                                            <div class="flex items-center space-x-6">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">Required Level:</span>
                                                    <span class="px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-sm font-medium">
                                                        {{ $competency->pivot->required_proficiency_level }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">Weight:</span>
                                                    <span class="px-2 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 rounded text-sm font-medium">
                                                        {{ $competency->pivot->weight }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-600 dark:text-gray-400 italic">No specific competencies listed for this request.</p>
                            @endforelse
                        </dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Request Details</dt>
                        <dd class="mt-1 text-gray-700 dark:text-gray-300 prose dark:prose-invert max-w-none">
                            {!! nl2br(e($talentRequest->details)) !!}
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        {{-- Talent Assignment Section --}}
        {{-- Disable assignment if a direct offer is pending AND it has NOT been rejected --}}
        @if($hasDirectOfferPending && !$isPendingAdminAfterDirectOfferRejection)
            <div class="bg-gray-100 dark:bg-gray-800/50 p-6 rounded-lg shadow text-center">
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Talent Assignment Disabled</h3>
                <p class="text-gray-600 dark:text-gray-400">Assignment is currently disabled because a direct offer is pending talent response.</p>
            </div>
        @else
            {{-- Show talent assignment form if no direct offer is pending, OR if a direct offer was made but then rejected --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700 mb-10">
                <div class="px-6 py-8 sm:px-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">🎯 Assign Talents</h2>

                    @if ($rankedTalents->isNotEmpty())
                        <form action="{{ route('admin.talent-requests.assign', $talentRequest) }}" method="POST">
                            @csrf
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($rankedTalents as $talent)
                                        <li class="px-6 py-5 sm:px-8 @if(!$hasDirectOfferPending) hover:bg-gray-50 dark:hover:bg-gray-700/50 @endif transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="talent_ids[]" value="{{ $talent['talent']->id }}" id="talent_{{ $talent['talent']->id }}"
                                                           class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-indigo-600 dark:ring-offset-gray-800"
                                                           @if($talentRequest->assignedTalents->contains($talent['talent']->id)) checked @endif
                                                           @if($hasDirectOfferPending) disabled @endif>
                                                    <label for="talent_{{ $talent['talent']->id }}" class="ml-3 min-w-0 flex-1">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <p class="text-lg font-medium text-indigo-600 dark:text-indigo-400 truncate">{{ $talent['talent']->name }}</p>
                                                            <div class="flex items-center space-x-2">
                                                                <!-- Simple Scoring Display -->
                                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300">
                                                                    Score: {{ number_format($talent['total_score'], 2) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate mb-3">{{ $talent['talent']->email }}</p>

                                                        <!-- Competencies Display -->
                                                        <div class="flex flex-wrap gap-2 mt-2 mb-3">
                                                            @foreach ($talent['talent']->competencies as $competency)
                                                                @php
                                                                    $scoreContribution = isset($talent['score_breakdown'][$competency->name])
                                                                        ? $talent['score_breakdown'][$competency->name]['contribution'] ?? 0
                                                                        : 0;
                                                                @endphp
                                                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-blue-100 dark:bg-blue-800/50 text-blue-800 dark:text-blue-300"
                                                                      title="Level {{ $competency->pivot->proficiency_level }} | Contribution: {{ number_format($scoreContribution, 3) }}">
                                                                    {{ $competency->name }} (L{{ $competency->pivot->proficiency_level }})
                                                                    @if($scoreContribution > 0)
                                                                        <span class="ml-1 text-xs opacity-75">+{{ number_format($scoreContribution, 2) }}</span>
                                                                    @endif
                                                                </span>
                                                            @endforeach
                                                        </div>

                                                        <!-- Location Information -->
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                                            <div class="flex items-center space-x-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                </svg>
                                                                <span class="font-medium">Location:</span>
                                                                <span>{{ $talent['talent']->domicile_city ?? 'N/A' }}{{ $talent['talent']->domicile_city && $talent['talent']->domicile_country ? ', ' : '' }}{{ $talent['talent']->domicile_country ?? 'N/A' }}</span>
                                                                @if(isset($talent['location_score']))
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                                        @if($talent['location_score'] >= 0.7) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                                        @elseif($talent['location_score'] >= 0.4) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                                                        @endif
                                                                    ">
                                                                        {{ number_format($talent['location_score'] * 100, 0) }}% compatible
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                @if (($talentRequest->status === 'pending_admin' || $talentRequest->status === 'pending_talent') && !$hasDirectOfferPending)
                                <div class="px-6 py-5 sm:px-8 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                        Assign/Update Selected Talents
                                    </button>
                                </div>
                                @endif
                            </div>
                        </form>
                    @else
                        @if (!$hasDirectOfferPending) {{-- Only show 'no suitable talents' if not blocked by direct offer --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700 px-6 py-8 sm:px-8">
                            <p class="text-center text-gray-500 dark:text-gray-400">No suitable talents found based on the required competencies.</p>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        {{-- Action Buttons for Admin (Reject/Complete) --}}
        {{-- ...existing code... --}}
    </div>
</x-layouts.app>
