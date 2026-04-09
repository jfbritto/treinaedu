<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::firstOrCreate(
            ['name' => 'Starter'],
            [
                'price' => 199.00,
                'max_users' => 20,
                'max_trainings' => 30,
                'features' => ['certificates', 'basic_reports'],
            ]
        );

        Plan::firstOrCreate(
            ['name' => 'Business'],
            [
                'price' => 499.00,
                'max_users' => 50,
                'max_trainings' => 100,
                'features' => ['certificates', 'basic_reports', 'ai_quiz', 'learning_paths', 'export_reports'],
            ]
        );

        Plan::firstOrCreate(
            ['name' => 'Professional'],
            [
                'price' => 999.00,
                'max_users' => 200,
                'max_trainings' => null,
                'features' => ['certificates', 'basic_reports', 'ai_quiz', 'learning_paths', 'export_reports', 'engagement'],
            ]
        );
    }
}
