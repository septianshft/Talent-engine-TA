<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\TalentRequest;
use App\Models\Competency;

class AdminAssignsMultipleTalentsTest extends TestCase
{
    use RefreshDatabase;

    private $admin, $requester, $talent1, $talent2;
    private $talentRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        // Seed competencies if your TalentRequestFactory depends on them
        // Or if you need to attach them for the request to be valid
        // We should also seed some competencies as TalentRequestFactory might attach them
        // and the DecisionSupportService in Admin\TalentRequestController->show might need them.
        $this->artisan('db:seed', ['--class' => 'CompetencySeeder']);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(Role::where('name', 'admin')->first());

        $this->requester = User::factory()->create();
        $this->requester->roles()->attach(Role::where('name', 'user')->first());

        $this->talent1 = User::factory()->create();
        $this->talent1->roles()->attach(Role::where('name', 'talent')->first());

        $this->talent2 = User::factory()->create();
        $this->talent2->roles()->attach(Role::where('name', 'talent')->first());

        $this->talentRequest = TalentRequest::factory()->create([
            'user_id' => $this->requester->id,
            'status' => 'pending_admin',
            'details' => 'Test request for multiple talents'
        ]);
    }

    /** @test */
    public function admin_can_assign_multiple_talents_to_a_request()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.talent-requests.assign', $this->talentRequest->id), [
            'talent_ids' => [$this->talent1->id, $this->talent2->id],
        ]);

        $response->assertRedirect(route('admin.talent-requests.index'));
        $response->assertSessionHas('success', 'Talents assigned successfully. Requests sent to talents for review.');

        $this->talentRequest->refresh();

        $this->assertEquals('pending_talent', $this->talentRequest->status);
        $this->assertCount(2, $this->talentRequest->assignedTalents);

        // Check pivot table entries and their status
        $this->assertDatabaseHas('talent_request_assignments', [
            'talent_request_id' => $this->talentRequest->id,
            'talent_id' => $this->talent1->id,
            'status' => 'pending_assignment_response' // Default status from migration
        ]);
        $this->assertDatabaseHas('talent_request_assignments', [
            'talent_request_id' => $this->talentRequest->id,
            'talent_id' => $this->talent2->id,
            'status' => 'pending_assignment_response' // Default status from migration
        ]);
    }
}
