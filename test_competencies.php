<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "Testing competencies relationship...\n";

// Test if users with talent role exist
$talentUsers = User::whereHas('roles', function ($q) {
    $q->where('name', 'talent');
})->get();

echo "Found " . $talentUsers->count() . " talent users\n";

if ($talentUsers->count() > 0) {
    $firstTalent = $talentUsers->first();
    echo "First talent: " . $firstTalent->name . "\n";
    
    // Test competencies relationship
    try {
        $competenciesCount = $firstTalent->competencies()->count();
        echo "Competencies count for first talent: " . $competenciesCount . "\n";
        
        // Test withCount
        $userWithCount = User::withCount('competencies')->find($firstTalent->id);
        echo "Using withCount - competencies_count: " . $userWithCount->competencies_count . "\n";
        
    } catch (Exception $e) {
        echo "Error testing competencies: " . $e->getMessage() . "\n";
    }
}

// Test direct table query
try {
    $competencyUserCount = DB::table('competency_user')->count();
    echo "Total records in competency_user table: " . $competencyUserCount . "\n";
} catch (Exception $e) {
    echo "Error querying competency_user table: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
