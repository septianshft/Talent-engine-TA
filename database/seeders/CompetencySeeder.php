<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Competency;

class CompetencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competencies = [
            // Programming Languages
            ['name' => 'PHP', 'category' => 'Programming Languages', 'description' => 'Server-side scripting language'],
            ['name' => 'JavaScript', 'category' => 'Programming Languages', 'description' => 'Client-side and server-side programming language'],
            ['name' => 'Python', 'category' => 'Programming Languages', 'description' => 'General-purpose programming language'],

            // Frameworks
            ['name' => 'Laravel', 'category' => 'Frameworks', 'description' => 'PHP web application framework'],
            ['name' => 'Vue.js', 'category' => 'Frameworks', 'description' => 'Progressive JavaScript framework'],
            ['name' => 'React', 'category' => 'Frameworks', 'description' => 'JavaScript library for building user interfaces'],

            // Database
            ['name' => 'SQL', 'category' => 'Database', 'description' => 'Structured Query Language for database management'],
            ['name' => 'Database Design', 'category' => 'Database', 'description' => 'Database architecture and optimization'],
            ['name' => 'MySQL', 'category' => 'Database', 'description' => 'Relational database management system'],

            // Development
            ['name' => 'API Development', 'category' => 'Development', 'description' => 'RESTful and GraphQL API development'],
            ['name' => 'Testing', 'category' => 'Development', 'description' => 'Unit, integration, and end-to-end testing'],
            ['name' => 'Version Control', 'category' => 'Development', 'description' => 'Git and collaborative development'],

            // Soft Skills
            ['name' => 'Project Management', 'category' => 'Soft Skills', 'description' => 'Planning, organizing, and managing resources'],
            ['name' => 'Communication', 'category' => 'Soft Skills', 'description' => 'Effective verbal and written communication'],
            ['name' => 'Problem Solving', 'category' => 'Soft Skills', 'description' => 'Analytical thinking and troubleshooting'],
            ['name' => 'Leadership', 'category' => 'Soft Skills', 'description' => 'Team leadership and mentoring abilities'],
        ];

        foreach ($competencies as $competency) {
            Competency::firstOrCreate(
                ['name' => $competency['name']],
                $competency
            );
        }
    }
}
