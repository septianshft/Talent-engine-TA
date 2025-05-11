<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Competency;
use App\Models\TalentRequest;

class UserTalentRequestTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Competency $competency1;
    protected Competency $competency2;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles if not already done by a global seeder
        if (Role::count() === 0) {
            $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        }

        // The UserFactory's configure() method already attaches the 'user' role by default.
        // So, we don't need to explicitly fetch and attach it here.
        $this->user = User::factory()->create();

        // Ensure the user actually has the 'user' role for sanity, in case factory logic changes.
        // This also ensures $userRole is available if needed, though not strictly for attachment.
        $userRole = Role::where('name', 'user')->firstOrFail();
        if (!$this->user->roles->contains($userRole)) {
            $this->user->roles()->attach($userRole); // Attach only if not already present
        }


        $this->competency1 = Competency::factory()->create(['name' => 'PHP Development']);
        $this->competency2 = Competency::factory()->create(['name' => 'Tailwind CSS']);
    }

    /**
     * Test that a user can create a talent request with competencies, levels, and weights.
     *
     * @return void
     */
    public function test_user_can_create_talent_request_with_competency_weights(): void
    {
        $this->actingAs($this->user);

        $requestData = [
            'details' => 'Need a developer for a new project.',
            'competencies' => [
                ['id' => $this->competency1->id, 'level' => 3, 'weight' => 5], // PHP Development, Advanced, Weight 5
                ['id' => $this->competency2->id, 'level' => 2, 'weight' => 3], // Tailwind CSS, Intermediate, Weight 3
            ],
        ];

        $response = $this->post(route('user.requests.store'), $requestData);

        $response->assertRedirect(route('user.requests.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(1, TalentRequest::count());
        $talentRequest = TalentRequest::first();

        $this->assertDatabaseHas('talent_requests', [
            'id' => $talentRequest->id,
            'user_id' => $this->user->id,
            'details' => 'Need a developer for a new project.',
            'status' => 'pending_admin',
        ]);

        // Check pivot table data
        $this->assertDatabaseHas('competency_talent_request', [
            'talent_request_id' => $talentRequest->id,
            'competency_id' => $this->competency1->id,
            'required_proficiency_level' => 3,
            'weight' => 5,
        ]);

        $this->assertDatabaseHas('competency_talent_request', [
            'talent_request_id' => $talentRequest->id,
            'competency_id' => $this->competency2->id,
            'required_proficiency_level' => 2,
            'weight' => 3,
        ]);

        $this->assertCount(2, $talentRequest->competencies);
    }

    /**
     * Test validation when creating a talent request with missing competency data.
     *
     * @return void
     */
    public function test_talent_request_creation_fails_with_missing_competency_data(): void
    {
        $this->actingAs($this->user);

        // Missing level for competency1
        $requestDataMissingLevel = [
            'details' => 'Test details',
            'competencies' => [
                ['id' => $this->competency1->id, 'weight' => 5],
                ['id' => $this->competency2->id, 'level' => 2, 'weight' => 3],
            ],
        ];
        $response = $this->post(route('user.requests.store'), $requestDataMissingLevel);
        $response->assertSessionHasErrors('competencies.0.level');

        // Missing weight for competency2
        $requestDataMissingWeight = [
            'details' => 'Test details',
            'competencies' => [
                ['id' => $this->competency1->id, 'level' => 3, 'weight' => 5],
                ['id' => $this->competency2->id, 'level' => 2],
            ],
        ];
        $response = $this->post(route('user.requests.store'), $requestDataMissingWeight);
        $response->assertSessionHasErrors('competencies.1.weight');

        // No competencies selected
        $requestDataNoCompetencies = [
            'details' => 'Test details',
            'competencies' => [],
        ];
        $response = $this->post(route('user.requests.store'), $requestDataNoCompetencies);
        $response->assertSessionHasErrors('competencies');
    }
}
