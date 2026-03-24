<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Group;
use App\Models\Path;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
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

    public function test_admin_sees_admin_dashboard(): void
    {
        ['admin' => $admin] = $this->createEmployeeWithTraining();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('metrics');
    }

    public function test_employee_sees_employee_dashboard_with_pending_and_completed_trainings(): void
    {
        ['employee' => $employee, 'training' => $training, 'company' => $company] = $this->createEmployeeWithTraining();

        // Create a completed training view
        TrainingView::create([
            'training_id' => $training->id,
            'user_id' => $employee->id,
            'company_id' => $company->id,
            'progress_percent' => 100,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($employee)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.dashboard');
        $response->assertViewHas('pending');
        $response->assertViewHas('completed');
        $response->assertViewHas('certificates');
        $response->assertViewHas('chartData');
        $response->assertViewHas('paths');
    }

    public function test_instructor_sees_instructor_dashboard(): void
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();
        Subscription::factory()->create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        $instructor = User::factory()->create(['company_id' => $company->id, 'role' => 'instructor', 'active' => true]);

        $response = $this->actingAs($instructor)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('instructor.dashboard');
        $response->assertViewHas('trainings');
    }

    public function test_super_admin_gets_redirected_to_super_dashboard(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'active' => true]);

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));

        $response->assertRedirect(route('super.dashboard'));
    }
}
