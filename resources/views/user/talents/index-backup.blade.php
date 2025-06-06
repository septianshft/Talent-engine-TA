<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- MIS Header with Statistics -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Talent Management System
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Football Manager-style talent discovery and management platform</p>
        </div>
        <div class="flex flex-wrap gap-4 mt-4 lg:mt-0">
            <!-- Quick Stats -->
            <div class="bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-lg">
                <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total: {{ $stats['total'] }}</span>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 px-4 py-2 rounded-lg">
                <span class="text-sm text-green-600 dark:text-green-400 font-medium">Filtered: {{ $stats['filtered'] }}</span>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 px-4 py-2 rounded-lg">
                <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">Shortlisted: {{ $shortlistCount }}</span>
            </div>
        </div>
    </div>

    <!-- MIS Navigation -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('user.talents.index') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Talent Scout
        </a>
        <a href="{{ route('user.talents.shortlist') }}"
           class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            My Shortlist ({{ $shortlistCount }})
        </a>
        <a href="{{ route('user.talents.analytics') }}"
           class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Analytics
        </a>
    </div>

    <!-- Advanced MIS Filtering Panel -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl mb-8 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
        <!-- Filter Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                    </svg>
                    Advanced Filters
                </h2>
                <div class="flex gap-2">
                    <!-- Saved Searches Dropdown -->
                    @if($savedSearches->count() > 0)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            Saved Searches
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                            <div class="p-2">
                                @foreach($savedSearches as $savedSearch)
                                <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded">
                                    <a href="{{ route('user.talents.searches.load', $savedSearch) }}" class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $savedSearch->name }}
                                    </a>
                                    <button onclick="deleteSavedSearch({{ $savedSearch->id }})" class="text-red-500 hover:text-red-700 ml-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    <button onclick="saveCurrentSearch()" class="px-3 py-2 text-sm bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        Save Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('user.talents.index') }}" id="talent-filter-form" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Search Input -->
                <div class="lg:col-span-4">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Quick Search
                    </label>
                    <input type="text" id="search" name="search" placeholder="Name, email, or competency..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           value="{{ request('search') }}">
                </div>

                <!-- Location Filters -->
                <div class="lg:col-span-2">
                    <label for="province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Province
                    </label>
                    <select id="province" name="province" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        City
                    </label>
                    <select id="city" name="city" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Proficiency Filter -->
                <div class="lg:col-span-2">
                    <label for="min_proficiency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Min. Proficiency
                    </label>
                    <select id="min_proficiency" name="min_proficiency" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Any Level</option>
                        <option value="beginner" {{ request('min_proficiency') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('min_proficiency') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('min_proficiency') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        <option value="expert" {{ request('min_proficiency') == 'expert' ? 'selected' : '' }}>Expert</option>
                    </select>
                </div>

                <!-- Experts Only Toggle -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        &nbsp;
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="experts_only" value="1" {{ request('experts_only') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Experts Only</span>
                    </label>
                </div>
            </div>

            <!-- Multi-Competency Filter Section -->
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Competency Requirements</h3>
                <div id="competency-filters">
                    @if(request('competencies') && is_array(request('competencies')))
                        @foreach(request('competencies') as $compId => $minProf)
                            @if(!empty($compId) && !empty($minProf))
                            <div class="competency-filter-row flex gap-4 mb-3">
                                <select name="competencies[{{ $compId }}]" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Competency</option>
                                    @foreach($competencyCategories as $category => $comps)
                                        <optgroup label="{{ $category }}">
                                            @foreach($comps as $comp)
                                                <option value="{{ $comp->id }}" {{ $compId == $comp->id ? 'selected' : '' }}>
                                                    {{ $comp->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <select name="competencies[{{ $compId }}]" class="w-32 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="beginner" {{ $minProf == 'beginner' ? 'selected' : '' }}>Beginner+</option>
                                    <option value="intermediate" {{ $minProf == 'intermediate' ? 'selected' : '' }}>Intermediate+</option>
                                    <option value="advanced" {{ $minProf == 'advanced' ? 'selected' : '' }}>Advanced+</option>
                                    <option value="expert" {{ $minProf == 'expert' ? 'selected' : '' }}>Expert</option>
                                </select>
                                <button type="button" onclick="removeCompetencyFilter(this)" class="px-3 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            @endif
                        @endforeach
                    @endif
                </div>
                <button type="button" onclick="addCompetencyFilter()" class="inline-flex items-center px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Competency Filter
                </button>
            </div>

            <!-- Sort and Display Options -->
            <div class="flex flex-wrap gap-4 items-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sort By
                    </label>
                    <select id="sort_by" name="sort_by" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="competency_count" {{ request('sort_by') == 'competency_count' ? 'selected' : '' }}>Competency Count</option>
                    </select>
                </div>
                <div>
                    <label for="sort_direction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Direction
                    </label>
                    <select id="sort_direction" name="sort_direction" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>
                <div>
                    <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Per Page
                    </label>
                    <select id="per_page" name="per_page" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="12" {{ request('per_page') == '12' ? 'selected' : '' }}>12</option>
                        <option value="24" {{ request('per_page') == '24' ? 'selected' : '' }}>24</option>
                        <option value="48" {{ request('per_page') == '48' ? 'selected' : '' }}>48</option>
                    </select>
                </div>
                <div class="flex gap-3 ml-auto">
                    <a href="{{ route('user.talents.index') }}" class="px-6 py-2 text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Clear Filters
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            @if($talents->count() > 0)
                <p class="text-gray-600 dark:text-gray-400">
                    Showing {{ $talents->firstItem() }}-{{ $talents->lastItem() }} of {{ $talents->total() }} talents
                    @if(request()->hasAny(['search', 'competency_id', 'competencies', 'province', 'city', 'min_proficiency', 'experts_only']))
                        <span class="text-blue-600 dark:text-blue-400">(filtered)</span>
                    @endif
                </p>
            @endif
        </div>
        <div class="flex gap-2 mt-2 sm:mt-0">
            <!-- Compare Button -->
            <button id="compare-selected" onclick="compareSelected()" disabled
                    class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg transition-colors disabled:cursor-not-allowed">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l3-3l3 3v13M9 19H6a2 2 0 01-2-2V9a2 2 0 012-2h3m4 0h3a2 2 0 012 2v8a2 2 0 01-2 2h-3M9 19v-6"></path>
                </svg>
                Compare (<span id="selected-count">0</span>)
            </button>
            <!-- View Mode Toggle -->
            <div class="flex rounded-lg border border-gray-300 dark:border-gray-600">
                <button onclick="setViewMode('grid')" id="grid-view"
                        class="px-3 py-2 bg-blue-600 text-white rounded-l-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button onclick="setViewMode('list')" id="list-view"
                        class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Talents Display -->
    @if($talents->count() > 0)
        <!-- Grid View -->
        <div id="talents-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
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
                                         class="w-16 h-16 rounded-full object-cover shadow-lg ring-2 ring-blue-100 dark:ring-blue-900/50">
                                @else
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-full flex items-center justify-center shadow-lg ring-2 ring-blue-200 dark:ring-blue-800 group-hover:scale-110 transition-transform duration-200">
                                        <span class="text-white font-bold text-lg tracking-wide drop-shadow-sm">
                                            {{ strtoupper(substr($talent->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $talent->name)[1] ?? '', 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <!-- Rating/Performance Indicator -->
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-gradient-to-r from-green-400 to-green-500 border-2 border-white dark:border-gray-800 rounded-full shadow-sm flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">{{ $talent->competencies->count() }}</span>
                                </div>
                            </div>
                            <div class="ml-4 min-w-0 flex-1">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $talent->name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $talent->email }}</p>
                            </div>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-xl text-center">
                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $talent->competencies->count() }}</div>
                                <div class="text-xs text-blue-500 dark:text-blue-300">Skills</div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-xl text-center">
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($talent->competencies->avg('pivot.proficiency_level') ?? 0, 1) }}
                                </div>
                                <div class="text-xs text-green-500 dark:text-green-300">Avg Level</div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center text-gray-600 dark:text-gray-400 mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm truncate font-medium">
                                {{ $talent->domicile_city ?? 'Unknown' }}, {{ $talent->domicile_country ?? 'Unknown' }}
                            </span>
                        </div>

                        <!-- Top Competencies -->
                        <div class="mb-6 flex-grow">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Top Skills
                            </h4>
                            @if($talent->competencies && $talent->competencies->count() > 0)
                                <div class="space-y-2">
                                    @foreach($talent->competencies->take(3) as $competency)
                                        @php
                                            $proficiencyLevel = $competency->pivot->proficiency_level ?? 2;
                                            $proficiencyText = ['', 'Beginner', 'Intermediate', 'Advanced', 'Expert'][$proficiencyLevel] ?? 'Intermediate';
                                            $proficiencyColors = [
                                                1 => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                2 => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300',
                                                3 => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300',
                                                4 => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300'
                                            ];
                                            $colorClass = $proficiencyColors[$proficiencyLevel] ?? $proficiencyColors[2];
                                        @endphp
                                        <div class="flex justify-between items-center p-2 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $competency->name }}</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border {{ $colorClass }} ml-2">
                                                {{ $proficiencyText }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($talent->competencies->count() > 3)
                                        <div class="text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                +{{ $talent->competencies->count() - 3 }} more
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 italic">No skills listed</p>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex gap-2">
                                <a href="{{ route('user.talents.show', $talent->id) }}"
                                   class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <button onclick="sendRequest({{ $talent->id }})"
                                        class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 text-sm font-medium rounded-lg border-2 border-blue-600 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-gray-600 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- List View (Hidden by default) -->
        <div id="talents-list" class="hidden space-y-4 mb-12">
            @foreach($talents as $talent)
                <div class="talent-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
                     data-talent-id="{{ $talent->id }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
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
                                </div>

                                <!-- Basic Info -->
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $talent->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $talent->email }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $talent->domicile_city ?? 'Unknown' }}, {{ $talent->domicile_country ?? 'Unknown' }}
                                    </p>
                                </div>

                                <!-- Skills Preview -->
                                <div class="flex flex-wrap gap-1">
                                    @foreach($talent->competencies->take(3) as $competency)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $competency->name }}
                                        </span>
                                    @endforeach
                                    @if($talent->competencies->count() > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            +{{ $talent->competencies->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-3">
                                <!-- Stats -->
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $talent->competencies->count() }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Skills</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ number_format($talent->competencies->avg('pivot.proficiency_level') ?? 0, 1) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Avg Level</div>
                                </div>

                                <!-- Action Buttons -->
                                <button onclick="toggleShortlist({{ $talent->id }}, this)"
                                        class="shortlist-btn p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        data-shortlisted="false">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                                <a href="{{ route('user.talents.show', $talent->id) }}"
                                   class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    View Profile
                                </a>
                                <button onclick="sendRequest({{ $talent->id }})"
                                        class="px-4 py-2 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 text-sm font-medium rounded-lg border border-blue-600 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-gray-600 transition-colors">
                                    Request
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
            <a href="{{ route('user.talents.index') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Explore All Talents
            </a>
        </div>
    @endif
</div>

<!-- Save Search Modal -->
<div id="save-search-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Save Current Search</h3>
            <form id="save-search-form">
                <div class="mb-4">
                    <label for="search-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search Name *
                    </label>
                    <input type="text" id="search-name" name="name" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="e.g., Senior PHP Developers">
                </div>
                <div class="mb-6">
                    <label for="search-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description (Optional)
                    </label>
                    <textarea id="search-description" name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Brief description of this search..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeSaveSearchModal()"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Save Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced JavaScript for MIS functionality -->
<script>
// Global variables
let selectedTalents = new Set();
let currentViewMode = 'grid';

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateCompareButton();

    // Auto-submit form on filter changes for real-time filtering
    const form = document.getElementById('talent-filter-form');
    const inputs = form.querySelectorAll('select, input[type="checkbox"]');

    inputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.type !== 'text') {
                form.submit();
            }
        });
    });

    // Debounced search input
    const searchInput = document.getElementById('search');
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            form.submit();
        }, 500);
    });
});

