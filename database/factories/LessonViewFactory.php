<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\TrainingLesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonViewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lesson_id' => TrainingLesson::factory(),
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'progress_percent' => 0,
            'started_at' => now(),
            'completed_at' => null,
        ];
    }
}
