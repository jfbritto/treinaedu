<?php

namespace Tests\Feature\Employee;

use App\Models\Company;
use App\Models\Group;
use App\Models\LessonView;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingModule;
use App\Models\TrainingLesson;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingControllerTest extends TestCase
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

    public function test_employee_can_view_assigned_trainings_index(): void
    {
        ['employee' => $employee] = $this->createEmployeeWithTraining();

        $response = $this->actingAs($employee)->get(route('employee.trainings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.trainings.index');
        $response->assertViewHas('pending');
        $response->assertViewHas('completed');
    }

    public function test_employee_can_view_training_detail(): void
    {
        ['employee' => $employee, 'training' => $training, 'company' => $company] = $this->createEmployeeWithTraining();

        // Create a module with a lesson so the show page can render fully
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        TrainingLesson::factory()->create(['module_id' => $module->id]);

        $response = $this->actingAs($employee)->get(route('employee.trainings.show', $training));

        $response->assertStatus(200);
        $response->assertViewIs('employee.trainings.show');
        $response->assertViewHas('training');
        $response->assertViewHas('currentLesson');
    }

    public function test_employee_can_complete_training(): void
    {
        ['employee' => $employee, 'training' => $training, 'company' => $company] = $this->createEmployeeWithTraining();

        // Create module + lesson
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        $lesson = TrainingLesson::factory()->create(['module_id' => $module->id]);

        // Mark lesson as completed
        LessonView::create([
            'lesson_id' => $lesson->id,
            'user_id' => $employee->id,
            'company_id' => $company->id,
            'progress_percent' => 100,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        // Create a training view (started, not completed)
        TrainingView::create([
            'training_id' => $training->id,
            'user_id' => $employee->id,
            'company_id' => $company->id,
            'progress_percent' => 90,
            'started_at' => now(),
        ]);

        $response = $this->actingAs($employee)->post(route('employee.trainings.complete', $training));

        $response->assertRedirect(route('employee.trainings.show', $training));
        $this->assertDatabaseHas('training_views', [
            'training_id' => $training->id,
            'user_id' => $employee->id,
            'progress_percent' => 100,
        ]);
    }

    public function test_employee_cannot_view_unassigned_training(): void
    {
        ['employee' => $employee, 'company' => $company, 'admin' => $admin] = $this->createEmployeeWithTraining();

        // Create another training NOT assigned to the employee
        $unassignedTraining = Training::factory()->create([
            'company_id' => $company->id,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($employee)->get(route('employee.trainings.show', $unassignedTraining));

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_employee_routes(): void
    {
        ['admin' => $admin] = $this->createEmployeeWithTraining();

        $response = $this->actingAs($admin)->get(route('employee.trainings.index'));

        $response->assertStatus(403);
    }
}
