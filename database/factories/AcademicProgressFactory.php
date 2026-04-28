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
            'semester' => fake()->randomElement(['Fall', 'Spring', 'Summer']),
            'year' => fake()->numberBetween(2023, 2026),
            'cgpa' => fake()->randomFloat(2, 2.5, 4.0),
            'transcript_path' => null,
        ];
    }
}
