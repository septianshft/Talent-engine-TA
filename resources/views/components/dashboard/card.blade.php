@props(['title', 'value', 'color' => 'indigo'])

<div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">{{ $title }}</h4>
    <p class="mt-2 text-3xl font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400">
        {{ $value }}
    </p>
</div>
