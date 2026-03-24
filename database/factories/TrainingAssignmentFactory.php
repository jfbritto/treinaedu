<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Group;
use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'training_id' => Training::factory(),
            'group_id' => Group::factory(),
            'due_date' => null,
            'mandatory' => false,
        ];
    }
}
