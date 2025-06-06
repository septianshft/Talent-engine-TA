<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competency>
 */
class CompetencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Programming Languages',
            'Frameworks',
            'Database',
            'Development',
            'Soft Skills',
            'Design',
            'DevOps'
        ];

        return [
            'name' => $this->faker->unique()->jobTitle,
            'category' => $this->faker->randomElement($categories),
            'description' => $this->faker->sentence(),
        ];
    }
}
