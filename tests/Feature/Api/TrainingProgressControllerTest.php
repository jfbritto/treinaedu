<?php

namespace Tests\Feature\Api;

use App\Models\Company;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingProgressControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function createEmployeeWithTraining(): array
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();
        Subscription::factory()->create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        $admin = User::factory()->admin()->create(['company_id' => $company->id, 'active' => true]);
        $employee = User::factory()->create(['company_id' => $company->id, 'role' => 'employee', 'active' => true]);
        $training = Training::factory()->create(['company_id' => $company->id, 'created_by' => $admin->id]);
        $group = Group::factory()->create(['company_id' => $company->id]);
        $group->users()->attach($employee);
        TrainingAssignment::factory()->create(['company_id' => $company->id, 'training_id' => $training->id, 'group_id' => $group->id]);

        return compact('company', 'admin', 'employee', 'training', 'group');
    }

    public function test_authenticated_user_can_update_training_progress(): void
    {
        ['employee' => $employee, 'training' => $training] = $this->createEmployeeWithTraining();

        $response = $this->actingAs($employee)->postJson(route('api.training-progress'), [
            'training_id' => $training->id,
            'progress_percent' => 50,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['progress_percent', 'can_complete']);
        $response->assertJsonPath('progress_percent', 50);
        $response->assertJsonPath('can_complete', false);
    }

    public function test_progress_never_decreases(): void
    {
        ['employee' => $employee, 'training' => $training] = $this->createEmployeeWithTraining();

        // Set initial progress to 70%
        $this->actingAs($employee)->postJson(route('api.training-progress'), [
            'training_id' => $training->id,
            'progress_percent' => 70,
        ]);

        // Try to decrease to 30%
        $response = $this->actingAs($employee)->postJson(route('api.training-progress'), [
            'training_id' => $training->id,
            'progress_percent' => 30,
        ]);

        $response->assertStatus(200);
        // Progress should remain at 70 (GREATEST in SQL)
        $response->assertJsonPath('progress_percent', 70);
    }

    public function test_unauthenticated_user_cannot_update_progress(): void
    {
        ['training' => $training] = $this->createEmployeeWithTraining();

        $response = $this->postJson(route('api.training-progress'), [
            'training_id' => $training->id,
            'progress_percent' => 50,
        ]);

        $response->assertStatus(401);
    }
}
