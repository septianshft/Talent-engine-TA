<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Competency;
use App\Models\TalentRequest;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Seed roles if not already done by a global seeder
    if (Role::count() === 0) {
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    // Create test user
    $this->user = User::factory()->create();

    // Ensure the user has the 'user' role
    $userRole = Role::where('name', 'user')->firstOrFail();
    if (!$this->user->roles->contains($userRole)) {
        $this->user->roles()->attach($userRole);
    }

    // Create test competencies
    $this->competency1 = Competency::factory()->create(['name' => 'PHP Development']);
    $this->competency2 = Competency::factory()->create(['name' => 'Tailwind CSS']);
});

test('can create talent request with competency weights', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Need a developer for a new project.',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 5], // PHP Development, Advanced, Weight 5
            ['id' => $this->competency2->id, 'level' => 2, 'weight' => 3], // Tailwind CSS, Intermediate, Weight 3
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertRedirect(route('user.requests.index'));
    $response->assertSessionHas('success');

    expect(TalentRequest::count())->toBe(1);
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

    expect($talentRequest->competencies)->toHaveCount(2);
});

test('fails validation with missing competency level', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'weight' => 5], // Missing level
            ['id' => $this->competency2->id, 'level' => 2, 'weight' => 3],
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('competencies.0.level');
});

test('fails validation with missing competency weight', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 5],
            ['id' => $this->competency2->id, 'level' => 2], // Missing weight
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('competencies.1.weight');
});

test('fails validation with no competencies selected', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'remote',
        'competencies' => [],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('competencies');
});

test('fails validation with duplicate competencies', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 5],
            ['id' => $this->competency1->id, 'level' => 2, 'weight' => 3], // Duplicate competency
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('competencies');
});

test('fails validation with invalid competency level', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 5, 'weight' => 3], // Level should be 1-4
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('competencies.0.level');
});

test('fails validation with invalid competency weight', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 101], // Weight should be 0-100
        ],
    ];

    $response = $this->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('competencies.0.weight');
});

test('fails validation with missing location country for on_site work', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'on_site',
        'work_location_city' => 'Jakarta',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 5],
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('work_location_country');
});

test('fails validation with missing location city for hybrid work', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'hybrid',
        'work_location_country' => 'Indonesia',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 5],
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('work_location_city');
});

test('can create direct talent request', function () {
    $this->actingAs($this->user);

    // Create a talent user
    $talent = User::factory()->create();
    $talentRole = Role::firstOrCreate(['name' => 'talent']);
    $talent->roles()->attach($talentRole);

    // Ensure the role is properly saved and loaded
    $talent->load('roles');

    // Debug: verify role assignment worked
    expect($talent->hasRole('talent'))->toBeTrue();

    $requestData = [
        'details' => 'Direct request for specific talent.',
        'work_location_type' => 'remote',
        'talent_id' => $talent->id,
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 5],
        ],
    ];

    $response = $this->post(route('user.requests.store'), $requestData);

    $response->assertRedirect(route('user.requests.index'));
    $response->assertSessionHas('success');

    expect(TalentRequest::count())->toBe(1);
    $talentRequest = TalentRequest::first();

    // Verify status is pending_talent for direct requests
    expect($talentRequest->status)->toBe('pending_talent');

    // Verify talent is assigned
    expect($talentRequest->assignedTalents->contains($talent))->toBeTrue();
});
