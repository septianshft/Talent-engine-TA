<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking current competencies...\n\n";

try {
    $competencies = App\Models\Competency::all();
    echo "Total competencies found: " . $competencies->count() . "\n\n";

    foreach ($competencies as $competency) {
        echo "ID: {$competency->id}, Name: {$competency->name}, Category: {$competency->category}\n";
    }

    echo "\nCategories summary:\n";
    $categories = $competencies->groupBy('category');
    foreach ($categories as $category => $items) {
        echo "- $category: " . $items->count() . " items\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
