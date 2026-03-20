<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingViewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'training_id' => Training::factory(),
            'user_id' => User::factory(),
            'progress_percent' => fake()->numberBetween(0, 100),
            'started_at' => fake()->dateTime(),
            'completed_at' => null,
        ];
    }
}
