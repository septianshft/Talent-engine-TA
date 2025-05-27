<?php

use App\Models\User;
use App\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();

    // Create or get the user role and assign it
    $userRole = Role::firstOrCreate(['name' => 'user']);
    $user->roles()->attach($userRole);

    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});
