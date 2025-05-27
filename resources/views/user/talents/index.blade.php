<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Discover Talents
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Find and connect with talented professionals.</p>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 mb-8 ring-1 ring-gray-200 dark:ring-gray-700">
        <form method="GET" action="{{ route('user.talents.index') }}" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search Talents
                    </label>
                    <input type="text"
                           id="search"
                           name="search"
                           placeholder="Search by name, skills, or location..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           value="{{ request('search') }}">
                </div>

                <!-- Skills Filter -->
                <div>
                    <label for="competency_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Skills
                    </label>
                    <select id="competency_id"
                            name="competency_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Skills</option>
                        @foreach($competencies as $competency)
                            <option value="{{ $competency->id }}" {{ request('competency_id') == $competency->id ? 'selected' : '' }}>
                                {{ $competency->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Location
                    </label>
                    <select id="province"
                           name="province"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Locations</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-3">
                <a href="{{ route('user.talents.index') }}"
                   class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Clear Filters
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Search Talents
                </button>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    @if($talents->count() > 0)
        <div class="mb-6">
            <p class="text-gray-600 dark:text-gray-400">Found {{ $talents->total() }} talent{{ $talents->total() > 1 ? 's' : '' }}</p>
        </div>
    @endif

    <!-- Talents Grid -->
    @if($talents->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
            @foreach($talents as $talent)
                <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 transform hover:-translate-y-2">
                    <!-- Card Content -->
                    <div class="p-6 flex flex-col h-full">
                        <!-- Talent Header -->
                        <div class="flex items-center mb-6">
                            <div class="relative">
                                @if($talent->profile_picture && Storage::exists('public/' . $talent->profile_picture))
                                    <img src="{{ Storage::url($talent->profile_picture) }}" alt="{{ $talent->name }}" class="w-16 h-16 rounded-full object-cover shadow-lg ring-2 ring-blue-100 dark:ring-blue-900/50">
                                @else
                                    <div class="w-16 h-16 bgblue-500 dark:blue-600 rounded-full flex items-center justify-center shadow-lg ring-2 ring-blue-200 dark:ring-blue-800 border-2 border-blue-300 dark:border-blue-700 group-hover:scale-110 transition-transform duration-200">
                                        <span class="text-black dark:text-white font-bold text-lg tracking-wide drop-shadow-sm" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                                            {{ strtoupper(substr($talent->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $talent->name)[1] ?? '', 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <!-- Online status indicator -->
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full shadow-sm"></div>
                            </div>
                            <div class="ml-4 min-w-0 flex-1">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $talent->name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $talent->email }}</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center text-gray-600 dark:text-gray-400 mb-6 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm truncate font-medium">
                                {{ $talent->domicile_city ?? 'City not specified' }}, {{ $talent->domicile_country ?? 'Country not specified' }}
                            </span>
                        </div>

                        <!-- Competencies -->
                        <div class="mb-6 flex-grow">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Skills & Expertise
                            </h4>
                            @if($talent->competencies && $talent->competencies->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($talent->competencies->take(3) as $competency)
                                        @php
                                            $proficiencyColors = [
                                                'beginner' => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700',
                                                'intermediate' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
                                                'advanced' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700',
                                                'expert' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700'
                                            ];
                                            $colorClass = $proficiencyColors[$competency->pivot->proficiency_level ?? 'intermediate'] ?? 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $colorClass }}">
                                            {{ $competency->name }}
                                        </span>
                                    @endforeach
                                    @if($talent->competencies->count() > 3)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                            +{{ $talent->competencies->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 italic">No skills listed</p>
                            @endif
                        </div>

                        <!-- Actions - Always at bottom -->
                        <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col space-y-3">
                                <a href="{{ route('user.talents.show', $talent->id) }}"
                                   class="w-full inline-flex justify-center items-center px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Profile
                                </a>
                                <button type="button"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 text-sm font-semibold rounded-xl border-2 border-blue-600 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                                        onclick="sendRequest({{ $talent->id }})">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Send Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $talents->appends(request()->query())->links() }}
        </div>

    @else
        <!-- No Results -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">No talents found</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                We couldn't find any talents matching your criteria. Try adjusting your search filters or browsing all available talents.
            </p>
            <a href="{{ route('user.talents.index') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Browse All Talents
            </a>
        </div>
    @endif
</div>

<script>
function sendRequest(talentId) {
    // This would typically open a modal or redirect to a request form
    alert('Send request functionality to be implemented for talent ID: ' + talentId);
}
</script>
</x-layouts.app>
