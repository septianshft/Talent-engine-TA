<?php

// Set environment to testing
putenv('APP_ENV=testing');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check database connection
echo "Current DB Connection: " . config('database.default') . "\n";
echo "DB Config: " . json_encode(config('database.connections.sqlite')) . "\n";

// Test database
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "Database connection successful!\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
