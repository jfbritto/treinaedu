<?php

namespace Database\Factories;

use App\Models\TrainingModule;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingLessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'module_id' => TrainingModule::factory(),
            'title' => fake()->sentence(),
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => fake()->numberBetween(5, 60),
            'sort_order' => 0,
            'content' => null,
        ];
    }
}
