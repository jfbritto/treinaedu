<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'price' => 9900,
            'max_users' => 50,
            'max_trainings' => 100,
            'features' => ['certificates', 'reports'],
            'active' => true,
        ];
    }
}
