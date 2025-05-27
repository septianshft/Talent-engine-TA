<?php

require_once 'vendor/autoload.php';

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

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Competency;
use App\Models\TalentRequest;
use App\Services\EnhancedDecisionSupportService;

echo "Debugging confidence scores...\n";

// Create competencies
$php = Competency::factory()->create(['name' => 'PHP']);
$laravel = Competency::factory()->create(['name' => 'Laravel']);

// Create a high talent
$highTalent = User::factory()->create([
    'role' => 'talent',
    'domicile_city' => 'Jakarta',
    'domicile_country' => 'Indonesia'
]);
$highTalent->competencies()->attach($php->id, ['proficiency_level' => 5]);
$highTalent->competencies()->attach($laravel->id, ['proficiency_level' => 5]);

// Create talent request
$talentRequest = TalentRequest::factory()->create([
    'work_location_city' => 'Jakarta',
    'work_location_country' => 'Indonesia',
    'work_location_type' => 'on-site'
]);

$talentRequest->competencies()->attach([
    $php->id => ['required_proficiency_level' => 3, 'weight' => 50],
    $laravel->id => ['required_proficiency_level' => 3, 'weight' => 50]
]);

// Test confidence scoring
$service = new EnhancedDecisionSupportService();
$results = $service->findAndRankTalents($talentRequest);

echo "Results count: " . count($results) . "\n";
echo "MIN_CONFIDENCE_SCORE: " . EnhancedDecisionSupportService::MIN_CONFIDENCE_SCORE . "\n";

foreach ($results as $result) {
    echo "Talent ID: " . $result['talent_id'] . "\n";
    echo "Confidence Score: " . $result['confidence_score'] . "\n";
    echo "Overall Score: " . $result['overall_score'] . "\n";
    echo "---\n";
}
