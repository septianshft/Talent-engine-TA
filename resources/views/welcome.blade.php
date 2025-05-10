<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TalentConnect') }} - Streamline Your Talent Scouting</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              sans: ['Instrument Sans', 'sans-serif'],
            },
            colors: {
              'brand-blue': '#007bff',
              'brand-blue-dark': '#0056b3',
              'brand-light': '#F8F9FA',
              'brand-secondary': '#6c757d',
            },
            animation: {
              'fade-in': 'fadeIn 0.6s ease-out',
              'slide-up': 'slideUp 0.6s ease-out',
            },
            keyframes: {
              fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
              slideUp: { '0%': { transform: 'translateY(20px)', opacity: 0 }, '100%': { transform: 'translateY(0)', opacity: 1 } }
            }
          }
        }
      }
    </script>
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #F8F9FA;
            scroll-padding-top: 4rem;
        }
        html {
        scroll-behavior: smooth; /* Enables smooth scrolling */
        }
        .dark body {
        background-color: #111827;
        color: #d1d5db;
        }
        .gradient-text {
            background: linear-gradient(to right, #007bff, #0056b3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .feature-icon-bg {
            background: linear-gradient(135deg, rgba(0,123,255,0.15) 0%, rgba(0,86,179,0.1) 100%);
        }
        .hover-glow:hover {
            box-shadow: 0 0 15px rgba(0,123,255,0.2);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
        .dark .glass-effect {
            background: rgba(17, 24, 39, 0.8);
        }
    </style>
</head>
<body class="antialiased text-gray-800 dark:text-gray-200" x-data="{ mobileMenuOpen: false }">
    <!-- Navigation -->
    <header class="sticky top-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between h-16">
                <a href="/" class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ config('app.name', 'TalentConnect') }}
                </a>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#how-it-works" class="text-sm font-medium text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">How It Works</a>
                    <a href="#benefits" class="text-sm font-medium text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">Benefits</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow hover:shadow-md transition-all hover:from-blue-700 hover:to-blue-800">Get Started</a>
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors">Log In</a>
                    @endif
                </div>
                
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white focus:outline-none">
                    <span class="sr-only">Open menu</span>
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </nav>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="md:hidden bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg border-b border-gray-100 dark:border-gray-800">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a @click="mobileMenuOpen = false" href="#how-it-works" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-gray-800">How It Works</a>
                <a @click="mobileMenuOpen = false" href="#benefits" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-gray-800">Benefits</a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                <div class="px-4 flex flex-col space-y-2">
                    <a @click="mobileMenuOpen = false" href="{{ route('register') }}" class="w-full py-2 text-center text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow hover:shadow-md transition-all hover:from-blue-700 hover:to-blue-800">Get Started</a>
                    @if (Route::has('login'))
                        <a @click="mobileMenuOpen = false" href="{{ route('login') }}" class="w-full py-2 text-center text-sm font-medium text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors">Log In</a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 -z-10"></div>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-32 md:pt-32 md:pb-40">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight text-gray-900 dark:text-white mb-6 animate-slide-up">
                    <span class="block">Streamline Your</span>
                    <span class="block gradient-text">Talent Scouting Process</span>
                </h1>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto animate-slide-up" style="animation-delay: 0.2s">
                    Discover the right talent, faster. Our intelligent platform connects HR professionals with skilled candidates through a seamless, competency-based outsourcing model.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row sm:justify-center gap-4 sm:gap-6">
                    <a href="{{ route('register') }}" class="px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Request a Demo
                    </a>
                    <a href="#benefits" class="px-6 py-3 text-base font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-300 dark:bg-blue-900/40 dark:hover:bg-blue-900/60 rounded-lg shadow hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-20 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base font-semibold text-blue-600 dark:text-blue-400 tracking-wide uppercase">Benefits</h2>
                <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">Recruit Smarter, Not Harder</p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-400 mx-auto">
                    Our Talent Engine empowers your HR team to find and manage top-tier talent with unprecedented efficiency.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach([
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />',
                        'title' => 'Intelligent Matching',
                        'description' => 'Our AI-powered system precisely matches talent competencies with your job requirements, saving you hours of manual screening.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
                        'title' => 'Streamlined Workflow',
                        'description' => 'From request to onboarding, manage the entire recruitment lifecycle in one centralized, easy-to-use platform.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        'title' => 'Access to Vetted Talent',
                        'description' => 'Connect with a curated pool of pre-vetted professionals, ensuring quality and reducing hiring risks.'
                    ]
                ] as $benefit)
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-700 group">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                {!! $benefit['icon'] !!}
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $benefit['title'] }}</h3>
                        <p class="text-base text-gray-500 dark:text-gray-400">{{ $benefit['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base font-semibold text-blue-600 dark:text-blue-400 tracking-wide uppercase">How It Works</h2>
                <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">Simple Steps to Your Ideal Candidate</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @foreach([
                    [
                        'step' => '1',
                        'title' => 'Define Your Needs',
                        'description' => 'Submit a talent request specifying required competencies and project details.'
                    ],
                    [
                        'step' => '2',
                        'title' => 'Get Matched',
                        'description' => 'Our system identifies and ranks the most suitable candidates from our talent pool.'
                    ],
                    [
                        'step' => '3',
                        'title' => 'Hire & Collaborate',
                        'description' => 'Review profiles, approve talent, and start collaborating seamlessly.'
                    ]
                ] as $step)
                    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:transform hover:-translate-y-1">
                        <div class="flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 text-xl font-bold">
                            {{ $step['step'] }}
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                        <p class="text-base text-gray-500 dark:text-gray-400">{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative overflow-hidden py-16 md:py-24">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-700 -z-10"></div>
        <div class="absolute inset-0 opacity-10 dark:opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0wIDBoNjB2NjBoLTYwem0xNC40NCAxNC40NGMtMy4zMiAwLTQuOTggMC02LjY0IC0xLjY2QzYuMTQgMTQuNDQgNC40OCAxMy4xMiA0LjQ4IDExLjQ2YzAtMS42NiAxLjY2LTMuMzIgMy4zMi0zLjMyIDEuNjYgMCAzLjMyIC42NiA0LjQ0IDEuOTZMOS45MiA4LjE2Yy0xLjY2LTEuMy0zLjk4LTEuOC01LjY0LTEuOHMtNC4xNCAuNS01LjggMS44Yy0xLjY2IDEuMy0yLjY0IDMuMzItMi42NCA1LjY0IDAgMi4zMiAxIDQuMTQgMi42NiA1LjQ0IDEuNjYgMS4zIDMuODIgMi4xNCA2LjY0IDIuMTQgMi44MiAwIDQuOTgtLjg0IDYuNi0yLjUgMS42Ni0xLjY2IDIuNS00LjE0IDIuNS02LjYgMC0xLjY2LS44NC0zLjMyLTEuNjYtNC40NHoiIGZpbGw9IiNmZmYiLz48L2c+PC9zdmc+')]"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl animate-fade-in">
                <span class="block">Ready to transform your recruitment?</span>
            </h2>
            <p class="mt-4 text-lg text-blue-100 dark:text-blue-200 animate-fade-in" style="animation-delay: 0.2s">
                Join leading HR teams who trust {{ config('app.name', 'TalentConnect') }} to find the best talent, effortlessly.
            </p>
            <a href="{{ route('register') }}" class="mt-8 inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-blue-600 bg-white hover:bg-blue-50 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Get Started Today
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'TalentConnect') }}. All rights reserved. Built for HR Professionals.</p>
        </div>
    </footer>
</body>
</html>