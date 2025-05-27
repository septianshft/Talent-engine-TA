<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
                <a href="{{ route('user.talents.index') }}"
                   class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-200">
                    Temukan Talent
                </a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-900 dark:text-white font-medium">{{ $talent->name }}</span>
            </nav>

            <!-- Back Button -->
            <div class="mb-8">
                <a href="{{ route('user.talents.index') }}"
                   class="inline-flex items-center px-4 py-2 text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 rounded-[1rem] transition-all duration-200 group">
                    <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Talent Discovery
                </a>
            </div>

        <!-- Enhanced Talent Profile Card with floating effect -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-[2rem] overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700 transform hover:scale-[1.02] transition-all duration-300">
            <!-- Header Section with enhanced gradient and animations -->
            <div class="relative bg-gradient-to-br from-purple-600 via-blue-600 to-indigo-600 px-6 sm:px-8 py-10 sm:py-12 overflow-hidden">
                <!-- Background decorative elements -->
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-transparent"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full transform translate-x-32 -translate-y-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-400/20 rounded-full transform -translate-x-24 translate-y-24"></div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center lg:items-start space-y-6 lg:space-y-0 lg:space-x-8">
                    <!-- Enhanced Avatar with animation -->
                    <div class="flex-shrink-0 group">
                        <div class="relative">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 bg-white bg-opacity-90 rounded-[2rem] flex items-center justify-center text-purple-700 font-bold text-2xl sm:text-3xl shadow-2xl transform group-hover:scale-110 transition-transform duration-300 ring-4 ring-white/30">
                                {{ $talent->initials() }}
                            </div>
                            <!-- Online status indicator -->
                            <div class="absolute bottom-1 right-1 sm:bottom-2 sm:right-2 w-5 h-5 sm:w-6 sm:h-6 bg-green-400 rounded-[1rem] border-3 sm:border-4 border-white shadow-lg animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Enhanced Basic Info -->
                    <div class="flex-1 text-center lg:text-left space-y-4">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-2 sm:mb-3 tracking-tight leading-tight">{{ $talent->name }}</h1>
                        <p class="text-blue-100 text-lg sm:text-xl font-medium">{{ $talent->email }}</p>

                        <!-- Enhanced badges container -->
                        <div class="flex flex-col sm:flex-row items-center lg:items-start gap-3 sm:gap-4">
                            <!-- Enhanced location with icon -->
                            @if($talent->domicile_country || $talent->domicile_city)
                                <div class="inline-flex items-center text-blue-100 bg-white/20 rounded-[1.5rem] px-4 py-2 backdrop-blur-sm">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="font-medium text-sm sm:text-base">{{ $talent->domicile_city ? $talent->domicile_city . ', ' : '' }}{{ $talent->domicile_country }}</span>
                                </div>
                            @endif

                            <!-- Skills count badge -->
                            @if($talent->competencies && $talent->competencies->count() > 0)
                                <div class="inline-flex items-center text-white bg-white/20 rounded-[1.5rem] px-4 py-2 backdrop-blur-sm">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    <span class="font-medium text-sm sm:text-base">{{ $talent->competencies->count() }} {{ Str::plural('Skill', $talent->competencies->count()) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Enhanced Action Buttons with better responsive layout -->
                    <div class="flex-shrink-0 flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3 w-full sm:w-auto lg:w-full xl:w-auto">

                        <!-- Main CTA Button - Direct Request -->
                        <a href="{{ route('user.requests.create-direct', $talent->id) }}"
                           class="group inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 focus:ring-opacity-50 rounded-xl sm:rounded-2xl transition-all duration-300 ease-in-out shadow-2xl hover:shadow-3xl transform hover:-translate-y-1">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Send Request
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enhanced Content Section -->
            <div class="p-6 sm:p-8">
                <!-- Enhanced Profile Information with better spacing -->
                <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 lg:gap-8">
                    <!-- Main Content Area - takes more space -->
                    <div class="xl:col-span-3 space-y-8 lg:space-y-10">
                        <!-- Enhanced Contact Information -->
                        <div class="group">
                            <div class="flex items-center mb-6">                                        <div class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 dark:bg-purple-900/50 rounded-[1.5rem] mr-3 sm:mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Contact Information</h2>
                            </div>

                            <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-[2rem] p-6 sm:p-8 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            Full Name
                                        </label>
                                        <p class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                                            {{ $talent->name }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            Email Address
                                        </label>
                                        <div class="flex items-center space-x-3">
                                            <p class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white break-all">
                                                {{ $talent->email }}
                                            </p>
                                            <a href="mailto:{{ $talent->email }}"
                                               class="text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200 flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                    @if($talent->phone)
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                Phone Number
                                            </label>
                                            <div class="flex items-center space-x-3">
                                                <p class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                                                    {{ $talent->phone }}
                                                </p>
                                                <a href="tel:{{ $talent->phone }}"
                                                   class="text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200 flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if($talent->domicile_country || $talent->domicile_city)
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                Location
                                            </label>
                                            <p class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $talent->domicile_city ? $talent->domicile_city . ', ' : '' }}{{ $talent->domicile_country }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Skills & Competencies Section -->
                        @if($talent->competencies && $talent->competencies->count() > 0)
                            <div class="group">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                                    <div class="flex items-center">
                                        <div class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 dark:bg-blue-900/50 rounded-[1.5rem] mr-3 sm:mr-4 flex-shrink-0">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400 rounded" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Skills & Competencies</h2>
                                            <span class="inline-block mt-1 px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 text-xs sm:text-sm font-semibold rounded-[1rem]">
                                                {{ $talent->competencies->count() }} {{ Str::plural('Skill', $talent->competencies->count()) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Skills Filter Toggle -->
                                    <div class="flex items-center">
                                        <button onclick="toggleSkillsView()" id="skillsViewToggle"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-[1rem] transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                            </svg>
                                            Grid View
                                        </button>
                                    </div>
                                </div>

                                <!-- Skills Container -->
                                <div id="skillsContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 transition-all duration-300">
                                    @foreach($talent->competencies as $competency)
                                        <div class="skill-card group/card bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-[1.5rem] p-4 sm:p-6 hover:shadow-lg hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300 transform hover:-translate-y-1"
                                             data-proficiency="{{ $competency->pivot->proficiency_level ?? 0 }}">
                                            <div class="flex items-start justify-between mb-3 sm:mb-4">
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover/card:text-purple-600 dark:group-hover/card:text-purple-400 transition-colors">
                                                        {{ $competency->name }}
                                                    </h3>
                                                    @if($competency->description)
                                                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                                            {{ Str::limit($competency->description, 100) }}
                                                            @if(strlen($competency->description) > 100)
                                                                <button onclick="toggleDescription(this)" class="text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 font-medium ml-1">
                                                                    Read more
                                                                </button>
                                                                <span class="hidden full-description">{{ $competency->description }}</span>
                                                            @endif
                                                        </p>
                                                    @endif
                                                </div>
                                                @if($competency->pivot->proficiency_level)
                                                    <div class="ml-3 sm:ml-4 flex-shrink-0">
                                                        <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-[1rem] text-xs sm:text-sm font-semibold tooltip cursor-help
                                                            @if($competency->pivot->proficiency_level == 1) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                            @elseif($competency->pivot->proficiency_level == 2) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                            @elseif($competency->pivot->proficiency_level == 3) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                            @elseif($competency->pivot->proficiency_level == 4) bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                                            @endif"
                                                            title="Proficiency Level: {{ ['', 'Beginner - Basic understanding', 'Intermediate - Comfortable working', 'Advanced - Highly skilled', 'Expert - Industry leading expertise'][$competency->pivot->proficiency_level] }}">
                                                            {{ ['', 'Beginner', 'Intermediate', 'Advanced', 'Expert'][$competency->pivot->proficiency_level] }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            @if($competency->pivot->proficiency_level)
                                                <!-- Enhanced Proficiency Level Indicator -->
                                                <div class="flex items-center space-x-3 mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-100 dark:border-gray-600">
                                                    <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 flex-shrink-0">Proficiency:</span>
                                                    <div class="flex items-center space-x-1">
                                                        @for($i = 1; $i <= 4; $i++)
                                                            <div class="relative">
                                                                <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-[0.5rem]
                                                                    @if($i <= $competency->pivot->proficiency_level)
                                                                        @if($competency->pivot->proficiency_level == 1) bg-yellow-400 shadow-yellow-300
                                                                        @elseif($competency->pivot->proficiency_level == 2) bg-blue-400 shadow-blue-300
                                                                        @elseif($competency->pivot->proficiency_level == 3) bg-green-400 shadow-green-300
                                                                        @elseif($competency->pivot->proficiency_level == 4) bg-purple-400 shadow-purple-300
                                                                        @endif shadow-sm
                                                                    @else bg-gray-200 dark:bg-gray-600
                                                                    @endif transition-all duration-200">
                                                                </div>
                                                                @if($i <= $competency->pivot->proficiency_level)
                                                                    <div class="absolute inset-0 rounded-[0.5rem] animate-ping opacity-25
                                                                        @if($competency->pivot->proficiency_level == 1) bg-yellow-400
                                                                        @elseif($competency->pivot->proficiency_level == 2) bg-blue-400
                                                                        @elseif($competency->pivot->proficiency_level == 3) bg-green-400
                                                                        @elseif($competency->pivot->proficiency_level == 4) bg-purple-400
                                                                        @endif">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endfor
                                                        <span class="ml-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                                                            ({{ $competency->pivot->proficiency_level }}/4)
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                <!-- Enhanced Responsive Sidebar -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Quick Stats -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Quick Stats
                        </h3>

                        <div class="space-y-3 sm:space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Skills</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $talent->competencies->count() }}
                                </span>
                            </div>

                            @if($talent->competencies->count() > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Expert Level</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $talent->competencies->where('pivot.proficiency_level', 4)->count() }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Advanced Level</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $talent->competencies->where('pivot.proficiency_level', 3)->count() }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Intermediate Level</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $talent->competencies->where('pivot.proficiency_level', 2)->count() }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Beginner Level</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $talent->competencies->where('pivot.proficiency_level', 1)->count() }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Member Since</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $talent->created_at->format('M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Card -->
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-600">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">
                            Interested in this talent?
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 sm:mb-6">
                            Send a request to connect with {{ $talent->name }} for your project needs.
                        </p>
                        <a href="{{ route('user.requests.create-direct', $talent) }}"
                           class="w-full inline-flex justify-center items-center px-4 py-3 text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 rounded-lg font-medium transition-all duration-300 ease-in-out">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="text-sm sm:text-base">Send Request</span>
                        </a>
                    </div>

                    <!-- Contact Card -->
                    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">
                            Contact Options
                        </h3>
                        <div class="space-y-2 sm:space-y-3">
                            <a href="mailto:{{ $talent->email }}"
                               class="w-full inline-flex items-center px-3 sm:px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm">Send Email</span>
                            </a>

                            @if($talent->phone)
                                <a href="tel:{{ $talent->phone }}"
                                   class="w-full inline-flex items-center px-3 sm:px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span class="text-sm">Call Now</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Enhanced Back to Top Button -->
        <button id="backToTopBtn" onclick="scrollToTop()"
                class="fixed bottom-6 right-6 sm:bottom-8 sm:right-8 bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-full shadow-lg hover:shadow-xl transform translate-y-16 transition-all duration-300 z-50 group">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:-translate-y-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
        </button>
    </div>

<!-- JavaScript for Enhanced Interactions -->
<script>
    // Share Profile Function
    function shareProfile() {
        if (navigator.share) {
            navigator.share({
                title: 'Check out {{ $talent->name }} on TalentConnect',
                text: 'View {{ $talent->name }}\'s professional profile and skills on TalentConnect',
                url: window.location.href
            }).catch(console.error);
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                // Show toast notification
                showToast('Profile link copied to clipboard!');
            }).catch(() => {
                // Fallback: show modal with link
                showShareModal();
            });
        }
    }

    // Show toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
        toast.textContent = message;
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }

    // Show share modal
    function showShareModal() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Share Profile</h3>
                <div class="flex items-center space-x-3 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg mb-4">
                    <input type="text" value="${window.location.href}" readonly class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-300 focus:outline-none">
                    <button onclick="copyLink()" class="text-purple-600 hover:text-purple-700 dark:text-purple-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
                <button onclick="closeShareModal()" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    Close
                </button>
            </div>
        `;
        document.body.appendChild(modal);
        modal.shareModal = true;
    }

    // Copy link function
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('Link copied to clipboard!');
            closeShareModal();
        });
    }

    // Close share modal
    function closeShareModal() {
        const modal = document.querySelector('[class*="fixed inset-0"]');
        if (modal && modal.shareModal) {
            document.body.removeChild(modal);
        }
    }

    // Toggle Skills View
    function toggleSkillsView() {
        const container = document.getElementById('skillsContainer');
        const button = document.getElementById('skillsViewToggle');

        if (container.classList.contains('grid')) {
            // Switch to list view
            container.className = 'space-y-4 transition-all duration-300';
            button.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                Grid View
            `;
        } else {
            // Switch to grid view
            container.className = 'grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 transition-all duration-300';
            button.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                List View
            `;
        }
    }

    // Toggle Description
    function toggleDescription(button) {
        const card = button.closest('.skill-card');
        const shortDesc = button.previousElementSibling;
        const fullDesc = button.nextElementSibling;

        if (button.textContent.trim() === 'Read more') {
            button.textContent = 'Read less';
            button.previousElementSibling.textContent = fullDesc.textContent;
        } else {
            button.textContent = 'Read more';
            const originalText = fullDesc.textContent;
            button.previousElementSibling.textContent = originalText.substring(0, 100) + '...';
        }
    }

    // Back to Top functionality
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Show/hide back to top button
    window.addEventListener('scroll', function() {
        const backToTopBtn = document.getElementById('backToTopBtn');
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.remove('translate-y-16');
            backToTopBtn.classList.add('translate-y-0');
        } else {
            backToTopBtn.classList.remove('translate-y-0');
            backToTopBtn.classList.add('translate-y-16');
        }
    });

    // Initialize tooltips (if using a tooltip library)
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll behavior to all internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add entrance animations to skill cards
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('animate-fade-in-up');
                    }, index * 100);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.skill-card').forEach(card => {
            observer.observe(card);
        });
    });

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .tooltip:hover::after {
            content: attr(title);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 1000;
            margin-bottom: 0.25rem;
        }

        .tooltip:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 4px solid transparent;
            border-top-color: #1f2937;
            z-index: 1000;
        }
    `;
    document.head.appendChild(style);
</script>
</x-layouts.app>