// View mode management
function setViewMode(mode) {
    currentViewMode = mode;
    const gridView = document.getElementById('talents-grid');
    const listView = document.getElementById('talents-list');
    const gridButton = document.getElementById('grid-view');
    const listButton = document.getElementById('list-view');

    if (mode === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridButton.classList.add('bg-blue-600', 'text-white');
        gridButton.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        listButton.classList.remove('bg-blue-600', 'text-white');
        listButton.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listButton.classList.add('bg-blue-600', 'text-white');
        listButton.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        gridButton.classList.remove('bg-blue-600', 'text-white');
        gridButton.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    }
}

// Talent selection for comparison
function updateCompareButton() {
    const checkboxes = document.querySelectorAll('.talent-select:checked');
    const compareButton = document.getElementById('compare-selected');
    const countSpan = document.getElementById('selected-count');

    selectedTalents.clear();
    checkboxes.forEach(checkbox => {
        selectedTalents.add(checkbox.value);
    });

    countSpan.textContent = selectedTalents.size;

    if (selectedTalents.size >= 2 && selectedTalents.size <= 4) {
        compareButton.disabled = false;
        compareButton.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        compareButton.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
    } else {
        compareButton.disabled = true;
        compareButton.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        compareButton.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
    }
}

// Compare selected talents
function compareSelected() {
    if (selectedTalents.size >= 2 && selectedTalents.size <= 4) {
        const talentIds = Array.from(selectedTalents).join(',');
        window.open(`{{ route('user.talents.compare') }}?talents=${talentIds}`, '_blank');
    }
}

