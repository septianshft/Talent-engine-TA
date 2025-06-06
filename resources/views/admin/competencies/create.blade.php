<x-layouts.app>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.competencies.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Competencies
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Add New</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
             <svg class="w-8 h-8 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Add New Competency
        </h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Create a new competency to be used across the platform.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
        <form action="{{ route('admin.competencies.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            
            <!-- Competency Name Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="name">
                    Competency Name
                </label>
                <div class="relative rounded-md shadow-sm">
                    <input 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 bg-slate-700 dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 rounded-md leading-tight focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500 @enderror"
                        id="name" 
                        name="name" 
                        type="text" 
                        placeholder="e.g., PHP, Laravel, Project Management" 
                        value="{{ old('name') }}" 
                        required
                        aria-describedby="name-error name-description"
                    >
                </div>
                @error('name')
                    <p class="text-red-600 dark:text-red-400 text-xs mt-2" id="name-error">{{ $message }}</p>
                @enderror
                 <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" id="name-description">The name should be unique and descriptive.</p>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-end space-y-4 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button 
                    type="button" 
                    onclick="window.location.href='{{ route('admin.competencies.index') }}'" 
                    class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 rounded-lg shadow-md transition-all duration-300 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd"></path>
                    </svg>
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 rounded-lg shadow-md transition-all duration-300 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Create Competency
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>