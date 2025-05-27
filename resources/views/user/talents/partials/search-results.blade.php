@if($talents->count() > 0)
    <!-- Results Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            Found {{ $talents->total() }} talent(s)
        </h2>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing {{ $talents->firstItem() }}-{{ $talents->lastItem() }} of {{ $talents->total() }} results
        </div>
    </div>

    <!-- Talents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($talents as $talent)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                <div class="p-6">
                    <!-- Talent Header -->
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                {{ $talent->initials() }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $talent->name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $talent->email }}
                            </p>
                        </div>
                    </div>                    <!-- Location -->
                    @if($talent->domicile_country || $talent->domicile_city)
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $talent->domicile_city ? $talent->domicile_city . ', ' : '' }}{{ $talent->domicile_country }}
                        </div>
                    @endif

                    <!-- Competencies -->
                    @if($talent->competencies && $talent->competencies->count() > 0)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Skills & Competencies
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($talent->competencies->take(3) as $competency)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $competency->name }}
                                        @if($competency->pivot->proficiency_level)
                                            <span class="ml-1 text-blue-600 dark:text-blue-400">
                                                ({{ ['', 'Beginner', 'Intermediate', 'Advanced', 'Expert'][$competency->pivot->proficiency_level] }})
                                            </span>
                                        @endif
                                    </span>
                                @endforeach
                                @if($talent->competencies->count() > 3)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        +{{ $talent->competencies->count() - 3 }} more
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex space-x-3">
                        <a href="{{ route('user.talents.show', $talent) }}"
                           class="flex-1 inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 rounded-lg transition-all duration-300 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Profile
                        </a>
                        <a href="{{ route('user.requests.create-direct', $talent) }}"
                           class="flex-1 inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-purple-700 bg-purple-100 hover:bg-purple-200 focus:ring-4 focus:outline-none focus:ring-purple-300 dark:bg-purple-900 dark:text-purple-300 dark:hover:bg-purple-800 dark:focus:ring-purple-800 rounded-lg transition-all duration-300 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Request
                        </a>
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
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No talents found</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Try adjusting your search criteria or filters.</p>
        <a href="{{ route('user.talents.index') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-purple-700 bg-purple-100 hover:bg-purple-200 focus:ring-4 focus:outline-none focus:ring-purple-300 dark:bg-purple-900 dark:text-purple-300 dark:hover:bg-purple-800 dark:focus:ring-purple-800 rounded-lg transition-all duration-300 ease-in-out">
            Clear Filters
        </a>
    </div>
@endif
