<?php

use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('applicant can save application as draft', function () {
    $user = User::factory()->create();
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Applicant']);
    $user->assignRole('Applicant');

    $response = $this->actingAs($user)->postJson('/application/draft', [
        'personal_info' => ['first_name' => 'John', 'last_name' => 'Doe'],
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('applications', [
        'user_id' => $user->id,
        'status' => 'draft',
    ]);
});

test('applicant can submit full application and trigger scoring and email', function () {
    Mail::fake();

    $user = User::factory()->create();
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Applicant']);
    $user->assignRole('Applicant');

    $payload = [
        'personal_info' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'Female',
            'university' => 'Makerere University',
            'program_of_study' => 'BSc Education Biology',
            'cgpa' => 4.0,
            'gpa' => 4.0,
            'high_school' => 'St Mary\'s College',
        ],
        'financial_info' => [
            'household_income' => 15000,
            'number_of_dependents' => 0,
            'estimated_tuition' => 5000000,
            'estimated_living_expenses' => 3000000,
            'income_sources' => 'Parent support and part-time tutoring',
            'funding_gap' => 2000000,
        ],
        'guardian_info' => [
            'guardian_name' => 'Jane Doe',
            'guardian_phone' => '0700000000',
            'guardian_relation' => 'Mother',
        ],
        'essay' => [
            'personal_statement' => str_repeat('word ', 120),
            'commitment' => str_repeat('word ', 120)
        ],
    ];

    $response = $this->actingAs($user)->post('/application/submit', $payload);

    $response->assertRedirect('/portal');
    $this->assertDatabaseHas('applications', [
        'user_id' => $user->id,
        'status' => 'submitted',
    ]);

    $application = Application::where('user_id', $user->id)->first();
    expect($application->scoring_breakdown)->not->toBeNull();

    Mail::assertSent(ApplicationReceived::class);
});
