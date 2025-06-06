<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\TalentRequest;

class AdminAssignmentDebugTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function debug_admin_assignment_issue()
    {
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'CompetencySeeder']);

        $admin = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole);
        $admin->load('roles');

        $talent1 = User::factory()->create();
        $talent1->roles()->attach(Role::where('name', 'talent')->first());

        $talent2 = User::factory()->create();
        $talent2->roles()->attach(Role::where('name', 'talent')->first());

        $requester = User::factory()->create();
        $requester->roles()->attach(Role::where('name', 'user')->first());

        $talentRequest = TalentRequest::factory()->create([
            'user_id' => $requester->id,
            'status' => 'pending_admin',
            'details' => 'Debug test request'
        ]);

        // Debug: Check if admin has the role
        dump('Admin ID: ' . $admin->id);
        dump('Admin roles: ' . $admin->roles->pluck('name')->join(', '));
        dump('Admin hasRole(admin): ' . ($admin->hasRole('admin') ? 'YES' : 'NO'));

        // Test authentication
        $this->actingAs($admin);
        dump('Authenticated user: ' . auth()->id());
        dump('Authenticated user roles: ' . auth()->user()->roles()->pluck('name')->join(', '));

        // Test the assignment route
        $response = $this->post(route('admin.talent-requests.assign', $talentRequest->id), [
            'talent_ids' => [$talent1->id, $talent2->id],
        ]);

        dump('Response status: ' . $response->getStatusCode());
        dump('Response location header: ' . $response->headers->get('location'));
        dump('Session errors: ' . json_encode(session()->get('errors')));
        dump('Session success: ' . session()->get('success'));

        // Let's see what happens
        $response->assertRedirect();
    }
}
