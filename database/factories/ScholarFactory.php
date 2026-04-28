<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Application;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Scholar>
 */
class ScholarFactory extends Factory
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
            'application_id' => Application::factory(),
            'student_id' => 'LGF' . fake()->unique()->numerify('#####'),
            'university' => fake()->company() . ' University',
            'course' => fake()->jobTitle(),
            'graduation_date' => fake()->dateTimeBetween('+1 year', '+4 years'),
        ];
    }
}
