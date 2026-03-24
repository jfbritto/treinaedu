<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'training_id' => Training::factory(),
            'company_id' => Company::factory(),
            'module_id' => null,
            'lesson_id' => null,
        ];
    }
}
