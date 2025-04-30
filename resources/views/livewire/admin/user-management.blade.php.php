<?php
 
use function Livewire\Volt\{state, rules, computed};
use App\Models\User;
use Illuminate\Support\Facades\Hash;
 
state([
    'users' => fn() => User::where('role', '!=', 'admin')->paginate(10),
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
 
rules([
    'form.name' => 'required|min:2',
    'form.email' => 'required|email|unique:users,email',
    'form.password' => 'required|min:8',
    'form.role' => 'required|in:user,talent',
]);
 
$createUser = function() {
    $this->validate();
 
    User::create([
        'name' => $this->form['name'],
        'email' => $this->form['email'],
        'password' => Hash::make($this->form['password']),
        'role' => $this->form['role'],
    ]);
 
    $this->reset('form', 'showModal');
    $this->users = User::where('role', '!=', 'admin')->paginate(10);
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
 
    $this->reset('form', 'showModal', 'editingUser');
    $this->users = User::where('role', '!=', 'admin')->paginate(10);
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
        $this->users = User::where('role', '!=', 'admin')->paginate(10);
    }
    $this->reset('showDeleteModal', 'userToDelete');
};
 
$closeModal = function() {
    $this->reset('form', 'showModal', 'editingUser');
};
 
$closeDeleteModal = function() {
    $this->reset('showDeleteModal', 'userToDelete');
};