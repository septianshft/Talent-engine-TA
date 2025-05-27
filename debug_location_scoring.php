yes <?php

// Use artisan tinker approach
require_once __DIR__ . '/bootstrap/app.php';

// Import required classes
use App\Models\Competency;
use App\Models\User;
use App\Models\TalentRequest;
use App\Services\EnhancedDecisionSupportService;

echo "=== Enhanced DSS Location Scoring Debug ===\n\n";

// Create competencies
$php = Competency::factory()->create(['name' => 'PHP']);
$laravel = Competency::factory()->create(['name' => 'Laravel']);

echo "Created competencies:\n";
echo "- PHP: ID {$php->id}\n";
echo "- Laravel: ID {$laravel->id}\n\n";

// Create talents
$talent1 = User::factory()->create([
    'domicile_city' => 'Jakarta',
    'domicile_country' => 'Indonesia'
]);
$talent1->competencies()->attach($php->id, ['proficiency_level' => 5]);
$talent1->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

$talent2 = User::factory()->create([
    'domicile_city' => 'Bandung',
    'domicile_country' => 'Indonesia'
]);
$talent2->competencies()->attach($php->id, ['proficiency_level' => 5]);
$talent2->competencies()->attach($laravel->id, ['proficiency_level' => 5]);

echo "Created talents:\n";
echo "- Talent1 (ID {$talent1->id}): Jakarta, PHP=5, Laravel=4\n";
echo "- Talent2 (ID {$talent2->id}): Bandung, PHP=5, Laravel=5\n\n";

// Create talent request
$talentRequest = TalentRequest::factory()->create([
    'work_location_city' => 'Jakarta'
]);

$talentRequest->competencies()->attach([
    $php->id => ['required_proficiency_level' => 4, 'weight' => 50],
    $laravel->id => ['required_proficiency_level' => 3, 'weight' => 50]
]);

echo "Created talent request:\n";
echo "- Work location: Jakarta\n";
echo "- PHP required: level 4, weight 50\n";
echo "- Laravel required: level 3, weight 50\n\n";

// Run the DSS
$service = new EnhancedDecisionSupportService();
$results = $service->findAndRankTalents($talentRequest);

echo "=== RESULTS ===\n";
foreach ($results as $index => $result) {
    $talent = $result['talent'];
    echo "Rank " . ($index + 1) . ": Talent {$talent->id} ({$talent->domicile_city})\n";
    echo "  - Total Score: " . number_format($result['total_score'], 4) . "\n";
    echo "  - Competency Score: " . number_format($result['competency_score'], 4) . "\n";
    echo "  - Location Score: " . number_format($result['location_score'], 4) . "\n";
    echo "  - Confidence: " . number_format($result['confidence_score'], 4) . "\n";

    if (isset($result['competency_details'])) {
        echo "  - Competency Details:\n";
        foreach ($result['competency_details'] as $comp_name => $details) {
            echo "    * {$comp_name}: level {$details['proficiency_level']}, score " . number_format($details['normalized_score'], 4) . "\n";
        }
    }
    echo "\n";
}

echo "Expected: Talent1 should rank higher due to exact location match\n";
echo "Actual: " . ($results->first()['talent']->id == $talent1->id ? "✓ CORRECT" : "✗ INCORRECT") . "\n";
