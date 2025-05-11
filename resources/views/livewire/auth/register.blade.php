<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $phone_number = ''; // Add phone number property
    public string $role = 'user'; // Add role property, default to 'user'

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'phone_number' => ['required', 'string', 'max:20'], // Make phone number required
            'role' => ['required', 'string', 'in:user,talent'], // Restrict to non-admin roles
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Separate role from user data
        $roleName = $validated['role'];
        unset($validated['role']); // Remove role from data to be passed to User::create()

        $user = User::create($validated);
        event(new Registered($user));

        // Attach the selected role
        $roleModel = Role::where('name', $roleName)->first();
        if($roleModel) {
            $user->roles()->attach($roleModel);
        }

        Auth::login($user);

        // Redirect based on role after registration
        // Admin role is not selectable during registration, so this check is likely redundant here
        // but kept for consistency if logic changes.
        if ($user->hasRole('admin')) {
            $this->redirect(route('admin.dashboard'), navigate: true);
        } elseif ($user->hasRole('talent')) { // Corrected to use hasRole()
            // Assuming talent dashboard route is named 'talent.dashboard'
            // Make sure to define this route in routes/web.php
            $this->redirect(route('talent.dashboard'), navigate: true);
        } else {
            $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Phone Number -->
        <flux:input
            wire:model="phone_number"
            :label="__('Phone Number')"
            type="tel"
            required
            autocomplete="tel"
            placeholder="e.g., +6281234567890"
        />

        <!-- Role Selection -->
        <flux:select
            wire:model="role"
            :label="__('Account Type')"
            required
        >
            <option value="user">{{ __('user') }}</option>
            <option value="talent">{{ __('talent') }}</option>
        </flux:select>

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
