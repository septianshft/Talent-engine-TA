<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Application;

// Initialize Laravel
$app = new Application(
    realpath(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Load the environment
$app->loadEnvironmentFrom('.env');

// Bootstrap Laravel
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

echo "Testing role assignment...\n";

// Test creating a talent and checking hasRole
$talent = User::factory()->create();
echo "Created talent with ID: " . $talent->id . "\n";

$talentRole = Role::firstOrCreate(['name' => 'talent']);
echo "Talent role ID: " . $talentRole->id . "\n";

echo "Before attaching role - hasRole: " . ($talent->hasRole('talent') ? 'YES' : 'NO') . "\n";

$talent->roles()->attach($talentRole);
echo "Attached role\n";

// Refresh the model to load relations
$talent->refresh();
echo "After refresh - hasRole: " . ($talent->hasRole('talent') ? 'YES' : 'NO') . "\n";

// Load roles relationship explicitly
$talent->load('roles');
echo "After loading roles - hasRole: " . ($talent->hasRole('talent') ? 'YES' : 'NO') . "\n";

echo "Number of roles: " . $talent->roles->count() . "\n";
echo "Role names: " . $talent->roles->pluck('name')->implode(', ') . "\n";
