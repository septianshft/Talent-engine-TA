<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\CompetencyController;
use App\Http\Controllers\User\TalentRequestController as UserTalentRequestController;
use App\Http\Controllers\Talent\TalentRequestController as TalentTalentRequestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'role:user']) // Add role:user middleware
    ->name('dashboard');

// User Talent Request Routes
Route::middleware(['auth', 'verified', 'role:user'])->prefix('requests')->name('user.requests.')->group(function () {
    Route::get('/', [App\Http\Controllers\User\TalentRequestController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\User\TalentRequestController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\User\TalentRequestController::class, 'store'])->name('store');
    Route::delete('/{talentRequest}', [App\Http\Controllers\User\TalentRequestController::class, 'destroy'])->name('destroy'); // Assuming users can delete their requests before approval
});

// Admin Dashboard Route
Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

// Admin Talent Request Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin/talent-requests')->name('admin.talent-requests.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\TalentRequestController::class, 'index'])->name('index');
    Route::get('/{talentRequest}', [App\Http\Controllers\Admin\TalentRequestController::class, 'show'])->name('show');
    Route::patch('/{talentRequest}', [App\Http\Controllers\Admin\TalentRequestController::class, 'update'])->name('update'); // For rejection
    Route::patch('/{talentRequest}/assign', [App\Http\Controllers\Admin\TalentRequestController::class, 'assign'])->name('assign');
    Route::patch('/{talentRequest}/complete', [App\Http\Controllers\Admin\TalentRequestController::class, 'markAsCompleted'])->name('complete'); // Mark as completed
});

// Admin Competency Management
Route::resource('admin/competencies', App\Http\Controllers\Admin\CompetencyController::class)
    ->names('admin.competencies') // Explicitly set the route name prefix
    ->middleware(['auth', 'verified', 'role:admin'])
    ->except(['show']); // Assuming show view is not needed for simple competency names

// Talent Dashboard Route
Route::view('talent/dashboard', 'talent.dashboard') // Assuming you will create a talent.dashboard view
    ->middleware(['auth', 'verified', 'role:talent'])
    ->name('talent.dashboard');

// Talent Request Routes (for Talents)
Route::middleware(['auth', 'verified', 'role:talent'])->prefix('talent/requests')->name('talent.requests.')->group(function () {
    Route::get('/', [App\Http\Controllers\Talent\TalentRequestController::class, 'index'])->name('index'); // List received requests
    Route::get('/{talentRequest}', [App\Http\Controllers\Talent\TalentRequestController::class, 'show'])->name('show'); // View a specific request
    Route::patch('/{talentRequest}', [App\Http\Controllers\Talent\TalentRequestController::class, 'update'])->name('update'); // Respond to a request (e.g., accept/reject)
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
