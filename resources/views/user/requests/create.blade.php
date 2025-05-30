<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-gray-600 dark:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.333 6.767L7.015 5.35a.5.5 0 0 1 .525.11L10 8.35l2.46-2.889a.5.5 0 0 1 .525-.11l2.682 1.417M9 12H1a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-3m-7 4v-5m3 5v-5m3 5v-5M1 17h14a1 1 0 0 0 1-1v-2.5a.5.5 0 0 0-.5-.5h-15a.5.5 0 0 0-.5.5V16a1 1 0 0 0 1 1Z"/>
                </svg>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Create New Talent Request</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Specify the requirements for the talent you need.</p>
                </div>
            </div>
            <a href="{{ route('user.requests.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                <svg class="w-4 h-3 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0l4 4M1 5l4-4"/>
                </svg>
                Back to Requests
            </a>
        </div>

        {{-- Talent Request Form --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-4 border border-gray-200 dark:border-gray-700">
            <form action="{{ route('user.requests.store') }}" method="POST">
                @csrf

                {{-- Required Competencies & Proficiency Selection --}}
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Required Competencies & Proficiency Level <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Select required competencies, specify proficiency, and set their relative weight (0-100%).</p>
                    <div class="space-y-3 max-h-72 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-4 bg-gray-50 dark:bg-gray-700/50" id="competenciesList">
                        @php
                            $proficiencyLevels = [
                                1 => 'Completion',
                                2 => 'Intermediate',
                                3 => 'Advanced',
                                4 => 'Expert',
                            ];
                            // $weights array removed as we are using a 0-100 range slider now.
                            $oldCompetencies = collect(old('competencies', []));
                        @endphp
                        @forelse ($competencies as $index => $competency)
                            @php
                                $oldCompData = $oldCompetencies->firstWhere('id', (string)$competency->id) ?? $oldCompetencies->firstWhere('id', $competency->id);
                                $isChecked = $oldCompData !== null;
                                $oldLevel = $oldCompData['level'] ?? '';
                                $oldWeight = $oldCompData['weight'] ?? '0'; // Default to 0 for the slider
                            @endphp
                            <div class="competency-item p-3 border border-gray-200 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 shadow-sm" data-id="{{ $competency->id }}">
                                <div class="flex items-center justify-between space-x-3">
                                    <div class="flex items-center flex-grow">
                                        <input type="checkbox"
                                               id="competency_checkbox_{{ $competency->id }}"
                                               class="competency-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                               value="{{ $competency->id }}"
                                               {{ $isChecked ? 'checked' : '' }}>
                                        <label for="competency_checkbox_{{ $competency->id }}" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300 flex-grow">
                                            {{ $competency->name }}
                                        </label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <select data-type="level"
                                                class="competency-level shadow-sm appearance-none border rounded py-1 px-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border-gray-300 dark:border-gray-500 leading-tight focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-36 @error('competencies.'.$index.'.level') border-red-500 @enderror"
                                                {{ !$isChecked ? 'disabled' : '' }}>
                                            <option value="">-- Level --</option>
                                            @foreach ($proficiencyLevels as $value => $label)
                                                <option value="{{ $value }}" {{ (string)$oldLevel === (string)$value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- Weight Input: Changed from select to range slider --}}
                                        <div class="flex items-center space-x-2 w-48">
                                            <input type="range" min="0" max="100" value="{{ $oldWeight }}"
                                                   data-type="weight"
                                                   class="competency-weight-slider flex-grow h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 @error('competencies.'.$index.'.weight') border-red-500 @enderror"
                                                   {{ !$isChecked ? 'disabled' : '' }}>
                                            <span class="competency-weight-value text-sm text-gray-700 dark:text-gray-300 w-10 text-right">{{ $oldWeight }}%</span>
                                        </div>
                                        {{-- Critical Competency Toggle --}}
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox"
                                                   data-type="critical"
                                                   class="competency-critical h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                                   {{ !$isChecked ? 'disabled' : '' }}
                                                   @if(old('competencies'))
                                                       {{ collect(old('competencies'))->firstWhere('id', (string)$competency->id)['is_critical'] ?? false ? 'checked' : '' }}
                                                   @endif>
                                            <label class="text-xs text-gray-600 dark:text-gray-400">Critical</label>
                                        </div>
                                    </div>
                                </div>
                                @error('competencies.'.$index.'.level')
                                    <p class="text-red-500 text-xs italic mt-1 ml-7">{{ $message }}</p>
                                @enderror
                                @error('competencies.'.$index.'.weight')
                                    <p class="text-red-500 text-xs italic mt-1 ml-7">{{ $message }}</p>
                                @enderror
                                @error('competencies.'.$index.'.is_critical')
                                    <p class="text-red-500 text-xs italic mt-1 ml-7">{{ $message }}</p>
                                @enderror
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No competencies available.</p>
                        @endforelse
                    </div>
                    {{-- Hidden container for inputs that will be submitted --}}
                    <div id="competencies-form-data-container"></div>

                    @error('competencies') {{-- General error if no competencies selected or configured --}}
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                     @if ($errors->has('competencies.*.id') || $errors->has('competencies.*.level') || $errors->has('competencies.*.weight'))
                        <p class="text-red-500 text-xs italic mt-2">Please ensure all selected competencies have a valid level and weight.</p>
                    @endif

                    {{-- Critical Competency Settings --}}
                    <div class="mt-4 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                        <div class="flex items-center space-x-2 mb-3">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-200">Critical Competency Configuration</h4>
                        </div>
                        <p class="text-xs text-orange-700 dark:text-orange-300 mb-3">
                            Critical competencies act as "must-have" requirements. Talents failing to meet the veto threshold for any critical competency will be automatically excluded from consideration, regardless of their other qualifications.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-orange-800 dark:text-orange-200 mb-2">
                                    Veto Threshold (% of Required Level)
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="range"
                                           id="veto_threshold"
                                           name="veto_threshold"
                                           min="60"
                                           max="100"
                                           value="{{ old('veto_threshold', '80') }}"
                                           class="flex-grow h-2 bg-orange-200 rounded-lg appearance-none cursor-pointer dark:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <span id="veto_threshold_value" class="text-sm font-medium text-orange-800 dark:text-orange-200 w-12 text-right">{{ old('veto_threshold', '80') }}%</span>
                                </div>
                                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                    Talents below this percentage of the required level for critical competencies will be excluded.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-orange-800 dark:text-orange-200 mb-2">
                                    Critical Competency Impact
                                </label>
                                <div class="text-xs space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                        <span class="text-orange-700 dark:text-orange-300">Automatic elimination if below threshold</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                        <span class="text-orange-700 dark:text-orange-300">Enhanced weight multiplier if above threshold</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        <span class="text-orange-700 dark:text-orange-300">Confidence boost for high critical performance</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('veto_threshold')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Request Details --}}
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="details">
                        Request Details <span class="text-red-500">*</span>
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-neutral-950 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('details') border-red-500 @enderror"
                              id="details" name="details" rows="5" placeholder="Describe the project, tasks, duration, or specific requirements..." required>{{ old('details') }}</textarea>
                    @error('details')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Work Location --}}
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="work_location_type">
                            Work Location Type <span class="text-red-500">*</span>
                        </label>
                        <select name="work_location_type" id="work_location_type" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-neutral-950 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('work_location_type') border-red-500 @enderror">
                            <option value="">-- Select Type --</option>
                            <option value="remote" {{ old('work_location_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                            <option value="on_site" {{ old('work_location_type') == 'on_site' ? 'selected' : '' }}>On-site</option>
                            <option value="hybrid" {{ old('work_location_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('work_location_type')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="work_location_country">
                            Work Location Country
                        </label>
                        <div class="relative">
                            <input type="text" name="work_location_country" id="work_location_country" value="{{ old('work_location_country') }}"
                                   placeholder="Type to search countries..."
                                   autocomplete="country"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-neutral-950 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('work_location_country') border-red-500 @enderror">
                            <div id="country_suggestions" class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                            </div>
                        </div>
                        @error('work_location_country')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="work_location_city">
                            Work Location City
                        </label>
                        <div class="relative">
                            <input type="text" name="work_location_city" id="work_location_city" value="{{ old('work_location_city') }}"
                                   placeholder="Type to search cities..."
                                   autocomplete="address-level2"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-neutral-950 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('work_location_city') border-red-500 @enderror">
                            <div id="city_suggestions" class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                            </div>
                        </div>
                        @error('work_location_city')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('user.requests.index') }}" class="inline-block align-baseline font-medium text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
                        Cancel
                    </a>
                    <button class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-layouts.app>

<script>
    // Removed the [REQUEST_FORM_JS_BASIC_TEST] and [REQUEST_FORM_JS_INLINE_TEST] logs for clarity
    // This script is now directly part of create.blade.php, not pushed.

    document.addEventListener('DOMContentLoaded', function () {
        console.log('[REQUEST_FORM_JS] DOM fully loaded and parsed (inline script)');
        const competenciesList = document.getElementById('competenciesList');
        const form = competenciesList ? competenciesList.closest('form') : null;
        const formDataContainer = document.getElementById('competencies-form-data-container');

        if (!competenciesList) {
            console.error('[REQUEST_FORM_JS] Could not find competenciesList element');
            return;
        }
        if (!form) {
            console.error('[REQUEST_FORM_JS] Could not find form element associated with competenciesList');
            return;
        }
        if (!formDataContainer) {
            console.error('[REQUEST_FORM_JS] Could not find formDataContainer element');
            return;
        }

        function initializeCompetencyItem(item) {
            const checkbox = item.querySelector('.competency-checkbox');
            if (!checkbox) {
                console.error('[REQUEST_FORM_JS] No checkbox found in item:', item);
                return;
            }

            const levelSelect = item.querySelector('.competency-level');
            const weightSlider = item.querySelector('.competency-weight-slider'); // Changed from competency-weight
            const weightValueDisplay = item.querySelector('.competency-weight-value'); // New element for displaying slider value
            const criticalToggle = item.querySelector('.competency-critical'); // New critical toggle

            if (!levelSelect) {
                console.error('[REQUEST_FORM_JS] No .competency-level select found in item:', item);
            }
            if (!weightSlider) {
                console.error('[REQUEST_FORM_JS] No .competency-weight-slider input found in item:', item);
            }
            if (!weightValueDisplay) {
                console.error('[REQUEST_FORM_JS] No .competency-weight-value span found in item:', item);
            }
            if (!criticalToggle) {
                console.error('[REQUEST_FORM_JS] No .competency-critical checkbox found in item:', item);
            }

            const initiallyDisabled = !checkbox.checked;
            if (levelSelect) levelSelect.disabled = initiallyDisabled;
            if (weightSlider) weightSlider.disabled = initiallyDisabled;
            if (criticalToggle) criticalToggle.disabled = initiallyDisabled;
            // weightValueDisplay doesn't need to be disabled, just updated.

            checkbox.addEventListener('change', function () {
                const isChecked = this.checked;
                console.log('[REQUEST_FORM_JS] Checkbox changed:', this.id, 'Checked:', isChecked);

                if (levelSelect) {
                    levelSelect.disabled = !isChecked;
                    if (!isChecked) levelSelect.value = ''; // Reset if unchecked
                }
                if (weightSlider) {
                    weightSlider.disabled = !isChecked;
                    if (!isChecked) {
                        weightSlider.value = '0'; // Reset slider to 0 if unchecked
                        if (weightValueDisplay) weightValueDisplay.textContent = '0%';
                    }
                }
                if (criticalToggle) {
                    criticalToggle.disabled = !isChecked;
                    if (!isChecked) criticalToggle.checked = false; // Reset critical toggle if unchecked
                }
            });

            // Event listener for the weight slider to update the display
            if (weightSlider && weightValueDisplay) {
                weightSlider.addEventListener('input', function() {
                    weightValueDisplay.textContent = this.value + '%';
                });
                // Initialize display for pre-checked items with old values
                if (checkbox.checked) {
                     weightValueDisplay.textContent = weightSlider.value + '%';
                }
            }
        }

        // Initialize existing items on page load
        document.querySelectorAll('.competency-item').forEach(item => {
            initializeCompetencyItem(item);
        });
        console.log('[REQUEST_FORM_JS] Initialized existing competency items.');


        // Handling form submission to gather data
        form.addEventListener('submit', function (event) {
            console.log('[REQUEST_FORM_JS] Form submission triggered');
            formDataContainer.innerHTML = ''; // Clear previous hidden inputs
            let competencyIndex = 0;

            document.querySelectorAll('.competency-item').forEach(item => {
                const checkbox = item.querySelector('.competency-checkbox');
                if (checkbox && checkbox.checked) {
                    const competencyId = item.dataset.id;
                    const levelSelect = item.querySelector('.competency-level');
                    const weightSlider = item.querySelector('.competency-weight-slider'); // Changed from competency-weight
                    const criticalToggle = item.querySelector('.competency-critical'); // New critical toggle

                    const level = levelSelect ? levelSelect.value : '';
                    const weight = weightSlider ? weightSlider.value : '0'; // Default to '0' if slider not found or disabled
                    const isCritical = criticalToggle ? criticalToggle.checked : false; // Get critical toggle state

                    if (competencyId && level) { // Weight can be 0, so we don't check it for truthiness here
                        // Create hidden input for ID
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = `competencies[${competencyIndex}][id]`;
                        idInput.value = competencyId;
                        formDataContainer.appendChild(idInput);

                        // Create hidden input for Level
                        const levelInput = document.createElement('input');
                        levelInput.type = 'hidden';
                        levelInput.name = `competencies[${competencyIndex}][level]`;
                        levelInput.value = level;
                        formDataContainer.appendChild(levelInput);

                        // Create hidden input for Weight
                        const weightInput = document.createElement('input');
                        weightInput.type = 'hidden';
                        weightInput.name = `competencies[${competencyIndex}][weight]`;
                        weightInput.value = weight;
                        formDataContainer.appendChild(weightInput);

                        // Create hidden input for Critical status
                        const criticalInput = document.createElement('input');
                        criticalInput.type = 'hidden';
                        criticalInput.name = `competencies[${competencyIndex}][is_critical]`;
                        criticalInput.value = isCritical ? '1' : '0';
                        formDataContainer.appendChild(criticalInput);

                        competencyIndex++;
                    } else {
                        console.warn('[REQUEST_FORM_JS] Skipped a checked competency due to missing ID or level:', item);
                    }
                }
            });
            console.log('[REQUEST_FORM_JS] Prepared hidden inputs for', competencyIndex, 'competencies.');
            if (competencyIndex === 0 && document.querySelector('.competency-checkbox:checked')) {
                // This case means checkboxes were checked but selects were not filled.
                // Backend validation for min:1 on the competencies array will handle if no items are processed.
                // If items were checked but not valid, specific backend validation for level/weight will trigger.
                console.warn('[REQUEST_FORM_JS] No valid competencies were processed for submission, though some might be checked.');
            }
        });
        console.log('[REQUEST_FORM_JS] Initialization complete (inline script)');
    });

    // Veto threshold slider handling
    const vetoThresholdSlider = document.getElementById('veto_threshold');
    const vetoThresholdValue = document.getElementById('veto_threshold_value');

    if (vetoThresholdSlider && vetoThresholdValue) {
        vetoThresholdSlider.addEventListener('input', function() {
            vetoThresholdValue.textContent = this.value + '%';
        });
        console.log('[REQUEST_FORM_JS] Veto threshold slider initialized');
    }

    // Location autocomplete functionality
    const countries = [
        'Indonesia', 'Singapore', 'Malaysia', 'Thailand', 'Philippines', 'Vietnam', 'Myanmar', 'Laos', 'Cambodia', 'Brunei',
        'United States', 'Canada', 'United Kingdom', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Belgium', 'Switzerland',
        'Australia', 'New Zealand', 'Japan', 'South Korea', 'China', 'Taiwan', 'Hong Kong', 'India', 'Sri Lanka', 'Bangladesh',
        'UAE', 'Saudi Arabia', 'Qatar', 'Kuwait', 'Bahrain', 'Oman', 'Turkey', 'Palestine', 'Egypt', 'South Africa',
        'Brazil', 'Argentina', 'Chile', 'Mexico', 'Colombia', 'Peru', 'Venezuela', 'Uruguay', 'Paraguay', 'Ecuador',
        'Norway', 'Sweden', 'Denmark', 'Finland', 'Iceland', 'Ireland', 'Portugal', 'Austria', 'Czech Republic', 'Poland',
        'Hungary', 'Slovakia', 'Slovenia', 'Croatia', 'Serbia', 'Bosnia and Herzegovina', 'Montenegro', 'North Macedonia',
        'Bulgaria', 'Romania', 'Greece', 'Cyprus', 'Malta', 'Estonia', 'Latvia', 'Lithuania', 'Luxembourg'
    ];

    const majorCities = {
        'Indonesia': ['Jakarta', 'Surabaya', 'Bandung', 'Bekasi', 'Medan', 'Tangerang', 'Depok', 'Semarang', 'Palembang', 'Makassar', 'Batam', 'Yogyakarta'],
        'Singapore': ['Singapore City', 'Jurong West', 'Woodlands', 'Tampines', 'Yishun', 'Hougang'],
        'Malaysia': ['Kuala Lumpur', 'George Town', 'Ipoh', 'Shah Alam', 'Petaling Jaya', 'Johor Bahru', 'Subang Jaya', 'Kota Kinabalu', 'Kuching'],
        'United States': ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose', 'Austin', 'Seattle', 'San Francisco', 'Boston', 'Miami'],
        'United Kingdom': ['London', 'Birmingham', 'Leeds', 'Glasgow', 'Sheffield', 'Bradford', 'Liverpool', 'Edinburgh', 'Manchester', 'Bristol'],
        'Germany': ['Berlin', 'Hamburg', 'Munich', 'Cologne', 'Frankfurt', 'Stuttgart', 'Düsseldorf', 'Dortmund', 'Essen', 'Leipzig'],
        'Palestine': ['Gaza', 'Ramallah', 'Hebron', 'Nablus', 'Bethlehem', 'Khan Younis', 'Rafah', 'Tulkarm', 'Jenin', 'Qalqilya'],
        'Australia': ['Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Adelaide', 'Gold Coast', 'Newcastle', 'Canberra', 'Sunshine Coast', 'Wollongong'],
        'Japan': ['Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Kobe', 'Kawasaki', 'Kyoto', 'Saitama'],
        'Canada': ['Toronto', 'Montreal', 'Calgary', 'Ottawa', 'Edmonton', 'Mississauga', 'Winnipeg', 'Vancouver', 'Brampton', 'Hamilton']
    };

    function setupAutocomplete(inputId, suggestionId, dataArray, callback = null) {
        const input = document.getElementById(inputId);
        const suggestionBox = document.getElementById(suggestionId);

        if (!input || !suggestionBox) return;

        input.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            suggestionBox.innerHTML = '';

            if (value.length === 0) {
                suggestionBox.classList.add('hidden');
                return;
            }

            const filtered = dataArray.filter(item =>
                item.toLowerCase().includes(value)
            ).slice(0, 10); // Limit to 10 suggestions

            if (filtered.length > 0) {
                filtered.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-gray-900 dark:text-gray-100';
                    div.textContent = item;
                    div.addEventListener('click', function() {
                        input.value = item;
                        suggestionBox.classList.add('hidden');
                        if (callback) callback(item);
                    });
                    suggestionBox.appendChild(div);
                });
                suggestionBox.classList.remove('hidden');
            } else {
                suggestionBox.classList.add('hidden');
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.classList.add('hidden');
            }
        });
    }

    // Setup country autocomplete
    setupAutocomplete('work_location_country', 'country_suggestions', countries, function(selectedCountry) {
        // Clear city field when country changes
        const cityInput = document.getElementById('work_location_city');
        if (cityInput) {
            cityInput.value = '';
        }
    });

    // Setup city autocomplete that depends on selected country
    const cityInput = document.getElementById('work_location_city');
    const citySuggestionBox = document.getElementById('city_suggestions');
    const countryInput = document.getElementById('work_location_country');

    if (cityInput && citySuggestionBox && countryInput) {
        cityInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            const selectedCountry = countryInput.value;
            citySuggestionBox.innerHTML = '';

            if (value.length === 0) {
                citySuggestionBox.classList.add('hidden');
                return;
            }

            let cities = [];
            if (selectedCountry && majorCities[selectedCountry]) {
                cities = majorCities[selectedCountry];
            } else {
                // If no country selected or not in our list, show some major international cities
                cities = Object.values(majorCities).flat().slice(0, 50);
            }

            const filtered = cities.filter(city =>
                city.toLowerCase().includes(value)
            ).slice(0, 10);

            if (filtered.length > 0) {
                filtered.forEach(city => {
                    const div = document.createElement('div');
                    div.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-gray-900 dark:text-gray-100';
                    div.textContent = city;
                    div.addEventListener('click', function() {
                        cityInput.value = city;
                        citySuggestionBox.classList.add('hidden');
                    });
                    citySuggestionBox.appendChild(div);
                });
                citySuggestionBox.classList.remove('hidden');
            } else {
                citySuggestionBox.classList.add('hidden');
            }
        });

        document.addEventListener('click', function(e) {
            if (!cityInput.contains(e.target) && !citySuggestionBox.contains(e.target)) {
                citySuggestionBox.classList.add('hidden');
            }
        });
    }
</script>
