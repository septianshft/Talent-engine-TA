<?php

use function Livewire\Volt\{state, rules, computed};
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

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
]);

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
    // Fetch users who do not have the 'admin' role
    return User::whereDoesntHave('roles', function ($query) {
        $query->where('name', 'admin');
    })
    ->with(['roles', 'competencies']) // Eager load roles and competencies
    ->paginate(10);
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
<div class="min-h-screen bg-white-50 dark:bg-gray-900 p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 space-y-4 sm:space-y-0">
        <div class="text-left">
            <h1 class="text-3xl font-bold text-black-900 dark:text-white">👥 User Management</h1>
            <p class="mt-1 text-black-600 dark:text-gray-400">Manage your platform users efficiently</p>
        </div>
        <button wire:click="$set('showModal', true)" 
                class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New User
        </button>
    </div>

    <!-- User Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden"> {{-- Added overflow-hidden --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between"> {{-- Increased padding --}}
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                    {{ $this->users->total() }} users found
                </span>
            </div>
            {{-- Optional: Add search/filter controls here --}}
        </div>

        <div class="overflow-x-auto"> {{-- Added container for horizontal scroll on small screens --}}
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/4">User</th> {{-- Adjusted padding/text size --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/4">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/6">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/3">Competencies</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Actions</th> {{-- Adjusted min-width --}}
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150 ease-in-out"> {{-- Added transition --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-tr from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</div>                              
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 truncate">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $roleName = $user->roles->first()?->name ?? 'user';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $roleName === 'talent' ? 'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400' : 
                                'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400' }}">
                                {{ ucfirst($roleName) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            @if ($roleName === 'talent' && $user->competencies->isNotEmpty())
                                @php $competencies = $user->competencies; $limit = 3; @endphp
                                <div class="flex flex-wrap items-center gap-1">
                                    @foreach ($competencies->take($limit) as $competency)
                                        <span class="px-3 py-1.5 inline-flex items-center text-xs font-medium rounded-md border border-purple-300 dark:border-purple-700 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/50 dark:hover:bg-purple-800/60 text-purple-700 dark:text-purple-300 transition-colors cursor-default whitespace-nowrap">
                                            {{ $competency->name }}&nbsp;({{ $competency->pivot->proficiency_level }})
                                        </span>
                                    @endforeach
                                    @if ($competencies->count() > $limit)
                                        <button wire:click="openCompetencyModal({{ $user->id }})" 
                                                class="px-2.5 py-1 text-xs font-semibold rounded-full transition-colors 
                                                       bg-gray-200 dark:bg-gray-600 text-blue-700 dark:text-blue-300 
                                                       hover:bg-gray-300 dark:hover:bg-gray-500 
                                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                            +{{ $competencies->count() - $limit }} more
                                        </button>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">-</span> {{-- Styled placeholder --}}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3"> {{-- Increased spacing --}}
                            <button wire:click="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-150 p-1 rounded hover:bg-blue-100 dark:hover:bg-gray-700"> {{-- Added padding/hover bg --}}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-150 p-1 rounded hover:bg-red-100 dark:hover:bg-gray-700"> {{-- Added padding/hover bg --}}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($this->users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $this->users->links() }}
        </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300" 
         x-data="{ show: @entangle('showModal') }"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $editingUser ? 'Edit User' : 'Create New User' }}
                </h3>
                <button wire:click="closeModal" class="p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="{{ $editingUser ? 'updateUser' : 'createUser' }}" class="p-6">
                <div class="space-y-6">
                    <!-- Name Input -->
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input id="name" type="text" wire:model="form.name" 
                                   class="form-input pl-10 @error('form.name') form-input-error @enderror"
                                   placeholder="Enter user's full name">
                        </div>
                        @error('form.name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Email Input -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                            </span>
                            <input id="email" type="email" wire:model="form.email" 
                                   class="form-input pl-10 @error('form.email') form-input-error @enderror"
                                   placeholder="e.g., user@example.com">
                        </div>
                        @error('form.email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Password Input -->
                    @if(!$editingUser)
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input id="password" type="password" wire:model="form.password" 
                                   class="form-input pl-10 @error('form.password') form-input-error @enderror"
                                   placeholder="Enter a secure password (min. 8 characters)">
                        </div>
                        @error('form.password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    @endif

                    <!-- Role Select -->
                    <div class="form-group">
                        <label for="role" class="form-label">Assign Role</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </span>
                            <select id="role" wire:model="form.role" class="form-input pl-10 @error('form.role') form-input-error @enderror">
                                <option value="user">User</option>
                                <option value="talent">Talent</option>
                            </select>
                        </div>
                        @error('form.role')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4 pt-8 mt-8 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="closeModal" class="btn-secondary inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary inline-flex items-center" wire:loading.attr="disabled" wire:target="createUser, updateUser">
                        <svg wire:loading wire:target="createUser, updateUser" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="createUser, updateUser" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        <span wire:loading.remove wire:target="createUser, updateUser">{{ $editingUser ? 'Save Changes' : 'Create User' }}</span>
                        <span wire:loading wire:target="createUser, updateUser">Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirm Delete</h3>
                <button wire:click="closeDeleteModal" class="p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <p class="text-gray- 600 dark:text-gray-300">Are you sure you want to delete user <strong class="font-medium">{{ $userToDelete?->name }}</strong>? This action cannot be undone.</p>
                
                <div class="flex justify-end space-x-3">
                    <button wire:click="closeDeleteModal" class="btn-secondary">Cancel</button>
                    <button wire:click="deleteUser " class="btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Competencies Modal -->
    @if($showCompetencyModal && $competencyModalUser)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300"
         x-data="{ show: @entangle('showCompetencyModal') }"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300"
             @click.away="show = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Competencies for {{ $competencyModalUser->name }}
                </h3>
                <button wire:click="closeCompetencyModal" class="p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="flex flex-wrap gap-2">
                    @foreach ($competencyModalUser->competencies as $competency)
                        <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400">
                            {{ $competency->name }} ({{ $competency->pivot->proficiency_level }})
                        </span>
                    @endforeach
                </div>
            </div>

             <!-- Footer -->
            <div class="flex justify-end gap-4 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="closeCompetencyModal" class="btn-secondary inline-flex items-center">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

    <style>
        .btn-primary {
            @apply px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 
                   text-white font-semibold rounded-xl shadow-sm transition-all duration-200 transform 
                   hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
                   dark:focus:ring-offset-gray-900 flex items-center;
        }

        .btn-secondary {
            @apply px-6 py-2.5 bg-transparent border border-gray-300 dark:border-gray-600 
                   text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 
                   font-semibold rounded-lg 
                   transition-colors duration-200;
        }

        .btn-danger {
            @apply px-6 py-2.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white 
                   uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none 
                   focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150;
        }

        .form-group {
            @apply space-y-2;
        }

        .form-label {
            @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1;
        }

        .form-input {
            @apply w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 
                   text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none
                   transition-all duration-200;
        }

        .form-input-error {
            @apply border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500;
        }

        .form-error {
            @apply text-sm text-red-500 dark:text-red-400 mt-1;
        }

        /* Dark Mode Overrides */
        .dark .form-input {
            @apply bg-gray-800 border-gray-700;
        }
    </style>
</div>