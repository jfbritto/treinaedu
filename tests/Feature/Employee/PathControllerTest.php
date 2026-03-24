<?php

namespace Tests\Feature\Employee;

use App\Models\Company;
use App\Models\Group;
use App\Models\Path;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PathControllerTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_employee_can_view_paths_index(): void
    {
        ['employee' => $employee, 'company' => $company, 'training' => $training] = $this->createEmployeeWithTraining();

        $path = Path::factory()->create(['company_id' => $company->id, 'active' => true]);
        $path->trainings()->attach($training, ['sort_order' => 0]);

        $response = $this->actingAs($employee)->get(route('employee.paths.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.paths.index');
        $response->assertViewHas('paths');
    }

    public function test_employee_can_view_path_detail_with_training_status(): void
    {
        ['employee' => $employee, 'company' => $company, 'training' => $training] = $this->createEmployeeWithTraining();

        $path = Path::factory()->create(['company_id' => $company->id, 'active' => true]);
        $path->trainings()->attach($training, ['sort_order' => 0]);

        $response = $this->actingAs($employee)->get(route('employee.paths.show', $path));

        $response->assertStatus(200);
        $response->assertViewIs('employee.paths.show');
        $response->assertViewHas('path');
        $response->assertViewHas('completedCount', 0);
        $response->assertViewHas('totalCount', 1);
        $response->assertViewHas('progressPercent', 0);
    }

    public function test_inactive_path_returns_404(): void
    {
        ['employee' => $employee, 'company' => $company] = $this->createEmployeeWithTraining();

        $inactivePath = Path::factory()->create(['company_id' => $company->id, 'active' => false]);

        $response = $this->actingAs($employee)->get(route('employee.paths.show', $inactivePath));

        $response->assertStatus(404);
    }

    public function test_path_from_other_company_returns_404(): void
    {
        ['employee' => $employee] = $this->createEmployeeWithTraining();

        $otherCompany = Company::factory()->create();
        $otherPath = Path::factory()->create(['company_id' => $otherCompany->id, 'active' => true]);

        $response = $this->actingAs($employee)->get(route('employee.paths.show', $otherPath));

        $response->assertStatus(404);
    }
}
