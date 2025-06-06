<!-- Talent List View -->
@if($talents->isNotEmpty())
    <div class="space-y-4">
        @foreach($talents as $talent)
            <div class="talent-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-300"
                 data-talent-id="{{ $talent->id }}">

                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <!-- Selection -->
                        <input type="checkbox" class="talent-select w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500"
                               value="{{ $talent->id }}" onchange="updateCompareButton()">

                        <!-- Avatar -->
                        <div class="relative">
                            @if($talent->profile_picture && Storage::exists('public/' . $talent->profile_picture))
                                <img src="{{ Storage::url($talent->profile_picture) }}" alt="{{ $talent->name }}"
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">
                                        {{ strtoupper(substr($talent->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $talent->name)[1] ?? '', 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></div>
                        </div>

                        <!-- Talent Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $talent->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $talent->email }}</p>
                                    @if($talent->domicile_city && $talent->domicile_country)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center mt-1">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $talent->domicile_city }}, {{ $talent->domicile_country }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Performance Metrics -->
                                <div class="flex items-center space-x-6 ml-4">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $talent->competencies->count() }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Skills</div>
                                    </div>
                                    <div class="text-center">
                                        @php
                                            $avgProficiency = $talent->competencies->avg('pivot.proficiency_level');
                                        @endphp
                                        <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($avgProficiency, 1) }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Avg Level</div>
                                    </div>
                                    <div class="text-center">
                                        @php
                                            $expertCount = $talent->competencies->where('pivot.proficiency_level', 4)->count();
                                        @endphp
                                        <div class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $expertCount }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Expert</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Competency Tags -->
                            <div class="mt-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($talent->competencies->sortByDesc('pivot.proficiency_level')->take(6) as $competency)
                                        @php
                                            $proficiencyColors = [1 => 'red', 2 => 'yellow', 3 => 'blue', 4 => 'green'];
                                            $level = $competency->pivot->proficiency_level;
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-{{ $proficiencyColors[$level] }}-100 text-{{ $proficiencyColors[$level] }}-800 dark:bg-{{ $proficiencyColors[$level] }}-900 dark:text-{{ $proficiencyColors[$level] }}-200">
                                            {{ $competency->name }}
                                            <span class="ml-1 flex">
                                                @for($i = 1; $i <= $level; $i++)
                                                    <div class="w-1 h-1 bg-{{ $proficiencyColors[$level] }}-600 rounded-full ml-0.5"></div>
                                                @endfor
                                            </span>
                                        </span>
                                    @endforeach
                                    @if($talent->competencies->count() > 6)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            +{{ $talent->competencies->count() - 6 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-3">
                            <!-- Shortlist Button -->
                            <button onclick="toggleShortlist({{ $talent->id }}, this)"
                                    class="shortlist-btn p-2 text-gray-400 hover:text-red-500 transition-colors"
                                    data-shortlisted="false">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('user.talents.show', $talent) }}"
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('user.requests.create', ['talent_id' => $talent->id]) }}"
                                   class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Request
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- No Results State -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">No talents found</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
            We couldn't find any talents matching your criteria. Try adjusting your filters or explore all available talents.
        </p>
        <button onclick="clearAllFilters()"
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Clear Filters
        </button>
    </div>
@endif
