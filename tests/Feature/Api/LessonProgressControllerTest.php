<?php

namespace Tests\Feature\Api;

use App\Models\Company;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingModule;
use App\Models\TrainingLesson;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressControllerTest extends TestCase
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

    public function test_authenticated_user_can_update_lesson_progress(): void
    {
        ['employee' => $employee, 'training' => $training, 'company' => $company] = $this->createEmployeeWithTraining();

        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        $lesson = TrainingLesson::factory()->create(['module_id' => $module->id]);

        $response = $this->actingAs($employee)->postJson(route('api.lesson-progress'), [
            'lesson_id' => $lesson->id,
            'progress_percent' => 50,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'progress_percent',
            'lesson_completed',
            'module_progress',
            'training_progress',
        ]);
        $response->assertJsonPath('progress_percent', 50);
        $response->assertJsonPath('lesson_completed', false);
    }

    public function test_lesson_marked_complete_at_100_percent(): void
    {
        ['employee' => $employee, 'training' => $training, 'company' => $company] = $this->createEmployeeWithTraining();

        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        $lesson = TrainingLesson::factory()->create([
            'module_id' => $module->id,
            'type' => 'video',
        ]);

        $response = $this->actingAs($employee)->postJson(route('api.lesson-progress'), [
            'lesson_id' => $lesson->id,
            'progress_percent' => 100,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('progress_percent', 100);
        $response->assertJsonPath('lesson_completed', true);

        $this->assertDatabaseHas('lesson_views', [
            'lesson_id' => $lesson->id,
            'user_id' => $employee->id,
            'progress_percent' => 100,
        ]);

        // Verify completed_at is set
        $this->assertNotNull(
            \App\Models\LessonView::withoutGlobalScope('company')
                ->where('lesson_id', $lesson->id)
                ->where('user_id', $employee->id)
                ->value('completed_at')
        );
    }
}
