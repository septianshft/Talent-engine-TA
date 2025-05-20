<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role; // Import Role model

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->optional()->phoneNumber(), // Add phone number
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // 'role' => 'user', // Removed
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        // Remove automatic default role attachment.
        // Roles should be explicitly assigned via states (e.g., isAdmin(), isTalent())
        // or directly in seeders/tests (e.g., using hasAttached() or afterCreating() hooks).
        return $this;
    }

    /**
     * Indicate that the user is an admin.
     */
    public function isAdmin(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            $user->roles()->syncWithoutDetaching([$adminRole->id]); // Use syncWithoutDetaching to avoid removing default role if needed
        });
    }

    /**
     * Indicate that the user is a talent.
     */
    public function isTalent(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $talentRole = Role::firstOrCreate(['name' => 'talent']);
            $user->roles()->syncWithoutDetaching([$talentRole->id]);
        });
    }
}
