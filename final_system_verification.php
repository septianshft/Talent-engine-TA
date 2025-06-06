<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FINAL SYSTEM VERIFICATION ===\n\n";

// Test 1: Database Connection and Competencies
echo "1. Testing Database Connection and Competencies:\n";
try {
    $competencies = App\Models\Competency::all();
    echo "   ✅ Database connection: SUCCESS\n";
    echo "   ✅ Total competencies: " . $competencies->count() . "\n";

    $categories = $competencies->groupBy('category');
    echo "   ✅ Categories found: " . $categories->keys()->count() . "\n";

    foreach ($categories as $category => $items) {
        echo "      - $category: " . $items->count() . " items\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 2: Competency Query Test (the one that was failing)
echo "\n2. Testing Competency Ordering Query:\n";
try {
    $competencies = App\Models\Competency::orderBy('category')->orderBy('name')->get();
    echo "   ✅ ORDER BY category query: SUCCESS\n";
    echo "   ✅ Records retrieved: " . $competencies->count() . "\n";
} catch (Exception $e) {
    echo "   ❌ Query error: " . $e->getMessage() . "\n";
}

// Test 3: Routes Test
echo "\n3. Testing Route Resolution:\n";
try {
    // Test the problematic route that was fixed
    $route = app('router')->getRoutes()->getByName('user.requests.create-direct');
    if ($route) {
        echo "   ✅ Route 'user.requests.create-direct' exists\n";
        echo "   ✅ Route URI: " . $route->uri() . "\n";
        echo "   ✅ Route methods: " . implode(', ', $route->methods()) . "\n";
    } else {
        echo "   ❌ Route not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Route error: " . $e->getMessage() . "\n";
}

// Test 4: Talent Model Test
echo "\n4. Testing Talent Model:\n";
try {
    $talentCount = App\Models\Talent::count();
    echo "   ✅ Talent model accessible\n";
    echo "   ✅ Total talents: " . $talentCount . "\n";
} catch (Exception $e) {
    echo "   ❌ Talent model error: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "System Status: ";

// Overall system health check
try {
    App\Models\Competency::orderBy('category')->first();
    App\Models\Talent::first();
    echo "🟢 HEALTHY\n";
} catch (Exception $e) {
    echo "🔴 ISSUES DETECTED\n";
}

echo "\nTimestamp: " . date('Y-m-d H:i:s') . "\n";
