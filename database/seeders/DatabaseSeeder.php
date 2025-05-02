<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Competency;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected $faker; // Add faker property

    public function __construct()
    {
        $this->faker = \Faker\Factory::create(); // Initialize faker
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run Role and Competency Seeders first
        $this->call([
            RoleSeeder::class,
            CompetencySeeder::class,
        ]);

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        $talentRole = Role::where('name', 'talent')->first();

        // Get all competencies
        $allCompetencies = Competency::all();

        // Create Admin User
        User::factory()->hasAttached($adminRole)->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            // Password is 'password' (hashed by factory)
        ]);

        // Create Regular User
        User::factory()->hasAttached($userRole)->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ]);

        // Create Talent Users
        User::factory(200)->hasAttached($talentRole)->create()->each(function ($user) use ($allCompetencies) {
            // Ensure there are competencies to attach
            if ($allCompetencies->isNotEmpty()) {
                // Attach a random number of competencies (e.g., 2 to 5) from the full list
                $competenciesToAttach = $allCompetencies->random(min(rand(2, 5), $allCompetencies->count()));
                $attachData = [];
                foreach ($competenciesToAttach as $competency) {
                    $attachData[$competency->id] = ['proficiency_level' => $this->faker->numberBetween(1, 5)];
                }
                $user->competencies()->attach($attachData);
            }
        });

        // You can add TalentRequest seeding here later if needed
        // Example:
        // $requestingUser = User::whereHas('roles', fn($q) => $q->where('name', 'user'))->first();
        // $assignedTalent = User::whereHas('roles', fn($q) => $q->where('name', 'talent'))->first();
        // \App\Models\TalentRequest::factory()->create([
        //     'user_id' => $requestingUser->id,
        //     'talent_id' => $assignedTalent->id, // Optional: Assign talent later
        //     'details' => 'Need help with Laravel project.',
        //     'status' => 'pending_admin',
        // ]);
    }
}
