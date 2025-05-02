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
                    <div class="space-y-3 max-h-60 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-4 bg-gray-50 dark:bg-gray-700/50">
                        @php
                            $proficiencyLevels = [
                                1 => 'Completion',
                                2 => 'Intermediate',
                                3 => 'Advanced',
                                4 => 'Expert',
                            ];
                        @endphp
                        @forelse ($competencies as $competency)
                            <div class="flex items-center justify-between space-x-4">
                                <div class="flex items-center flex-grow">
                                    <input type="checkbox"
                                           id="competency_{{ $competency->id }}"
                                           name="competencies_selected[]" {{-- Helper to know which competencies were considered --}}
                                           value="{{ $competency->id }}"
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                           {{ (is_array(old('competencies')) && array_key_exists($competency->id, old('competencies'))) ? 'checked' : '' }}>
                                    <label for="competency_{{ $competency->id }}" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300 flex-grow">
                                        {{ $competency->name }}
                                    </label>
                                </div>
                                <select name="competencies[{{ $competency->id }}]" {{-- Send as associative array --}}
                                        class="shadow-sm appearance-none border rounded py-1 px-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border-gray-300 dark:border-gray-500 leading-tight focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-40 @error('competencies.'.$competency->id) border-red-500 @enderror">
                                    <option value="">-- Select Level --</option>
                                    @foreach ($proficiencyLevels as $value => $label)
                                        <option value="{{ $value }}" {{ old('competencies.'.$competency->id) == $value ? 'selected' : '' }}>
                                            {{ $label }} ({{ $value }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('competencies.'.$competency->id)
                                <p class="text-red-500 text-xs italic ml-7">{{ $message }}</p>
                            @enderror
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No competencies available.</p>
                        @endforelse
                    </div>
                    @error('competencies') {{-- General error if no competencies selected --}}
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
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
</x-layouts.app>