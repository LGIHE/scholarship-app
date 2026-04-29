<?php

use App\Models\Application;
use App\Models\User;
use App\Services\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('scoring service calculates breakdown and total correctly', function () {
    $user = User::factory()->create();
    $application = Application::factory()->create([
        'user_id' => $user->id,
        'financial_info' => [
            'household_income' => 15000, // 20 pts
            'number_of_dependents' => 5 // 10 pts
        ],
        'personal_info' => [
            'gpa' => 4.0, // 25 pts
            'gender' => 'Female', // 5 pts
            'is_rural' => true // 5 pts + 5 base = 15 pts
        ],
        'essay' => [
            'commitment' => str_repeat('word ', 105), // 15 pts
            'personal_statement' => str_repeat('word ', 205) // 15 pts
        ]
    ]);

    $scoringService = new ScoringService();
    $scoringService->score($application);

    expect($application->fresh()->scoring_breakdown)->toBeArray()
        ->and($application->fresh()->scoring_breakdown['financial_need'])->toEqual(30)
        ->and($application->fresh()->scoring_breakdown['academic_merit'])->toEqual(25)
        ->and($application->fresh()->scoring_breakdown['demographics'])->toEqual(15)
        ->and($application->fresh()->scoring_breakdown['commitment'])->toEqual(15)
        ->and($application->fresh()->scoring_breakdown['essay_quality'])->toEqual(10)
        ->and($application->fresh()->scoring_breakdown['total'])->toEqual(95);
});
