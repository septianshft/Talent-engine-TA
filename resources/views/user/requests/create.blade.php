<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-700 mb-6">Create New Talent Request</h1>

    {{-- Competency Filter Form --}}
    <form method="GET" action="{{ route('user.requests.create') }}" class="mb-6 bg-white shadow-md rounded px-8 pt-6 pb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="competency_id">
                Filter Talents by Competency (Optional)
            </label>
            <select name="competency_id" id="competency_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="this.form.submit()">
                <option value="">-- Select Competency --</option>
                @foreach ($competencies as $competency)
                    <option value="{{ $competency->id }}" {{ $selectedCompetencyId == $competency->id ? 'selected' : '' }}>
                        {{ $competency->name }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- Optional: Add a button to clear filter --}}
        @if($selectedCompetencyId)
        <a href="{{ route('user.requests.create') }}" class="text-sm text-blue-500 hover:text-blue-700">Clear Filter</a>
        @endif
    </form>

    {{-- Talent Request Form --}}
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <form action="{{ route('user.requests.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="talent_id">
                    Select Talent
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('talent_id') border-red-500 @enderror" id="talent_id" name="talent_id" required>
                    <option value="">-- Select a Talent --</option>
                    @forelse ($talents as $talent)
                        <option value="{{ $talent->id }}" {{ old('talent_id') == $talent->id ? 'selected' : '' }}>
                            {{ $talent->name }}
                        </option>
                    @empty
                        <option value="" disabled>No talents found{{ $selectedCompetencyId ? ' with the selected competency' : '' }}.</option>
                    @endforelse
                </select>
                @error('talent_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="details">
                    Request Details
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('details') border-red-500 @enderror" id="details" name="details" rows="5" placeholder="Describe the project or task requirements..." required>{{ old('details') }}</textarea>
                @error('details')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Submit Request
                </button>
                <a href="{{ route('user.requests.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>