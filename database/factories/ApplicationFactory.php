<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'personal_info' => [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'dob' => fake()->date(),
            ],
            'financial_info' => [
                'household_income' => fake()->numberBetween(10000, 100000),
                'number_of_dependents' => fake()->numberBetween(1, 5),
            ],
            'guardian_info' => [
                'guardian_name' => fake()->name(),
                'guardian_phone' => fake()->phoneNumber(),
                'guardian_relation' => 'Parent',
            ],
            'essay' => [
                'personal_statement' => fake()->paragraphs(3, true),
                'commitment' => fake()->paragraphs(2, true),
            ],
            'scoring_breakdown' => null,
            'status' => fake()->randomElement(['draft', 'submitted', 'under_review', 'approved', 'rejected']),
        ];
    }
}
