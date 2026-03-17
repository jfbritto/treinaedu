<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::firstOrCreate(
            ['name' => 'Basic'],
            ['price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]
        );

        Plan::firstOrCreate(
            ['name' => 'Pro'],
            ['price' => 199.90, 'max_users' => 200, 'max_trainings' => 100]
        );

        Plan::firstOrCreate(
            ['name' => 'Enterprise'],
            ['price' => 499.90, 'max_users' => null, 'max_trainings' => null]
        );
    }
}
