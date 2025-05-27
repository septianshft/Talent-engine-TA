<?php

use function Livewire\Volt\{state, rules, computed, uses};
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Livewire\WithPagination;

uses([WithPagination::class]); // Add this for $this->resetPage()

state([
    'showCompetencyModal' => false,
    'competencyModalUser' => null,
    'showModal' => false,
    'showDeleteModal' => false,
    'editingUser' => null,
    'userToDelete' => null,
    'form' => [
        'name' => '',
        'email' => '',
        'password' => '',
        'role' => 'user',
    ],
    'sortField' => 'name', // Default sort field
    'sortDirection' => 'asc', // Default sort direction
    'filterRole' => 'all', // 'all', 'user', 'talent'
]);

$setFilterRole = function (string $role) {
    $this->filterRole = $role;
    $this->resetPage(); // Reset pagination when filter changes
};

$sortBy = function (string $field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortDirection = 'asc';
    }
    $this->sortField = $field;
};

$resetForm = function() {
    $this->form = [
        'name' => '',
        'email' => '',
        'password' => '',
        'role' => 'user',
    ];
    $this->resetValidation();
};

$users = computed(function() {
    $query = User::query()
        ->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'admin'); // Always exclude admins
        })
        ->with(['roles', 'competencies']);

    // Apply role filter
    if ($this->filterRole !== 'all') {
        $query->whereHas('roles', function ($q) {
            $q->where('name', $this->filterRole);
        });
    }

    // Apply sorting
    // Sorting by 'role' name directly is complex with Eloquent's `with` and `paginate`
    // when a user might have multiple roles or no roles.
    // The current sort logic defaults to 'name' if 'role' is chosen for sortField.
    // This is acceptable if we are filtering by a single role type.
    $actualSortField = $this->sortField;
    if ($this->sortField === 'role' && $this->filterRole === 'all') {
        // If filtering all users and trying to sort by role, default to name sort
        // as sorting by a potentially multiple/absent role name is tricky.
        $actualSortField = 'name';
    }

    $query->orderBy($actualSortField, $this->sortDirection);

    return $query->paginate(10);
});

$validate = [
    'form.name' => 'required|min:2',
    'form.email' => 'required|email|unique:users,email',
    'form.password' => 'required|min:8',
    'form.role' => 'required|in:user,talent',
];

$createUser = function() use ($validate) {
    $this->validate($validate);

    $user = User::create([
        'name' => $this->form['name'],
        'email' => $this->form['email'],
        'password' => Hash::make($this->form['password']),
        // 'role' is no longer a direct attribute
    ]);

    // Find the role model and attach it
    $role = Role::where('name', $this->form['role'])->first();
    if ($role) {
        $user->roles()->attach($role->id);
    }

    $this->showModal = false;
    $this->resetForm();
    $this->dispatch('$refresh'); // Force a component refresh
};

$updateUser = function() {
    $this->validate([
        'form.name' => 'required|min:2',
        'form.email' => 'required|email|unique:users,email,'.$this->editingUser->id,
        'form.role' => 'required|in:user,talent',
    ]);

    $this->editingUser->update([
        'name' => $this->form['name'],
        'email' => $this->form['email'],
        // 'role' is no longer a direct attribute
    ]);

    // Find the role model and sync it
    $role = Role::where('name', $this->form['role'])->first();
    if ($role) {
        $this->editingUser->roles()->sync([$role->id]); // Use sync to replace existing roles
    }

    $this->showModal = false;
    $this->resetForm();
    $this->editingUser = null;
};

$editUser = function(User $user) {
    $this->editingUser = $user->load('roles'); // Eager load roles
    $this->form = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->roles->first()?->name ?? 'user', // Get the first role name, default to 'user'
    ];
    $this->showModal = true;
};

$confirmDelete = function(User $user) {
    $this->userToDelete = $user;
    $this->showDeleteModal = true;
};

$deleteUser = function() {
    if ($this->userToDelete) {
        $this->userToDelete->delete();
    }
    $this->showDeleteModal = false;
    $this->userToDelete = null;
};

$openCompetencyModal = function(User $user) {
    $this->competencyModalUser = $user->load('competencies'); // Load competencies if not already loaded
    $this->showCompetencyModal = true;
};

