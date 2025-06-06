<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Comparison Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Talent Comparison
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Side-by-side analysis of {{ $talents->count() }} selected talents</p>
        </div>
        <div class="flex gap-4 mt-4 lg:mt-0">
            <a href="{{ route('user.talents.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Talent List
            </a>
            <button onclick="exportComparison()"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </button>
        </div>
    </div>

    <!-- Comparison Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white w-1/6">
                            Attribute
                        </th>
                        @foreach($talents as $talent)
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $talent->name }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    <!-- Profile Pictures -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            Photo
                        </td>
                        @foreach($talents as $talent)
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center">
                                @if($talent->profile_picture && Storage::exists('public/' . $talent->profile_picture))
                                    <img src="{{ Storage::url($talent->profile_picture) }}" alt="{{ $talent->name }}"
                                         class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-medium text-lg">
                                            {{ strtoupper(substr($talent->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $talent->name)[1] ?? '', 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>

                    <!-- Basic Information -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            Email
                        </td>
                        @foreach($talents as $talent)
                        <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
                            {{ $talent->email }}
                        </td>
                        @endforeach
                    </tr>

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            Location
                        </td>
                        @foreach($talents as $talent)
                        <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
                            @if($talent->domicile_city && $talent->domicile_country)
                                {{ $talent->domicile_city }}, {{ $talent->domicile_country }}
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            Phone
                        </td>
                        @foreach($talents as $talent)
                        <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
                            {{ $talent->phone_number ?? 'Not specified' }}
                        </td>
                        @endforeach
                    </tr>

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            Total Competencies
                        </td>
                        @foreach($talents as $talent)
                        <td class="px-6 py-4 text-center text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $talent->competencies->count() }}
                            </span>
                        </td>
                        @endforeach
                    </tr>

                    <!-- Competency Comparison Header -->
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <td colspan="{{ $talents->count() + 1 }}" class="px-6 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                            Competency Comparison
                        </td>
                    </tr>

                    <!-- Individual Competencies -->
                    @foreach($allCompetencies as $competency)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            <div class="flex flex-col">
                                <span>{{ $competency->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $competency->category }}</span>
                            </div>
                        </td>
                        @foreach($talents as $talent)
                        @php
                            $talentCompetency = $talent->competencies->firstWhere('id', $competency->id);
                            $proficiencyLevel = $talentCompetency ? $talentCompetency->pivot->proficiency_level : 0;
                            $proficiencyLabels = [0 => 'Not Available', 1 => 'Beginner', 2 => 'Intermediate', 3 => 'Advanced', 4 => 'Expert'];
                            $proficiencyColors = [0 => 'gray', 1 => 'red', 2 => 'yellow', 3 => 'blue', 4 => 'green'];
                        @endphp
                        <td class="px-6 py-4 text-center text-sm">
                            @if($proficiencyLevel > 0)
                                <div class="flex flex-col items-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $proficiencyColors[$proficiencyLevel] }}-100 text-{{ $proficiencyColors[$proficiencyLevel] }}-800 dark:bg-{{ $proficiencyColors[$proficiencyLevel] }}-900 dark:text-{{ $proficiencyColors[$proficiencyLevel] }}-200">
                                        {{ $proficiencyLabels[$proficiencyLevel] }}
                                    </span>
                                    <div class="flex mt-1">
                                        @for($i = 1; $i <= 4; $i++)
                                            <div class="w-2 h-2 mx-0.5 rounded-full {{ $i <= $proficiencyLevel ? 'bg-' . $proficiencyColors[$proficiencyLevel] . '-500' : 'bg-gray-300' }}"></div>
                                        @endfor
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    Not Available
                                </span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach

                    <!-- Summary Scores -->
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <td colspan="{{ $talents->count() + 1 }}" class="px-6 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                            Performance Summary
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            Average Proficiency
                        </td>
                        @foreach($talents as $talent)
                        @php
                            $avgProficiency = $talent->competencies->avg('pivot.proficiency_level');
                        @endphp
                        <td class="px-6 py-4 text-center text-sm">
                            <div class="flex flex-col items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ number_format($avgProficiency, 1) }}
                                </span>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($avgProficiency / 4) * 100 }}%"></div>
                                </div>
                            </div>
                        </td>
                        @endforeach
                    </tr>

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            Expert Level Competencies
                        </td>
                        @foreach($talents as $talent)
                        @php
                            $expertCount = $talent->competencies->where('pivot.proficiency_level', 4)->count();
                        @endphp
                        <td class="px-6 py-4 text-center text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $expertCount }}
                            </span>
                        </td>
                        @endforeach
                    </tr>

                    <!-- Actions -->
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            Actions
                        </td>
                        @foreach($talents as $talent)
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('user.talents.show', $talent) }}"
                                   class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Profile
                                </a>
                                <button onclick="toggleShortlist({{ $talent->id }}, this)"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    Shortlist
                                </button>
                                <a href="{{ route('user.requests.create', ['talent_id' => $talent->id]) }}"
                                   class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Request
                                </a>
                            </div>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Enhanced JavaScript -->
<script>
// Shortlist functionality
function toggleShortlist(talentId, button) {
    fetch(`/talents/${talentId}/shortlist`, {
        method: 'POST',
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
            // Visual feedback
            button.classList.add('bg-purple-700');
            setTimeout(() => {
                button.classList.remove('bg-purple-700');
            }, 200);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Export functionality placeholder
function exportComparison() {
    // This would typically generate a PDF or CSV export
    // For now, we'll show a placeholder
    alert('Export functionality will be implemented in Phase 2');
}

// Print functionality
function printComparison() {
    window.print();
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }

    .print-break {
        page-break-before: always;
    }
}
</style>
</x-layouts.app>
