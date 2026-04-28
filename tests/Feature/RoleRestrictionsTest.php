<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('committee can access admin panel', function () {
    Role::firstOrCreate(['name' => 'Committee']);
    $admin = User::factory()->create();
    $admin->assignRole('Committee');

    $response = $this->actingAs($admin)->get('/admin');
    
    $response->assertStatus(200);
});

test('applicant cannot access admin panel', function () {
    Role::firstOrCreate(['name' => 'Applicant']);
    $applicant = User::factory()->create();
    $applicant->assignRole('Applicant');

    $response = $this->actingAs($applicant)->get('/admin');
    
    $response->assertStatus(403);
});

test('applicant can access portal', function () {
    Role::firstOrCreate(['name' => 'Applicant']);
    $applicant = User::factory()->create();
    $applicant->assignRole('Applicant');

    $response = $this->actingAs($applicant)->get('/portal');
    
    $response->assertStatus(200);
});
