<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Competency;
use App\Models\TalentRequest;
use App\Services\DecisionSupportService;

class DecisionSupportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DecisionSupportService $dss;
    protected User $requester;
    protected Competency $phpCompetency;
    protected Competency $jsCompetency;
    protected Competency $cssCompetency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dss = new DecisionSupportService();

        // Seed roles
        if (Role::count() === 0) {
            $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        }

        // UserFactory assigns 'user' role by default.
        $this->requester = User::factory()->create();
        // Ensure it has the user role for clarity, though factory should handle it.
        $userRole = Role::firstOrCreate(['name' => 'user']);
        if (!$this->requester->roles->contains($userRole)) {
            $this->requester->roles()->attach($userRole);
        }


        $this->phpCompetency = Competency::factory()->create(['name' => 'PHP']);
        $this->jsCompetency = Competency::factory()->create(['name' => 'JavaScript']);
        $this->cssCompetency = Competency::factory()->create(['name' => 'CSS']);
    }

    private function createTalentWithProficiencies(string $name, array $proficiencies): User
    {
        $talent = User::factory()->create(['name' => $name]);
        // Detach any default roles assigned by the factory (like 'user')
        // to ensure this talent is *only* a 'talent' for this test context.
        $talent->roles()->detach();

        $talentRole = Role::firstOrCreate(['name' => 'talent']);
        $talent->roles()->attach($talentRole);

        foreach ($proficiencies as $competencyId => $level) {
            $talent->competencies()->attach($competencyId, ['proficiency_level' => $level]);
        }
        return $talent;
    }

    public function test_dss_ranks_talents_correctly_with_weights(): void
    {
        // Create Talents
        $talentAlice = $this->createTalentWithProficiencies('Alice', [
            $this->phpCompetency->id => 4, // PHP: Expert
            $this->jsCompetency->id  => 3, // JS: Advanced
            $this->cssCompetency->id => 2, // CSS: Intermediate
        ]);

        $talentBob = $this->createTalentWithProficiencies('Bob', [
            $this->phpCompetency->id => 3, // PHP: Advanced
            $this->jsCompetency->id  => 4, // JS: Expert
            $this->cssCompetency->id => 3, // CSS: Advanced
        ]);

        // TalentCharlie does not meet JS requirement, should be filtered out
        $talentCharlie = $this->createTalentWithProficiencies('Charlie', [
            $this->phpCompetency->id => 4,
            $this->jsCompetency->id  => 1, // Below required level for JS
            $this->cssCompetency->id => 4,
        ]);

        // Create Talent Request: PHP is most important, then JS, then CSS
        $talentRequest = TalentRequest::factory()->create(['user_id' => $this->requester->id]);
        $talentRequest->competencies()->attach([
            $this->phpCompetency->id => ['required_proficiency_level' => 3, 'weight' => 5], // PHP: Level 3, Weight 5
            $this->jsCompetency->id  => ['required_proficiency_level' => 2, 'weight' => 3], // JS: Level 2, Weight 3
            $this->cssCompetency->id => ['required_proficiency_level' => 1, 'weight' => 1], // CSS: Level 1, Weight 1
        ]);

        $rankedTalents = $this->dss->findAndRankTalents($talentRequest);

        // Expected Scores:
        // Alice: (PHP 4*5) + (JS 3*3) + (CSS 2*1) = 20 + 9 + 2 = 31
        // Bob:   (PHP 3*5) + (JS 4*3) + (CSS 3*1) = 15 + 12 + 3 = 30
        // Charlie: Should be filtered out

        $this->assertCount(2, $rankedTalents, "Charlie should have been filtered out.");

        $this->assertEquals($talentAlice->id, $rankedTalents->first()->id, "Alice should be ranked first.");
        $this->assertEquals(31, $rankedTalents->first()->dss_score);

        $this->assertEquals($talentBob->id, $rankedTalents->last()->id, "Bob should be ranked second.");
        $this->assertEquals(30, $rankedTalents->last()->dss_score);
    }

    public function test_dss_returns_empty_if_no_competencies_required(): void
    {
        $talentRequest = TalentRequest::factory()->create(['user_id' => $this->requester->id]);
        // No competencies attached to the request

        $rankedTalents = $this->dss->findAndRankTalents($talentRequest);
        $this->assertCount(0, $rankedTalents);
    }

    public function test_dss_returns_empty_if_no_talent_meets_requirements(): void
    {
        // Talent with insufficient skills
        $this->createTalentWithProficiencies('Dave', [
            $this->phpCompetency->id => 1, // Below required PHP level
            $this->jsCompetency->id  => 1, // Below required JS level
        ]);

        $talentRequest = TalentRequest::factory()->create(['user_id' => $this->requester->id]);
        $talentRequest->competencies()->attach([
            $this->phpCompetency->id => ['required_proficiency_level' => 3, 'weight' => 5],
            $this->jsCompetency->id  => ['required_proficiency_level' => 2, 'weight' => 3],
        ]);

        $rankedTalents = $this->dss->findAndRankTalents($talentRequest);
        $this->assertCount(0, $rankedTalents);
    }
}
