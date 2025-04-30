<?php

use function Livewire\Volt\{state, rules, computed};
use App\Models\User;
use Illuminate\Support\Facades\Hash;

state([
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
    return User::where('role', '!=', 'admin')->paginate(10);
});

$validate = [
    'form.name' => 'required|min:2',
    'form.email' => 'required|email|unique:users,email',
    'form.password' => 'required|min:8',
    'form.role' => 'required|in:user,talent',
];

$createUser = function() use ($validate) {
    $this->validate($validate);

    User::create([
        'name' => $this->form['name'],
        'email' => $this->form['email'],
        'password' => Hash::make($this->form['password']),
        'role' => $this->form['role'],
    ]);

    $this->showModal = false;
    $this->resetForm();
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
        'role' => $this->form['role'],
    ]);

    $this->showModal = false;
    $this->resetForm();
    $this->editingUser = null;
};

$editUser = function(User $user) {
    $this->editingUser = $user;
    $this->form = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
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

$closeModal = function() {
    $this->showModal = false;
    $this->resetForm();
    $this->editingUser = null;
};

$closeDeleteModal = function() {
    $this->showDeleteModal = false;
    $this->userToDelete = null;
};
?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium">User Management</h3>
        <button wire:click="$set('showModal', true)" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            Add New User
        </button>
    </div>

    <!-- Create/Edit User Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-medium mb-4">{{ $editingUser ? 'Edit User' : 'Create New User' }}</h3>
            
            <form wire:submit.prevent="{{ $editingUser ? 'updateUser' : 'createUser' }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input type="text" wire:model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700">
                        @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" wire:model="form.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700">
                        @error('form.email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    @if(!$editingUser)
                    <div>
                        <label class="block text-sm font-medium">Password</label>
                        <input type="password" wire:model="form.password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700">
                        @error('form.password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium">Role</label>
                        <select wire:model="form.role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700">
                            <option value="user">User</option>
                            <option value="talent">Talent</option>
                        </select>
                        @error('form.role') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        {{ $editingUser ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-medium mb-4">Confirm Delete</h3>
            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button wire:click="closeDeleteModal" class="px-4 py-2 border rounded-md">Cancel</button>
                <button wire:click="deleteUser" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Users Table -->
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($this->users as $user)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $user->role }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <button wire:click="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                    <button wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $this->users->links() }}
    </div>
</div>