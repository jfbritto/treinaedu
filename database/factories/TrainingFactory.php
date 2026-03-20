<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'video_url' => fake()->url(),
            'video_provider' => 'youtube',
            'duration_minutes' => fake()->numberBetween(10, 180),
            'passing_score' => 70,
            'has_quiz' => false,
            'active' => true,
            'is_sequential' => false,
        ];
    }
}
