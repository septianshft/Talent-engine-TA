<!-- Talent Cards Grid -->
@if($talents->isNotEmpty())
    @foreach($talents as $talent)
        <div class="talent-card group bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 transform hover:-translate-y-1"
             data-talent-id="{{ $talent->id }}">

            <!-- Selection Checkbox -->
            <div class="absolute top-4 left-4 z-10">
                <input type="checkbox" class="talent-select w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500"
                       value="{{ $talent->id }}" onchange="updateCompareButton()">
            </div>

            <!-- Shortlist Button -->
            <div class="absolute top-4 right-4 z-10">
                <button onclick="toggleShortlist({{ $talent->id }}, this)"
                        class="shortlist-btn p-2 bg-white/90 dark:bg-gray-800/90 rounded-full shadow-md hover:bg-white dark:hover:bg-gray-800 transition-colors"
                        data-shortlisted="false">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </button>
            </div>

            <!-- Card Content -->
            <div class="p-6 flex flex-col h-full">
                <!-- Talent Header -->
                <div class="flex items-center mb-6 mt-4">
                    <div class="relative">
                        @if($talent->profile_picture && Storage::exists('public/' . $talent->profile_picture))
                            <img src="{{ Storage::url($talent->profile_picture) }}" alt="{{ $talent->name }}"
                                 class="w-16 h-16 rounded-full object-cover">
                        @else
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium text-lg">
                                    {{ strtoupper(substr($talent->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $talent->name)[1] ?? '', 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        <!-- Online Status Indicator -->
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></div>
                    </div>

                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
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
                </div>

                <!-- Performance Metrics -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $talent->competencies->count() }}</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">Skills</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                        @php
                            $avgProficiency = $talent->competencies->avg('pivot.proficiency_level');
                        @endphp
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($avgProficiency, 1) }}</div>
                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">Avg Level</div>
                    </div>
                </div>

                <!-- Competency Tags -->
                <div class="flex-1 mb-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Top Competencies:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($talent->competencies->sortByDesc('pivot.proficiency_level')->take(4) as $competency)
                            @php
                                $proficiencyColors = [1 => 'red', 2 => 'yellow', 3 => 'blue', 4 => 'green'];
                                $level = $competency->pivot->proficiency_level;
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $proficiencyColors[$level] }}-100 text-{{ $proficiencyColors[$level] }}-800 dark:bg-{{ $proficiencyColors[$level] }}-900 dark:text-{{ $proficiencyColors[$level] }}-200">
                                {{ $competency->name }}
                                <span class="ml-1 flex">
                                    @for($i = 1; $i <= $level; $i++)
                                        <div class="w-1 h-1 bg-{{ $proficiencyColors[$level] }}-600 rounded-full ml-0.5"></div>
                                    @endfor
                                </span>
                            </span>
                        @endforeach
                        @if($talent->competencies->count() > 4)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                +{{ $talent->competencies->count() - 4 }} more
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-auto">
                    <a href="{{ route('user.talents.show', $talent) }}"
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg font-medium transition-colors text-sm">
                        View Profile
                    </a>
                    <a href="{{ route('user.requests.create', ['talent_id' => $talent->id]) }}"
                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2.5 px-4 rounded-lg font-medium transition-colors text-sm">
                        Request
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@else
    <!-- No Results State -->
    <div class="col-span-full">
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
    </div>
@endif
