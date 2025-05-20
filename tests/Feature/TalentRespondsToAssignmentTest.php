<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\TalentRequest;
use App\Models\Competency;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Import DB facade

class TalentRespondsToAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private User $talent;
    private TalentRequest $talentRequest;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        Role::factory()->create(['name' => 'talent']);
        Role::factory()->create(['name' => 'user']); // Requester role
        Role::factory()->create(['name' => 'admin']);

        // Create a talent user
        $this->talent = User::factory()->create();
        $this->talent->roles()->attach(Role::where('name', 'talent')->first());

        // Create a requester user
        $requester = User::factory()->create();
        $requester->roles()->attach(Role::where('name', 'user')->first());

        // Create competencies that will be manually attached
        $competency1 = Competency::factory()->create(['name' => 'PHP']);
        $competency2 = Competency::factory()->create(['name' => 'Laravel']);

        $this->talentRequest = TalentRequest::factory()
            // ->for($requester, 'requestingUser') // Temporarily replaced with direct user_id assignment
            ->skipDefaultCompetencies()
            ->create([
                'user_id' => $requester->id, // Explicitly set user_id
                'status' => 'pending_talent_response',
            ]);

        // Manually attach competencies to the talent request
        Log::info('SETUP: Attaching competencies to TalentRequest ID: ' . $this->talentRequest->id);
        $this->talentRequest->competencies()->attach([
            $competency1->id => ['required_proficiency_level' => 3, 'weight' => 5],
            $competency2->id => ['required_proficiency_level' => 4, 'weight' => 3],
        ]);
        Log::info('SETUP: Competencies attached count: ' . $this->talentRequest->competencies()->count());

        // Assign the talent to this request
        Log::info('SETUP: Attaching talent ID: ' . $this->talent->id . ' (Role: ' . ($this->talent->roles->first()->name ?? 'N/A') . ') to TalentRequest ID: ' . $this->talentRequest->id . ' (Requester ID: ' . $this->talentRequest->user_id . ')');
        $this->talentRequest->assignedTalents()->attach($this->talent->id, [
            'status' => 'pending_assignment_response',
            // created_at and updated_at are handled by withTimestamps() on the relationship
        ]);

        // Refresh the model to ensure relationships are loaded correctly from DB
        $this->talentRequest->refresh();

        // Verify attachment directly in the database
        $assignmentCheck = DB::table('talent_request_assignments')
            ->where('talent_request_id', $this->talentRequest->id)
            ->where('user_id', $this->talent->id)
            ->first();

        if ($assignmentCheck) {
            Log::info('SETUP: DB check PASS. Found assignment in talent_request_assignments. Status: ' . $assignmentCheck->status);
        } else {
            Log::error('SETUP: DB check FAIL. Did NOT find assignment in talent_request_assignments for TR_ID=' . $this->talentRequest->id . ', User_ID=' . $this->talent->id);
        }

        // Verify attachment via Eloquent relationship
        $assignedTalentModel = $this->talentRequest->assignedTalents()->find($this->talent->id);

        if ($assignedTalentModel && $assignedTalentModel->pivot) {
            Log::info('SETUP: Eloquent check PASS. Talent ID ' . $assignedTalentModel->id . ' found in assignedTalents with pivot. Pivot status: ' . $assignedTalentModel->pivot->status);
        } else {
            Log::error('SETUP: Eloquent check FAIL. Talent NOT found via assignedTalents()->find() or pivot is missing for Talent ID: ' . $this->talent->id . '. Assigned talents count: ' . $this->talentRequest->assignedTalents()->count());
            if ($assignedTalentModel) {
                Log::error('SETUP: Eloquent check - Talent model found, but its pivot attribute is missing/null.');
            } else {
                 Log::error('SETUP: Eloquent check - Talent model NOT found in assignedTalents collection using find().');
            }
            $allAssignmentsForRequest = DB::table('talent_request_assignments')->where('talent_request_id', $this->talentRequest->id)->get();
            Log::info('SETUP: Current talent_request_assignments for TR_ID ' . $this->talentRequest->id . ': ' . json_encode($allAssignmentsForRequest));
        }
    }

    /** @test */
    public function talent_can_accept_an_assignment()
    {
        $this->actingAs($this->talent);

        $assignedTalent = $this->talentRequest->assignedTalents()->find($this->talent->id);

        $this->assertNotNull($assignedTalent, "Talent should be assigned to the request.");
        $this->assertNotNull($assignedTalent->pivot, "Pivot data should exist for the assignment.");

        $response = $this->patchJson(route('talent.requests.respond', [
            'talent_request_id' => $this->talentRequest->id,
            'talent_id' => $this->talent->id
        ]), [
            'status' => 'accepted'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('talent_request_assignments', [
            'talent_request_id' => $this->talentRequest->id,
            'user_id' => $this->talent->id,
            'status' => 'accepted'
        ]);
    }

    /** @test */
    public function talent_can_reject_an_assignment()
    {
        $this->actingAs($this->talent);

        $assignedTalent = $this->talentRequest->assignedTalents()->find($this->talent->id);

        $this->assertNotNull($assignedTalent, "Talent should be assigned to the request for rejection.");
        $this->assertNotNull($assignedTalent->pivot, "Pivot data should exist for the assignment for rejection.");

        $response = $this->patchJson(route('talent.requests.respond', [
            'talent_request_id' => $this->talentRequest->id,
            'talent_id' => $this->talent->id
        ]), [
            'status' => 'rejected'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('talent_request_assignments', [
            'talent_request_id' => $this->talentRequest->id,
            'user_id' => $this->talent->id,
            'status' => 'rejected'
        ]);
    }
}
