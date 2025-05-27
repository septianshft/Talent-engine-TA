<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Send Direct Request</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Send a request directly to {{ $talent->name }}</p>
                </div>
            </div>
            <a href="{{ route('user.talents.show', $talent->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                <svg class="w-4 h-3 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0l4 4M1 5l4-4"/>
                </svg>
                Back to Profile
            </a>
        </div>

        {{-- Talent Info Card --}}
        <div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-90 rounded-full flex items-center justify-center text-green-700 font-bold text-xl shadow-lg">
                    {{ $talent->initials() }}
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $talent->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $talent->email }}</p>
                    @if($talent->domicile_country || $talent->domicile_city)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            📍 {{ $talent->domicile_city ? $talent->domicile_city . ', ' : '' }}{{ $talent->domicile_country }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="mt-3 p-3 bg-white/60 dark:bg-gray-800/40 rounded border-l-4 border-green-400">
                <p class="text-sm text-green-800 dark:text-green-200 font-medium">
                    ⚡ Direct Request: Your request will go directly to this talent for review, bypassing admin approval.
                </p>
            </div>
        </div>

        {{-- Direct Request Form --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-4 border border-gray-200 dark:border-gray-700">
            <form action="{{ route('user.requests.store-direct', $talent) }}" method="POST">
                @csrf

                {{-- Talent's Skills Info --}}
                @if($talent->competencies->isNotEmpty())
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Available Skills
                        </label>
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                            <p class="text-sm text-blue-800 dark:text-blue-200 font-medium mb-3">{{ $talent->name }} has the following skills:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($talent->competencies as $skill)
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border border-blue-200 dark:border-blue-600">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $skill->name }}</span>
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 text-xs rounded-full font-medium">
                                            Level {{ $skill->pivot->proficiency_level }}
                                        </span>
                                    </div>
                                    {{-- Include all talent's skills with default values --}}
                                    <input type="hidden" name="competencies[{{ $loop->index }}][id]" value="{{ $skill->id }}">
                                    <input type="hidden" name="competencies[{{ $loop->index }}][level]" value="{{ $skill->pivot->proficiency_level }}">
                                    <input type="hidden" name="competencies[{{ $loop->index }}][weight]" value="3">
                                @endforeach
                            </div>
                            <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded">
                                <p class="text-xs text-green-800 dark:text-green-200">
                                    ✓ All of {{ $talent->name }}'s skills will be included in your request automatically.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Project Details --}}
                <div class="mb-6">
                    <label for="details" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Project Details <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="details"
                        name="details"
                        rows="4"
                        placeholder="Describe your project requirements, timeline, and any specific details you'd like the talent to know..."
                        class="w-full px-3 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:focus:ring-blue-600"
                        required
                    >{{ old('details') }}</textarea>
                    @error('details')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Work Location --}}
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Work Location Type <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="work_location_type" value="remote" class="form-radio" {{ old('work_location_type') == 'remote' ? 'checked' : '' }} onchange="toggleLocationFields()">
                            <span class="ml-2">Remote</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="work_location_type" value="on_site" class="form-radio" {{ old('work_location_type') == 'on_site' ? 'checked' : '' }} onchange="toggleLocationFields()">
                            <span class="ml-2">On-site</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="work_location_type" value="hybrid" class="form-radio" {{ old('work_location_type') == 'hybrid' ? 'checked' : '' }} onchange="toggleLocationFields()">
                            <span class="ml-2">Hybrid</span>
                        </label>
                    </div>
                    @error('work_location_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Location Details --}}
                <div id="locationFields" class="mb-6" style="display: {{ old('work_location_type') === 'remote' ? 'none' : 'block' }};">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="work_location_country" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="work_location_country"
                                name="work_location_country"
                                value="{{ old('work_location_country') }}"
                                placeholder="Enter country"
                                class="w-full px-3 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                            >
                            @error('work_location_country')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="work_location_city" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="work_location_city"
                                name="work_location_city"
                                value="{{ old('work_location_city') }}"
                                placeholder="Enter city"
                                class="w-full px-3 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                            >
                            @error('work_location_city')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('user.talents.show', $talent->id) }}" class="text-gray-600 hover:text-gray-800 text-sm">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Send Direct Request</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleLocationFields() {
        const workLocationType = document.querySelector('input[name="work_location_type"]:checked');
        const locationFields = document.getElementById('locationFields');
        const countryField = document.getElementById('work_location_country');
        const cityField = document.getElementById('work_location_city');

        if (workLocationType && workLocationType.value === 'remote') {
            locationFields.style.display = 'none';
            countryField.required = false;
            cityField.required = false;
        } else {
            locationFields.style.display = 'block';
            countryField.required = true;
            cityField.required = true;
        }
    }

    // Initialize the form state on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize location fields
        toggleLocationFields();
    });
</script>
</x-layouts.app>