// Multi-competency filter management
function addCompetencyFilter() {
    const container = document.getElementById('competency-filters');
    const timestamp = Date.now();

    const filterRow = document.createElement('div');
    filterRow.className = 'competency-filter-row flex gap-4 mb-3';

    // Create competency select
    const competencySelect = document.createElement('select');
    competencySelect.name = `competencies[${timestamp}]`;
    competencySelect.className = 'flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white';

    // Add default option
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Select Competency';
    competencySelect.appendChild(defaultOption);

    // Add competency options (you may need to pass these from the server)
    @foreach($competencyCategories as $category => $comps)
        const optgroup{{ $loop->index }} = document.createElement('optgroup');
        optgroup{{ $loop->index }}.label = '{{ $category }}';
        @foreach($comps as $comp)
            const option{{ $loop->parent->index }}_{{ $loop->index }} = document.createElement('option');
            option{{ $loop->parent->index }}_{{ $loop->index }}.value = '{{ $comp->id }}';
            option{{ $loop->parent->index }}_{{ $loop->index }}.textContent = '{{ $comp->name }}';
            optgroup{{ $loop->parent->index }}.appendChild(option{{ $loop->parent->index }}_{{ $loop->index }});
        @endforeach
        competencySelect.appendChild(optgroup{{ $loop->index }});
    @endforeach

    // Create proficiency select
    const proficiencySelect = document.createElement('select');
    proficiencySelect.name = `competency_levels[${timestamp}]`;
    proficiencySelect.className = 'w-32 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white';

    const levels = [
        { value: 'beginner', text: 'Beginner+' },
        { value: 'intermediate', text: 'Intermediate+', selected: true },
        { value: 'advanced', text: 'Advanced+' },
        { value: 'expert', text: 'Expert' }
    ];

    levels.forEach(level => {
        const option = document.createElement('option');
        option.value = level.value;
        option.textContent = level.text;
        if (level.selected) option.selected = true;
        proficiencySelect.appendChild(option);
    });

    // Create remove button
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'px-3 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors';
    removeButton.onclick = function() { removeCompetencyFilter(this); };
    removeButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';

    // Assemble the row
    filterRow.appendChild(competencySelect);
    filterRow.appendChild(proficiencySelect);
    filterRow.appendChild(removeButton);

    container.appendChild(filterRow);
}

