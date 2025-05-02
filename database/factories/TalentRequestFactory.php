<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TalentRequest>
 */
class TalentRequestFactory extends Factory
{
    protected $model = \App\Models\TalentRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Remove default user_id and talent_id to force explicit assignment in tests
        return [
            'details' => $this->faker->paragraph,
            'status' => 'pending_user', // Default status
        ];
    }
}
