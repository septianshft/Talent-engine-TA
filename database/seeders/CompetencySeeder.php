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
            'PHP',
            'Laravel',
            'JavaScript',
            'Vue.js',
            'React',
            'SQL',
            'Database Design',
            'API Development',
            'Testing',
            'Project Management',
            'Communication',
            'Problem Solving',
        ];

        foreach ($competencies as $competency) {
            Competency::firstOrCreate(['name' => $competency]);
        }
    }
}
