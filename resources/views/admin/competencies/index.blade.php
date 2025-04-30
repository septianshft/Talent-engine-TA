<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Manage Competencies</h1>
        <a href="{{ route('admin.competencies.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add New Competency
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-right text-xs leading-4 font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse ($competencies as $competency)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $competency->name }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-right">
                            <a href="{{ route('admin.competencies.edit', $competency) }}" class="text-indigo-600 hover:text-indigo-900 px-2">Edit</a>
                            <form action="{{ route('admin.competencies.destroy', $competency) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this competency?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center text-gray-500">No competencies found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $competencies->links() }} {{-- Pagination links --}}

</div>
</x-layouts.app>