function removeCompetencyFilter(button) {
    button.closest('.competency-filter-row').remove();
}

// Save search functionality
function saveCurrentSearch() {
    document.getElementById('save-search-modal').classList.remove('hidden');
}

function closeSaveSearchModal() {
    document.getElementById('save-search-modal').classList.add('hidden');
    document.getElementById('save-search-form').reset();
}

// Handle save search form submission
document.getElementById('save-search-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const currentUrl = new URL(window.location);

    // Add current search parameters
    for (const [key, value] of currentUrl.searchParams) {
        formData.append(key, value);
    }

    fetch('{{ route("user.talents.searches.save") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeSaveSearchModal();
            showNotification('Search saved successfully!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('Failed to save search', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
});

// Delete saved search
function deleteSavedSearch(searchId) {
    if (!confirm('Are you sure you want to delete this saved search?')) {
        return;
    }

    fetch(`/talents/searches/${searchId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Search deleted successfully!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('Failed to delete search', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Shortlist management
function toggleShortlist(talentId, button) {
    const isShortlisted = button.dataset.shortlisted === 'true';
    const method = isShortlisted ? 'DELETE' : 'POST';
    const url = `/talents/${talentId}/shortlist`;

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            list_name: 'default',
            priority: 0
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.dataset.shortlisted = isShortlisted ? 'false' : 'true';
            const icon = button.querySelector('svg');

            if (isShortlisted) {
                icon.classList.remove('text-red-500');
                icon.classList.add('text-gray-400');
                icon.setAttribute('fill', 'none');
            } else {
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-red-500');
                icon.setAttribute('fill', 'currentColor');
            }

            showNotification(data.message, 'success');
        } else {
            showNotification('Failed to update shortlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Send talent request
function sendRequest(talentId) {
    window.location.href = `{{ url('requests/create-direct') }}/${talentId}`;
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    notification.classList.add(bgColor, 'text-white');
    notification.textContent = message;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>

<!-- Add Alpine.js for dropdown functionality -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</x-layouts.app>
