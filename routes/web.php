<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'role:user']) // Add role:user middleware
    ->name('dashboard');

// Admin Dashboard Route
Route::view('admin/dashboard', 'admin.dashboard') // Assuming you have an admin.dashboard view
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

// Talent Dashboard Route
Route::view('talent/dashboard', 'talent.dashboard') // Assuming you will create a talent.dashboard view
    ->middleware(['auth', 'verified', 'role:talent'])
    ->name('talent.dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
