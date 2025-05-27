<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DebugEnvironmentTest extends TestCase
{
    /** @test */
    public function it_shows_current_environment_configuration()
    {
        // Debug environment variables
        echo "\n=== ENVIRONMENT DEBUG ===\n";
        echo "APP_ENV: " . env('APP_ENV') . "\n";
        echo "DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
        echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";

        // Debug config values
        echo "\n=== CONFIG DEBUG ===\n";
        echo "config('app.env'): " . config('app.env') . "\n";
        echo "config('database.default'): " . config('database.default') . "\n";
        echo "config('database.connections.sqlite.database'): " . config('database.connections.sqlite.database') . "\n";

        // Debug actual database connection
        echo "\n=== DATABASE CONNECTION DEBUG ===\n";
        try {
            $connection = DB::connection();
            echo "Current connection name: " . $connection->getName() . "\n";
            echo "Current driver: " . $connection->getDriverName() . "\n";
            echo "Database config: " . json_encode($connection->getConfig()) . "\n";
        } catch (\Exception $e) {
            echo "Database connection error: " . $e->getMessage() . "\n";
        }

        // This assertion will always pass - we just want to see the debug output
        $this->assertTrue(true);
    }
}
