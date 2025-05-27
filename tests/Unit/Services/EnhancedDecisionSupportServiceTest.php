<?php

namespace Tests\Unit\Services;

use App\Models\TalentRequest;
use App\Models\User;
use App\Models\Competency;
use App\Models\Role;
use App\Services\EnhancedDecisionSupportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class EnhancedDecisionSupportServiceTest extends TestCase
{
    use RefreshDatabase;

    private EnhancedDecisionSupportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EnhancedDecisionSupportService();

        // Seed roles
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /**
     * Create a talent user with the proper role
     */
    private function createTalent(array $attributes = []): User
    {
        $talent = User::factory()->create($attributes);
        $talentRole = Role::where('name', 'talent')->first();
        $talent->roles()->attach($talentRole);
        return $talent;
    }

    /** @test */
    public function it_allows_single_competency_weight_up_to_100_percent()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        $talentRequest = TalentRequest::factory()->create();
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 4, 'weight' => 100],
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 0]
        ]);

        $talent = $this->createTalent();
        $talent->competencies()->attach($php->id, ['proficiency_level' => 5]);
        $talent->competencies()->attach($laravel->id, ['proficiency_level' => 5]);


        // No ValidationException should be thrown.
        $results = $this->service->findAndRankTalents($talentRequest);
        $this->assertNotNull($results); // Check that the service runs without exception
        // Potentially add more assertions about the results if specific behavior is expected
    }

    /** @test */
    public function it_allows_zero_weight_for_all_competencies()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        $talentRequest = TalentRequest::factory()->create();
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 4, 'weight' => 0],
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 0]
        ]);

        $talent = $this->createTalent(['domicile_city' => 'TestCity']); // Ensure talent has a location for location score
        $talent->competencies()->attach($php->id, ['proficiency_level' => 5]);
        $talent->competencies()->attach($laravel->id, ['proficiency_level' => 5]);

        // No ValidationException should be thrown.
        $results = $this->service->findAndRankTalents($talentRequest);
        $this->assertNotNull($results);

        if ($results->isNotEmpty()) {
            $firstResult = $results->first();
            $this->assertArrayHasKey('competency_scores', $firstResult);
            $this->assertArrayHasKey('location_score', $firstResult);
            $this->assertArrayHasKey('saw_score', $firstResult);

            // Check that competency scores are zero or handled as expected
            foreach ($firstResult['competency_scores'] as $competencyName => $score) {
                $this->assertEquals(0, $score, "Normalized score for {$competencyName} should be 0 when all weights are 0");
            }
            // The overall SAW score should be influenced only by location score if all competency weights are 0
            // Assuming location weight is a constant in EnhancedDecisionSupportService
            $expectedSawScore = $firstResult['location_score'] * EnhancedDecisionSupportService::LOCATION_WEIGHT_PERCENTAGE;
            $this->assertEquals($expectedSawScore, $firstResult['saw_score'], 0.001, "SAW score should primarily reflect location score when competency weights are zero.");
        }
    }

    /** @test */
    public function it_normalize_weights_handles_all_zero_weights_gracefully()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $talentRequest = TalentRequest::factory()->create();
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 3, 'weight' => 0],
        ]);

        $talent = $this->createTalent();
        $talent->competencies()->attach($php->id, ['proficiency_level' => 5]);

        $results = $this->service->findAndRankTalents($talentRequest);
        $this->assertNotNull($results);

        if ($results->isNotEmpty()) {
            $firstResult = $results->first();
            $this->assertArrayHasKey('details', $firstResult);
            $this->assertArrayHasKey('normalized_weights', $firstResult['details']);
            // Expect normalized weight to be 0 if original weight was 0 and total weight was 0
            $this->assertEquals(0, $firstResult['details']['normalized_weights']['PHP']);
        }
    }

    /** @test */
    public function it_ranks_correctly_with_varied_weights_0_to_100_percent()
    {
        $c1 = Competency::factory()->create(['name' => 'Comp1']); // High weight
        $c2 = Competency::factory()->create(['name' => 'Comp2']); // Medium weight
        $c3 = Competency::factory()->create(['name' => 'Comp3']); // Zero weight

        $talentRequest = TalentRequest::factory()->create(['work_location_city' => 'Anytown']);
        $talentRequest->competencies()->attach([
            $c1->id => ['required_proficiency_level' => 3, 'weight' => 70],
            $c2->id => ['required_proficiency_level' => 3, 'weight' => 30],
            $c3->id => ['required_proficiency_level' => 3, 'weight' => 0],
        ]);

        // Talent Strong in C1, moderate in C2, weak in C3 (C3 doesn't matter due to 0 weight)
        $talentA = $this->createTalent(['name' => 'Talent A', 'domicile_city' => 'Anytown']);
        $talentA->competencies()->attach($c1->id, ['proficiency_level' => 5]);
        $talentA->competencies()->attach($c2->id, ['proficiency_level' => 3]);
        $talentA->competencies()->attach($c3->id, ['proficiency_level' => 1]);

        // Talent Moderate in C1, strong in C2, strong in C3
        $talentB = $this->createTalent(['name' => 'Talent B', 'domicile_city' => 'Anytown']);
        $talentB->competencies()->attach($c1->id, ['proficiency_level' => 3]);
        $talentB->competencies()->attach($c2->id, ['proficiency_level' => 5]);
        $talentB->competencies()->attach($c3->id, ['proficiency_level' => 5]);

        $results = $this->service->findAndRankTalents($talentRequest);

        $this->assertCount(2, $results);
        $this->assertEquals($talentA->id, $results->first()['talent']->id, "Talent A should be ranked higher due to higher score in more heavily weighted competency.");
        $this->assertEquals($talentB->id, $results->last()['talent']->id);

        // Verify SAW scores reflect the weighting
        $scoreA = $results->firstWhere('talent.id', $talentA->id)['saw_score'];
        $scoreB = $results->firstWhere('talent.id', $talentB->id)['saw_score'];
        $this->assertGreaterThan($scoreB, $scoreA);
    }


    /** @test */
    public function it_enforces_veto_thresholds_for_critical_competencies()
    {
        // Create competencies
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        // Create talent with insufficient critical competency
        $talent = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]);
        $talent->competencies()->attach($php->id, ['proficiency_level' => 2]); // Low level
        $talent->competencies()->attach($laravel->id, ['proficiency_level' => 5]);

        $talentRequest = TalentRequest::factory()->create([
            'work_location_city' => 'Jakarta'
        ]);

        // Attach competencies with weights using proper relationship
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 5, 'weight' => 50, 'is_critical' => true], // Critical competency
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 50]
        ]);

        $results = $this->service->findAndRankTalents($talentRequest);

        // Should be filtered out due to veto threshold
        $this->assertCount(0, $results);
    }

    /** @test */
    public function it_properly_normalizes_different_scales()
    {
        // Create competencies
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        // Create two talents with different locations
        $talent1 = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]); // Exact match
        $talent1->competencies()->attach($php->id, ['proficiency_level' => 5]);
        $talent1->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

        $talent2 = $this->createTalent([
            'domicile_city' => 'Bandung',
            'domicile_country' => 'Indonesia'
        ]); // Different location
        $talent2->competencies()->attach($php->id, ['proficiency_level' => 5]);
        $talent2->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

        $talentRequest = TalentRequest::factory()->create([
            'work_location_city' => 'Jakarta',
            'work_location_country' => 'Indonesia',
            'work_location_type' => 'on-site'
        ]);

        // Attach competencies with weights using proper relationship
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 4, 'weight' => 50],
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 50]
        ]);

        $results = $this->service->findAndRankTalents($talentRequest);

        $this->assertCount(2, $results);

        // talent1 should rank higher due to exact location match
        // when competency scores are equal
        $topTalent = $results->first();
        $this->assertEquals($talent1->id, $topTalent['talent']->id);
    }

    /** @test */
    public function it_calculates_confidence_scores_correctly()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);
        $vue = Competency::factory()->create(['name' => 'Vue.js']);

        // Create talent with all required competencies (high completeness)
        $talent1 = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]);
        $talent1->competencies()->attach($php->id, ['proficiency_level' => 5]);
        $talent1->competencies()->attach($laravel->id, ['proficiency_level' => 4]);
        $talent1->competencies()->attach($vue->id, ['proficiency_level' => 4]);

        // Create talent with missing competencies (low completeness)
        $talent2 = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]);
        $talent2->competencies()->attach($php->id, ['proficiency_level' => 5]);
        // Missing Laravel and Vue.js

        $talentRequest = TalentRequest::factory()->create([
            'work_location_city' => 'Jakarta'
        ]);

        // Attach competencies with weights using proper relationship (balanced weights to minimize variance)
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 4, 'weight' => 34],
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 33],
            $vue->id => ['required_proficiency_level' => 3, 'weight' => 33]
        ]);

        $results = $this->service->findAndRankTalents($talentRequest);

        $talent1Result = $results->firstWhere('talent.id', $talent1->id);
        $talent2Result = $results->firstWhere('talent.id', $talent2->id);

        // talent1 should have higher confidence due to completeness
        $this->assertGreaterThan(
            $talent2Result['confidence_score'] ?? 0,
            $talent1Result['confidence_score']
        );

        // Check confidence factors
        $this->assertArrayHasKey('confidence_factors', $talent1Result);
        $this->assertArrayHasKey('completeness', $talent1Result['confidence_factors']);
        $this->assertEquals(1.0, $talent1Result['confidence_factors']['completeness']); // 100% completeness
    }

    /** @test */
    public function it_performs_sensitivity_analysis()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        $talent = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]);
        $talent->competencies()->attach($php->id, ['proficiency_level' => 5]);
        $talent->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

        $talentRequest = TalentRequest::factory()->create([
            'work_location_city' => 'Jakarta'
        ]);

        // Attach competencies with weights using proper relationship
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 4, 'weight' => 50],
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 50]
        ]);

        $results = $this->service->findAndRankTalents($talentRequest);

        $talentResult = $results->first();

        // Should have sensitivity analysis data
        $this->assertArrayHasKey('sensitivity_score', $talentResult);
        $this->assertArrayHasKey('stability_score', $talentResult);
        $this->assertIsFloat($talentResult['sensitivity_score']);
        $this->assertIsFloat($talentResult['stability_score']);
        $this->assertGreaterThanOrEqual(0, $talentResult['stability_score']);
        $this->assertLessThanOrEqual(1, $talentResult['stability_score']);
    }

    /** @test */
    public function it_filters_low_confidence_results()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        // Create a talent that should meet requirements
        $talent = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]);
        $talent->competencies()->attach($php->id, ['proficiency_level' => 5]);
        $talent->competencies()->attach($laravel->id, ['proficiency_level' => 5]);

        $talentRequest = TalentRequest::factory()->create([
            'work_location_city' => 'Jakarta',
            'work_location_country' => 'Indonesia',
            'work_location_type' => 'on-site'
        ]);

        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 2, 'weight' => 50],
            $laravel->id => ['required_proficiency_level' => 2, 'weight' => 50]
        ]);

        $results = $this->service->findAndRankTalents($talentRequest);

        // Test that confidence filtering is applied to all results
        foreach ($results as $result) {
            $this->assertGreaterThanOrEqual(
                EnhancedDecisionSupportService::MIN_CONFIDENCE_SCORE,
                $result['confidence_score'],
                'All results should meet minimum confidence threshold of ' . EnhancedDecisionSupportService::MIN_CONFIDENCE_SCORE
            );
        }

        // Test that we can access the confidence score properly (it exists in results)
        if (count($results) > 0) {
            $this->assertArrayHasKey('confidence_score', $results->first());
        }
    }

    /** @test */
    public function it_handles_location_scoring_correctly()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        // Exact location match
        $talent1 = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]);
        $talent1->competencies()->attach($php->id, ['proficiency_level' => 4]);
        $talent1->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

        // Same region
        $talent2 = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]);
        $talent2->competencies()->attach($php->id, ['proficiency_level' => 4]);
        $talent2->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

        // Remote worker
        $talent3 = $this->createTalent([
            'domicile_city' => 'Bali',
            'domicile_country' => 'Indonesia',
            'can_work_remote' => true
        ]);
        $talent3->competencies()->attach($php->id, ['proficiency_level' => 4]);
        $talent3->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

        // Different location, no remote
        $talent4 = $this->createTalent([
            'domicile_city' => 'Surabaya',
            'domicile_country' => 'Indonesia',
            'can_work_remote' => false
        ]);
        $talent4->competencies()->attach($php->id, ['proficiency_level' => 4]);
        $talent4->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

        $talentRequest = TalentRequest::factory()->create([
            'work_location_city' => 'Jakarta'
        ]);

        // Attach competencies with weights using proper relationship (equal weights to minimize variance)
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 3, 'weight' => 50],
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 50]
        ]);

        $results = $this->service->findAndRankTalents($talentRequest);

        // Should be ordered by location score (exact > same region > remote > different)
        // Extract talent IDs from the result structure
        $orderedTalents = $results->sortByDesc('saw_score')->pluck('talent.id');

        // Log the ordered talent IDs
        Log::info('[Test Debug] Ordered talent IDs:', $orderedTalents->toArray());

        $this->assertEquals($talent1->id, $orderedTalents->first()); // Exact match highest
    }

    /** @test */
    public function it_provides_methodology_explanation()
    {
        $explanation = $this->service->getMethodologyExplanation();

        $this->assertArrayHasKey('method', $explanation);
        $this->assertArrayHasKey('improvements', $explanation);
        $this->assertArrayHasKey('scoring_components', $explanation);
        $this->assertArrayHasKey('validation_rules', $explanation);

        $this->assertEquals('Enhanced Simple Additive Weighting (SAW)', $explanation['method']);
        $this->assertIsArray($explanation['improvements']);
        $this->assertArrayHasKey('competencies', $explanation['scoring_components']);
        $this->assertArrayHasKey('location', $explanation['scoring_components']);

        // Check that old validation rules are no longer mentioned or are updated
        $validationRulesText = json_encode($explanation['validation_rules']);
        $this->assertStringNotContainsString('Single competency weight cannot exceed 60%', $validationRulesText);
        $this->assertStringNotContainsString('Weight distribution is too uneven', $validationRulesText);
        $this->assertStringContainsString('Competency weights must be between 0% and 100%', $validationRulesText);
        // Add more assertions if specific text for new rules is expected
    }

    /** @test */
    public function it_handles_empty_competencies_gracefully()
    {
        $talentRequest = TalentRequest::factory()->create();
        // No competencies attached - this tests empty competencies

        $results = $this->service->findAndRankTalents($talentRequest);

        $this->assertCount(0, $results);
    }

    /** @test */
    public function it_validates_proficiency_levels()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        $talentRequest = TalentRequest::factory()->create();

        // Attach competencies with weights using proper relationship (equal weights to minimize variance)
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 6, 'weight' => 50], // Invalid level (max is 5)
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 50]
        ]);

        $results = $this->service->findAndRankTalents($talentRequest);

        // Should handle invalid data gracefully
        $this->assertCount(0, $results);
    }

    /** @test */
    public function it_maintains_saw_methodology_constraints()
    {
        $php = Competency::factory()->create(['name' => 'PHP']);
        $laravel = Competency::factory()->create(['name' => 'Laravel']);

        $talent = $this->createTalent([
            'domicile_city' => 'Jakarta',
            'domicile_country' => 'Indonesia'
        ]);
        $talent->competencies()->attach($php->id, ['proficiency_level' => 5]);
        $talent->competencies()->attach($laravel->id, ['proficiency_level' => 4]);

        $talentRequest = TalentRequest::factory()->create([
            'work_location_city' => 'Jakarta'
        ]);

        // Attach competencies with weights using proper relationship (equal weights to minimize variance)
        $talentRequest->competencies()->attach([
            $php->id => ['required_proficiency_level' => 4, 'weight' => 50],
            $laravel->id => ['required_proficiency_level' => 3, 'weight' => 50]
        ]);

        $results = $this->service->findAndRankTalents($talentRequest);
        $result = $results->first();

        // SAW score should be between 0 and 1
        $this->assertGreaterThanOrEqual(0, $result['saw_score']);
        $this->assertLessThanOrEqual(1, $result['saw_score']);

        // Should have proper competency scoring components
        $this->assertArrayHasKey('competency_scores', $result);
        $this->assertArrayHasKey('location_score', $result);

        // Competency scores should be normalized (0-1)
        foreach ($result['competency_scores'] as $score) {
            $this->assertGreaterThanOrEqual(0, $score);
            $this->assertLessThanOrEqual(1, $score);
        }

        // Location score should be normalized (0-1)
        $this->assertGreaterThanOrEqual(0, $result['location_score']);
        $this->assertLessThanOrEqual(1, $result['location_score']);
    }
}
