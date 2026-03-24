<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
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

    private function createAdmin(): User
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create(['slug' => 'test-' . uniqid()]);
        Subscription::factory()->create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);

        return User::factory()->admin()->create(['company_id' => $company->id, 'active' => true]);
    }

    private function createEmployee(int $companyId): User
    {
        return User::factory()->create(['company_id' => $companyId, 'role' => 'employee', 'active' => true]);
    }

    private function createTraining(User $admin): Training
    {
        return Training::factory()->create([
            'company_id' => $admin->company_id,
            'created_by' => $admin->id,
        ]);
    }

    public function test_admin_can_view_trainings_index(): void
    {
        $admin = $this->createAdmin();
        $this->createTraining($admin);

        $response = $this->actingAs($admin)->get('/trainings');

        $response->assertStatus(200);
        $response->assertViewIs('admin.trainings.index');
        $response->assertViewHas('trainings');
    }

    public function test_admin_can_create_training_with_modules_and_lessons(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/trainings', [
            'title' => 'Treinamento de Seguranca',
            'description' => 'Descricao do treinamento',
            'is_sequential' => '0',
            'has_quiz' => '0',
            'modules' => [
                [
                    'title' => 'Modulo 1',
                    'description' => 'Primeiro modulo',
                    'is_sequential' => '0',
                    'lessons' => [
                        [
                            'title' => 'Aula 1 - Introducao',
                            'type' => 'video',
                            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                            'duration_minutes' => '15',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('trainings', [
            'title' => 'Treinamento de Seguranca',
            'company_id' => $admin->company_id,
        ]);
    }

    public function test_admin_can_update_training(): void
    {
        $admin = $this->createAdmin();
        $training = $this->createTraining($admin);

        // Create a module+lesson first so the training has content
        $module = $training->modules()->create([
            'title' => 'Modulo Original',
            'sort_order' => 0,
            'is_sequential' => false,
        ]);
        $lesson = $module->lessons()->create([
            'title' => 'Aula Original',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=abc123',
            'video_provider' => 'youtube',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->put("/trainings/{$training->id}", [
            'title' => 'Treinamento Atualizado',
            'description' => 'Nova descricao',
            'is_sequential' => false,
            'active' => true,
            'has_quiz' => false,
            'modules' => [
                [
                    'id' => $module->id,
                    'title' => 'Modulo Atualizado',
                    'description' => null,
                    'is_sequential' => false,
                    'lessons' => [
                        [
                            'id' => $lesson->id,
                            'title' => 'Aula Atualizada',
                            'type' => 'video',
                            'video_url' => 'https://www.youtube.com/watch?v=xyz789',
                            'duration_minutes' => 20,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertRedirect(route('trainings.show', $training));
        $this->assertDatabaseHas('trainings', ['id' => $training->id, 'title' => 'Treinamento Atualizado']);
        $this->assertDatabaseHas('training_modules', ['id' => $module->id, 'title' => 'Modulo Atualizado']);
        $this->assertDatabaseHas('training_lessons', ['id' => $lesson->id, 'title' => 'Aula Atualizada']);
    }

    public function test_admin_can_delete_training(): void
    {
        $admin = $this->createAdmin();
        $training = $this->createTraining($admin);

        $response = $this->actingAs($admin)->delete("/trainings/{$training->id}");

        $response->assertRedirect(route('trainings.index'));
        $this->assertSoftDeleted('trainings', ['id' => $training->id]);
    }

    public function test_admin_can_store_assignment(): void
    {
        $admin = $this->createAdmin();
        $training = $this->createTraining($admin);
        $group = Group::factory()->create(['company_id' => $admin->company_id]);

        $response = $this->actingAs($admin)->post("/trainings/{$training->id}/assignments", [
            'group_ids' => [$group->id],
            'due_date' => now()->addMonth()->format('Y-m-d'),
            'mandatory' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('training_assignments', [
            'training_id' => $training->id,
            'group_id' => $group->id,
            'company_id' => $admin->company_id,
            'mandatory' => true,
        ]);
    }

    public function test_admin_can_destroy_assignment(): void
    {
        $admin = $this->createAdmin();
        $training = $this->createTraining($admin);
        $group = Group::factory()->create(['company_id' => $admin->company_id]);

        $assignment = TrainingAssignment::create([
            'company_id' => $admin->company_id,
            'training_id' => $training->id,
            'group_id' => $group->id,
        ]);

        $response = $this->actingAs($admin)->delete("/trainings/{$training->id}/assignments/{$assignment->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('training_assignments', ['id' => $assignment->id]);
    }

    public function test_employee_cannot_access_admin_trainings(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee($admin->company_id);

        $response = $this->actingAs($employee)->get('/trainings');

        $response->assertStatus(403);
    }

    public function test_training_from_other_company_returns_404(): void
    {
        $admin = $this->createAdmin();

        // BelongsToCompany global scope filters out other company's trainings => 404
        $otherCompany = Company::factory()->create(['slug' => 'other-' . uniqid()]);
        $otherTraining = Training::create([
            'company_id' => $otherCompany->id,
            'created_by' => $admin->id,
            'title' => 'Other Training',
            'duration_minutes' => 10,
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->get("/trainings/{$otherTraining->id}");

        $response->assertStatus(404);
    }
}
