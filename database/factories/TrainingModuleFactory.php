<?php

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'training_id' => Training::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->optional()->text(),
            'sort_order' => 0,
            'is_sequential' => false,
        ];
    }
}
