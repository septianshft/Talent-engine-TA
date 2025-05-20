<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Competency;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run Role and Competency Seeders first
        $this->call([
            RoleSeeder::class,
            CompetencySeeder::class, // Assuming this seeds a few essential competencies
        ]);

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        $talentRole = Role::where('name', 'talent')->first();
        $allCompetencies = Competency::all();

        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'), // Consider using Hash::make()
            ]
        )->roles()->syncWithoutDetaching($adminRole->id);

        // Create one Regular User (for creating talent requests)
        User::factory()->create([
            'name' => 'Test Requester User',
            'email' => 'requester@example.com',
        ])->roles()->attach($userRole);

        // Create multiple Talent Users
        $numberOfTalentsToCreate = 350; // You can adjust this number
        for ($i = 0; $i < $numberOfTalentsToCreate; $i++) {
            $talentUser = User::factory()->create([
                'name' => $this->faker->name . ' (Talent)',
                'email' => $this->faker->unique()->safeEmail,
            ]);
            $talentUser->roles()->attach($talentRole);

            // Assign a few competencies to each talent user
            if ($allCompetencies->isNotEmpty()) {
                // Ensure at least 2 competencies, and at most 5 or total available if less than 5
                $competenciesToAttachCount = $this->faker->numberBetween(2, min(5, $allCompetencies->count()));
                $competenciesToAttach = $allCompetencies->random($competenciesToAttachCount);

                $attachData = [];
                // Handle cases where random() might return a single item or a collection
                if ($competenciesToAttach instanceof Competency) { // Single competency returned
                    $attachData[$competenciesToAttach->id] = ['proficiency_level' => $this->faker->numberBetween(1, 4)];
                } else { // Collection of competencies returned
                    foreach ($competenciesToAttach as $competency) {
                        $attachData[$competency->id] = ['proficiency_level' => $this->faker->numberBetween(1, 4)];
                    }
                }

                if (!empty($attachData)) {
                    $talentUser->competencies()->attach($attachData);
                }
            }
        }

        // All previous TalentRequest::factory() calls are removed.
        // You will create TalentRequests manually through the application.

        $this->command->info('Database seeded with essential roles, competencies, an admin, a requester, and ' . $numberOfTalentsToCreate . ' talent users.');
        $this->command->info('No TalentRequests have been seeded automatically.');
    }
}
