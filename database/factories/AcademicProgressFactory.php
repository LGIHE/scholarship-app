<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Scholar;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicProgress>
 */
class AcademicProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scholar_id' => Scholar::factory(),
            'academic_year' => fake()->randomElement(['2023/2024', '2024/2025', '2025/2026']),
            'semester' => fake()->randomElement(['Fall', 'Spring', 'Summer']),
            'gpa' => fake()->randomFloat(2, 2.5, 4.0),
            'cgpa' => fake()->randomFloat(2, 2.5, 4.0),
            'courses_taken' => fake()->optional()->sentence(),
            'achievements' => fake()->optional()->sentence(),
            'challenges' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
