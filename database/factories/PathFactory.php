<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class PathFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'color' => '#3B82F6',
            'sort_order' => 0,
            'active' => true,
        ];
    }
}