$closeCompetencyModal = function() {
    $this->showCompetencyModal = false;
    $this->competencyModalUser = null;
};

$closeModal = function() {
    $this->showModal = false;
    $this->resetForm();
    $this->editingUser = null;
};

$closeDeleteModal = function() {
    $this->showDeleteModal = false;
    $this->userToDelete = null;
};

$openCompetencyModal = function(User $user) {
    $this->competencyModalUser = $user->load('competencies'); // Load competencies if not already loaded
    $this->showCompetencyModal = true;
};

$closeCompetencyModal = function() {
    $this->showCompetencyModal = false;
    $this->competencyModalUser = null;
};

?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 rounded-lg">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Pengguna</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola dan atur pengguna platform Anda</p>
                </div>
                <button wire:click="$set('showModal', true)"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-medium flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pengguna
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Urutkan bedasarkan peran:</span>
                    <div class="flex gap-2">
                        <button wire:click="setFilterRole('all')"
                                class="px-3 py-1.5 text-sm rounded-xl transition-colors {{ $filterRole === 'all' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                            All
                        </button>
                        <button wire:click="setFilterRole('talent')"
                                class="px-3 py-1.5 text-sm rounded-xl transition-colors {{ $filterRole === 'talent' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                            Talents
                        </button>
                        <button wire:click="setFilterRole('user')"
                                class="px-3 py-1.5 text-sm rounded-xl transition-colors {{ $filterRole === 'user' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                            Users
                        </button>
                    </div>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $this->users->total() }} total pengguna
                </div>
            </div>
        </div>

        <!-- User Table -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" wire:click="sortBy('name')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    Nama
                                    @if ($sortField === 'name')
                                        <span class="text-gray-400">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" wire:click="sortBy('email')" class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    Email
                                    @if ($sortField === 'email')
                                        <span class="text-gray-400">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Peran</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kompetensi</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @php $roleName = $user->roles->first()?->name ?? 'user'; @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    {{ $roleName === 'talent' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }}">
                                    {{ ucfirst($roleName) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($roleName === 'talent' && $user->competencies->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($user->competencies->take(2) as $competency)
                                            <span class="inline-flex px-2 py-1 text-xs bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300 rounded-lg">
                                                {{ $competency->name }}
                                            </span>
                                        @endforeach
                                        @if ($user->competencies->count() > 2)
                                            <button wire:click="openCompetencyModal({{ $user->id }})"
                                                    class="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                                                +{{ $user->competencies->count() - 2 }}
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="editUser({{ $user->id }})"
                                            class="p-1.5 text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-900 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $user->id }})"
                                            class="p-1.5 text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                TIdak ada pengguna ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($this->users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $this->users->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $editingUser ? 'Edit User' : 'Create User' }}
                </h3>
            </div>

            <form wire:submit.prevent="{{ $editingUser ? 'updateUser' : 'createUser' }}" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input type="text" wire:model="form.name"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('form.name') border-red-500 @enderror">
                    @error('form.name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" wire:model="form.email"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('form.email') border-red-500 @enderror">
                    @error('form.email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                @if(!$editingUser)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" wire:model="form.password"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('form.password') border-red-500 @enderror">
                    @error('form.password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <select wire:model="form.role"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('form.role') border-red-500 @enderror">
                        <option value="user">User</option>
                        <option value="talent">Talent</option>
                    </select>
                    @error('form.role')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 flex items-center gap-2">
                        <svg wire:loading wire:target="createUser, updateUser" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ $editingUser ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Are you sure you want to delete <strong>{{ $userToDelete?->name }}</strong>? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <button wire:click="closeDeleteModal"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button wire:click="deleteUser"
                            class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Competencies Modal -->
    @if($showCompetencyModal && $competencyModalUser)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Competencies - {{ $competencyModalUser->name }}
                </h3>
                <button wire:click="closeCompetencyModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-lg p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                @if($competencyModalUser->competencies->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No competencies assigned</p>
                @else
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($competencyModalUser->competencies as $competency)
                            <span class="inline-flex px-3 py-1 text-sm bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300 rounded-full">
                                {{ $competency->name }} ({{ $competency->pivot->proficiency_level }})
                            </span>
                        @endforeach
                    </div>
                @endif
                <div class="flex justify-end">
                    <button wire:click="closeCompetencyModal"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
