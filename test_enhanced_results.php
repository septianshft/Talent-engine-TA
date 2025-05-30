<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->singleton('path.config', function () {
    return __DIR__ . '/config';
});

$app->singleton('path.public', function () {
    return __DIR__ . '/public';
});

$app->singleton('path.storage', function () {
    return __DIR__ . '/storage';
});

$app->singleton('path.database', function () {
    return __DIR__ . '/database';
});

$app->singleton('path.resources', function () {
    return __DIR__ . '/resources';
});

$app->singleton('path.bootstrap', function () {
    return __DIR__ . '/bootstrap';
});

echo "Testing Enhanced DSS Service instantiation...\n";

try {
    $service = $app->make(\App\Services\EnhancedDecisionSupportService::class);
    echo "✅ Enhanced DSS Service created successfully\n";

    // Test if the getBasicSAWResults method exists
    if (method_exists($service, 'getBasicSAWResults')) {
        echo "✅ getBasicSAWResults method exists\n";
    } else {
        echo "❌ getBasicSAWResults method missing\n";
    }

    // Test if the findAndRankTalents method exists
    if (method_exists($service, 'findAndRankTalents')) {
        echo "✅ findAndRankTalents method exists\n";
    } else {
        echo "❌ findAndRankTalents method missing\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nTesting Controller method exists...\n";

try {
    $controller = $app->make(\App\Http\Controllers\User\TalentRequestController::class);

    if (method_exists($controller, 'enhancedResults')) {
        echo "✅ enhancedResults method exists in TalentRequestController\n";
    } else {
        echo "❌ enhancedResults method missing in TalentRequestController\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nAll tests completed!\n";
