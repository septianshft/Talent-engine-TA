<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Updating Competency Categories...\n";

$updates = [
    // Programming Languages
    'PHP' => ['category' => 'Programming Languages', 'description' => 'Server-side scripting language'],
    'JavaScript' => ['category' => 'Programming Languages', 'description' => 'Client-side and server-side programming language'],
    'Python' => ['category' => 'Programming Languages', 'description' => 'General-purpose programming language'],

    // Frameworks
    'Laravel' => ['category' => 'Frameworks', 'description' => 'PHP web application framework'],
    'Vue.js' => ['category' => 'Frameworks', 'description' => 'Progressive JavaScript framework'],
    'React' => ['category' => 'Frameworks', 'description' => 'JavaScript library for building user interfaces'],

    // Database
    'SQL' => ['category' => 'Database', 'description' => 'Structured Query Language for database management'],
    'Database Design' => ['category' => 'Database', 'description' => 'Database architecture and optimization'],
    'MySQL' => ['category' => 'Database', 'description' => 'Relational database management system'],

    // Development
    'API Development' => ['category' => 'Development', 'description' => 'RESTful and GraphQL API development'],
    'Testing' => ['category' => 'Development', 'description' => 'Unit, integration, and end-to-end testing'],
    'Version Control' => ['category' => 'Development', 'description' => 'Git and collaborative development'],

    // Soft Skills
    'Project Management' => ['category' => 'Soft Skills', 'description' => 'Planning, organizing, and managing resources'],
    'Communication' => ['category' => 'Soft Skills', 'description' => 'Effective verbal and written communication'],
    'Problem Solving' => ['category' => 'Soft Skills', 'description' => 'Analytical thinking and troubleshooting'],
    'Leadership' => ['category' => 'Soft Skills', 'description' => 'Team leadership and mentoring abilities'],
];

$updated = 0;
$errors = 0;

foreach ($updates as $name => $data) {
    try {
        $competency = App\Models\Competency::where('name', $name)->first();
        if ($competency) {
            $competency->update($data);
            echo "✅ Updated: $name -> {$data['category']}\n";
            $updated++;
        } else {
            // Create if doesn't exist
            App\Models\Competency::create(array_merge(['name' => $name], $data));
            echo "✅ Created: $name -> {$data['category']}\n";
            $updated++;
        }
    } catch (Exception $e) {
        echo "❌ Error updating $name: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\nSummary:\n";
echo "Updated/Created: $updated\n";
echo "Errors: $errors\n";

// Test the query that was failing
try {
    echo "\nTesting the query...\n";
    $competencies = App\Models\Competency::orderBy('category')->orderBy('name')->get();
    $competencyCategories = $competencies->groupBy('category');

    echo "✅ SUCCESS: Query executed without errors!\n";
    echo "Total competencies: " . $competencies->count() . "\n";
    echo "Categories found: " . $competencyCategories->keys()->count() . "\n";

    foreach ($competencyCategories as $category => $items) {
        echo "- $category: " . $items->count() . " competencies\n";
        foreach ($items as $item) {
            echo "  * {$item->name}\n";
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\nUpdate completed.\n";
