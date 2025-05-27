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
    $this->competency3 = Competency::factory()->create(['name' => 'Vue.js']); // Added competency3
});

test('can create talent request with competency weights', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Need a developer for a new project with specific weights.',
        'work_location_type' => 'remote',
        'competencies' => [
            // Corrected: Ensure requestData matches assertions
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 75], // PHP Development, Advanced, Weight 75
            ['id' => $this->competency2->id, 'level' => 2, 'weight' => 25], // Tailwind CSS, Intermediate, Weight 25
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
        'details' => 'Need a developer for a new project with specific weights.',
        'status' => 'pending_admin',
    ]);

    // Check pivot table data
    $this->assertDatabaseHas('competency_talent_request', [
        'talent_request_id' => $talentRequest->id,
        'competency_id' => $this->competency1->id,
        'required_proficiency_level' => 3,
        'weight' => 75, // Corrected assertion
    ]);

    $this->assertDatabaseHas('competency_talent_request', [
        'talent_request_id' => $talentRequest->id,
        'competency_id' => $this->competency2->id,
        'required_proficiency_level' => 2,
        'weight' => 25, // Corrected assertion
    ]);

    expect($talentRequest->competencies)->toHaveCount(2);
});

test('can create talent request with 0 percent competency weight', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Project where one competency has zero weight.',
        'work_location_type' => 'hybrid',
        'work_location_city' => 'TestCity', // Required for hybrid
        'work_location_country' => 'TestCountry', // Required for hybrid
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 4, 'weight' => 0],
            ['id' => $this->competency2->id, 'level' => 3, 'weight' => 50],
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertRedirect(route('user.requests.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('competency_talent_request', [
        'competency_id' => $this->competency1->id,
        'weight' => 0,
    ]);
    $this->assertDatabaseHas('competency_talent_request', [
        'competency_id' => $this->competency2->id,
        'weight' => 50,
    ]);
});

test('can create talent request with 100 percent competency weight', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Project where one competency has 100% weight.',
        'work_location_type' => 'on_site',
        'work_location_city' => 'TestCity', // Required for on_site
        'work_location_country' => 'TestCountry', // Required for on_site
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 4, 'weight' => 100], // Changed level from 5 to 4
            ['id' => $this->competency2->id, 'level' => 2, 'weight' => 0], // Other competency must have 0 or not be present if sum is implicitly 100
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertRedirect(route('user.requests.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('competency_talent_request', [
        'competency_id' => $this->competency1->id,
        'weight' => 100,
    ]);
    $this->assertDatabaseHas('competency_talent_request', [
        'competency_id' => $this->competency2->id,
        'weight' => 0,
    ]);
});

test('can create talent request with mixed valid competency weights', function () {
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Project with mixed competency weights.',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 4, 'weight' => 60],
            ['id' => $this->competency2->id, 'level' => 3, 'weight' => 40],
            ['id' => $this->competency3->id, 'level' => 2, 'weight' => 0],
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertRedirect(route('user.requests.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('competency_talent_request', ['competency_id' => $this->competency1->id, 'weight' => 60]);
    $this->assertDatabaseHas('competency_talent_request', ['competency_id' => $this->competency2->id, 'weight' => 40]);
    $this->assertDatabaseHas('competency_talent_request', ['competency_id' => $this->competency3->id, 'weight' => 0]);
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
            ['id' => $this->competency1->id, 'level' => 2], // Missing weight for competency1
            ['id' => $this->competency2->id, 'level' => 3, 'weight' => 50], // competency2 is fine
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    // Corrected: Error should be on the first competency in the array if it's the one with the issue.
    $response->assertSessionHasErrors('competencies.0.weight');
    $response->assertSessionDoesntHaveErrors('competencies.1.weight');
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
            ['id' => $this->competency1->id, 'level' => 2, 'weight' => 30],
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 40], // Duplicate competency1
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors('competencies'); // General error for duplicate competencies
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

test('fails validation with competency weight greater than 100', function () { // Renamed and updated
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => 101], // Weight > 100
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors(['competencies.0.weight' => 'Weight must not exceed 100%.']);
});

test('fails validation with competency weight less than 0', function () { // Added
    $this->actingAs($this->user);

    $requestData = [
        'details' => 'Test details',
        'work_location_type' => 'remote',
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 3, 'weight' => -1], // Weight < 0
        ],
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData);

    $response->assertSessionHasErrors(['competencies.0.weight' => 'Weight must be at least 0%.']);
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
        'details' => 'Test details for hybrid work missing city', // Corrected details
        'work_location_type' => 'hybrid',
        'work_location_country' => 'TestCountry', // Country is provided
        // work_location_city is missing
        'competencies' => [
            ['id' => $this->competency1->id, 'level' => 2, 'weight' => 50]
        ]
    ];

    $response = $this->withoutMiddleware()
                     ->post(route('user.requests.store'), $requestData); // Added post

    $response->assertSessionHasErrors('work_location_city');
});

test('can create direct talent request', function () {
    $this->actingAs($this->user);

    // Create a talent user
    $talent = User::factory()->create();
    $talentRole = Role::firstOrCreate(['name' => 'talent']);
    $talent->roles()->attach($talentRole);

    // Ensure the role is properly saved and can be queried
    $talent->refresh();

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
