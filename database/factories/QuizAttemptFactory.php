<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizAttemptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'score' => 80,
            'passed' => true,
            'completed_at' => now(),
        ];
    }
}
