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
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Select required competencies and specify the minimum proficiency level needed for each.</p>
                    <div class="space-y-3 max-h-72 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-4 bg-gray-50 dark:bg-gray-700/50" id="competenciesList">
                        @php
                            $proficiencyLevels = [
                                1 => 'Completion',
                                2 => 'Intermediate',
                                3 => 'Advanced',
                                4 => 'Expert',
                            ];
                            $weights = [
                                1 => '1 (Lowest)',
                                2 => '2',
                                3 => '3 (Medium)',
                                4 => '4',
                                5 => '5 (Highest)',
                            ];
                            // Helper for old data, assuming competencies might be submitted as an indexed array
                            $oldCompetencies = collect(old('competencies', []));
                        @endphp
                        @forelse ($competencies as $index => $competency)
                            @php
                                $oldCompData = $oldCompetencies->firstWhere('id', (string)$competency->id) ?? $oldCompetencies->firstWhere('id', $competency->id);
                                $isChecked = $oldCompData !== null;
                                $oldLevel = $oldCompData['level'] ?? '';
                                $oldWeight = $oldCompData['weight'] ?? '';
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
                                    <div class="flex space-x-2">
                                        <select data-type="level"
                                                class="competency-level shadow-sm appearance-none border rounded py-1 px-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border-gray-300 dark:border-gray-500 leading-tight focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-36 @error('competencies.'.$index.'.level') border-red-500 @enderror"
                                                {{ !$isChecked ? 'disabled' : '' }}>
                                            <option value="">-- Level --</option>
                                            @foreach ($proficiencyLevels as $value => $label)
                                                <option value="{{ $value }}" {{ (string)$oldLevel === (string)$value ? 'selected' : '' }}>
                                                    {{ $label }} ({{ $value }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <select data-type="weight"
                                                class="competency-weight shadow-sm appearance-none border rounded py-1 px-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border-gray-300 dark:border-gray-500 leading-tight focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-36 @error('competencies.'.$index.'.weight') border-red-500 @enderror"
                                                {{ !$isChecked ? 'disabled' : '' }}>
                                            <option value="">-- Weight --</option>
                                            @foreach ($weights as $value => $label)
                                                <option value="{{ $value }}" {{ (string)$oldWeight === (string)$value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('competencies.'.$index.'.level')
                                    <p class="text-red-500 text-xs italic mt-1 ml-7">{{ $message }}</p>
                                @enderror
                                @error('competencies.'.$index.'.weight')
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
            if (!checkbox) return;

            const selects = item.querySelectorAll('select');
            selects.forEach(select => {
                select.disabled = !checkbox.checked;
            });

            checkbox.addEventListener('change', function () {
                console.log('[REQUEST_FORM_JS] Checkbox changed:', this.id, 'Checked:', this.checked);
                selects.forEach(select => {
                    select.disabled = !this.checked;
                    if (!this.checked) {
                        select.value = ''; // Reset if unchecked
                    }
                });
            });
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
                    const weightSelect = item.querySelector('.competency-weight');

                    console.log('[REQUEST_FORM_JS] Processing checked competency ID:', competencyId, 'Level val:', levelSelect.value, 'Weight val:', weightSelect.value);

                    if (competencyId && levelSelect && weightSelect && levelSelect.value && weightSelect.value) { // Ensure selects have values
                        let idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = `competencies[${competencyIndex}][id]`;
                        idInput.value = competencyId;
                        formDataContainer.appendChild(idInput);

                        let levelInput = document.createElement('input');
                        levelInput.type = 'hidden';
                        levelInput.name = `competencies[${competencyIndex}][level]`;
                        levelInput.value = levelSelect.value;
                        formDataContainer.appendChild(levelInput);

                        let weightInput = document.createElement('input');
                        weightInput.type = 'hidden';
                        weightInput.name = `competencies[${competencyIndex}][weight]`;
                        weightInput.value = weightSelect.value;
                        formDataContainer.appendChild(weightInput);

                        competencyIndex++;
                    } else {
                        console.warn('[REQUEST_FORM_JS] Missing data or select value for checked competency ID:', competencyId);
                        // Optionally, you could prevent form submission here if a checked item is not fully configured,
                        // though backend validation should also catch this.
                        // event.preventDefault(); // Example: stop submission
                        // alert('Please ensure all selected competencies have a level and weight.');
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
</script>
