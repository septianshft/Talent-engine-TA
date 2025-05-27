<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\TalentRequest;
use App\Models\Competency;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class TalentRequestManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $requestingUser;
    protected User $talentUser;
    protected Competency $competency1;
    protected Competency $competency2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']); // Ensure roles exist

        // Fetch roles
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $userRole = Role::where('name', 'user')->firstOrFail();
        $talentRole = Role::where('name', 'talent')->firstOrFail();

        // Create users manually
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->adminUser->roles()->attach($adminRole);

        $this->requestingUser = User::create([
            'name' => 'Requesting User',
            'email' => 'requester@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->requestingUser->roles()->attach($userRole);

        $this->talentUser = User::create([
            'name' => 'Talent User',
            'email' => 'talent@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->talentUser->roles()->attach($talentRole);

        // Create competencies (still using factory as it's not the source of the error)
        $this->competency1 = Competency::factory()->create(['name' => 'PHP']);
        $this->competency2 = Competency::factory()->create(['name' => 'Laravel']);

        // Assign competencies to talent
        $this->talentUser->competencies()->attach([
            $this->competency1->id => ['proficiency_level' => 3], // Advanced
            $this->competency2->id => ['proficiency_level' => 4], // Expert
        ]);

        // Acting as the admin user for requests - Moved here after user creation
        $this->actingAs($this->adminUser);
    }

    /** @test */
    public function admin_can_view_talent_requests_index(): void
    {
        // Arrange: Create a talent request
        TalentRequest::create([
            'user_id' => $this->requestingUser->id,
            'details' => 'Test request details',
            'status' => 'pending_admin'
        ]);

        // Act: Access the admin index page
        $response = $this->actingAs($this->adminUser)->get(route('admin.talent-requests.index'));

        // Assert: Check for successful response and view
        $response->assertStatus(200);
        $response->assertViewIs('admin.talent-requests.index');
        $response->assertViewHas('requests'); // Check if the view receives the requests variable
    }

    /** @test */
    public function admin_can_view_single_talent_request_with_dss_ranking(): void
    {
        // Arrange: Create a talent request requiring competencies the talent has
        $talentRequest = TalentRequest::create([
            'user_id' => $this->requestingUser->id,
            'details' => 'Test request details for DSS',
            'status' => 'pending_admin'
        ]);
        $talentRequest->competencies()->attach([
            $this->competency1->id => [
                'required_proficiency_level' => 2,
                'weight' => 4,
                'is_critical' => false
            ], // Intermediate PHP
            $this->competency2->id => [
                'required_proficiency_level' => 3,
                'weight' => 3,
                'is_critical' => false
            ], // Advanced Laravel
        ]);

        // Act: Access the admin show page
        $response = $this->actingAs($this->adminUser)->get(route('admin.talent-requests.show', $talentRequest));

        // Assert: Check for successful response, view, and ranked talents data
        $response->assertStatus(200);
        $response->assertViewIs('admin.talent-requests.show');
        $response->assertViewHas('talentRequest', $talentRequest);
        $response->assertViewHas('rankedTalents'); // Check if the DSS results are passed

        // Optionally, assert that the specific talent is in the ranked list
        $rankedTalentsData = $response->viewData('rankedTalents');
        $foundTalent = false;
        foreach ($rankedTalentsData as $rankedTalent) {
            if ($rankedTalent['talent']->id === $this->talentUser->id) {
                $foundTalent = true;
                break;
            }
        }
        $this->assertTrue($foundTalent, "The talentUser was not found in the rankedTalents list.");
    }

    /** @test */
    public function admin_can_reject_pending_admin_request(): void
    {
        // Arrange: Create a talent request with 'pending_admin' status
        $talentRequest = TalentRequest::create([
            'user_id' => $this->requestingUser->id,
            'details' => 'Test request details for DSS',
            'status' => 'pending_admin'
        ]);

        // Act: Send a PATCH request to reject
        $response = $this->actingAs($this->adminUser)->patch(route('admin.talent-requests.update', $talentRequest), [
            'action' => 'reject',
        ]);

        // Assert: Check for redirect and updated status in the database
        $response->assertRedirect(route('admin.talent-requests.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('talent_requests', [
            'id' => $talentRequest->id,
            'status' => 'rejected_admin',
        ]);
    }

    /** @test */
    public function admin_can_assign_talent_to_pending_admin_request(): void
    {
        // Arrange: Create a talent request with 'pending_admin' status
        $talentRequest = TalentRequest::create([
            'user_id' => $this->requestingUser->id,
            'details' => 'Test request details for DSS',
            'status' => 'pending_admin'
        ]);

        // Act: Send a POST request to assign the talent
        $response = $this->actingAs($this->adminUser)->post(route('admin.talent-requests.assign', $talentRequest), [
            'talent_ids' => [$this->talentUser->id], // Send as an array
        ]);

        // Assert: Check for redirect and updated status/talent_id in the database
        $response->assertRedirect(route('admin.talent-requests.index'));
        $response->assertSessionHas('success');

        // Check that the talent is assigned in the pivot table
        $this->assertDatabaseHas('talent_request_assignments', [
            'talent_request_id' => $talentRequest->id,
            'user_id' => $this->talentUser->id,
            'status' => 'pending_assignment_response' // Or whatever the default status is upon assignment
        ]);

        // Check that the main talent request status is updated
        $this->assertDatabaseHas('talent_requests', [
            'id' => $talentRequest->id,
            'status' => 'pending_talent',
        ]);
    }

    /** @test */
    public function admin_can_mark_approved_request_as_completed(): void
    {
        // Arrange: Create a talent request with 'approved' status
        $talentRequest = TalentRequest::create([
            'user_id' => $this->requestingUser->id,
            'talent_id' => $this->talentUser->id,
            'details' => 'Test request details for completion',
            'status' => 'approved'
        ]);

        // Act: Send a PATCH request to mark as completed
        $response = $this->actingAs($this->adminUser)->patch(route('admin.talent-requests.complete', $talentRequest));

        // Assert: Check for redirect and updated status in the database
        $response->assertRedirect(route('admin.talent-requests.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('talent_requests', [
            'id' => $talentRequest->id,
            'status' => 'completed',
        ]);
    }
}
