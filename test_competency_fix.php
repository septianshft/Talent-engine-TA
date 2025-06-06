<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Competency Category Fix...\n";

try {
    // Test the query that was failing
    $competencies = App\Models\Competency::orderBy('category')->orderBy('name')->get();
    $competencyCategories = $competencies->groupBy('category');

    echo "✅ SUCCESS: Query executed without errors!\n";
    echo "Total competencies: " . $competencies->count() . "\n";
    echo "Categories found: " . $competencyCategories->keys()->count() . "\n";

    foreach ($competencyCategories as $category => $items) {
        echo "- $category: " . $items->count() . " competencies\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
