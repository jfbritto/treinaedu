<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Path;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PathControllerTest extends TestCase
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

    public function test_admin_can_view_paths_index(): void
    {
        $admin = $this->createAdmin();
        Path::factory()->create(['company_id' => $admin->company_id]);

        $response = $this->actingAs($admin)->get('/paths');

        $response->assertStatus(200);
        $response->assertViewIs('admin.paths.index');
        $response->assertViewHas('paths');
    }

    public function test_admin_can_create_path_with_trainings(): void
    {
        $admin = $this->createAdmin();
        $training1 = Training::factory()->create(['company_id' => $admin->company_id, 'created_by' => $admin->id, 'active' => true]);
        $training2 = Training::factory()->create(['company_id' => $admin->company_id, 'created_by' => $admin->id, 'active' => true]);

        $response = $this->actingAs($admin)->post('/paths', [
            'title' => 'Trilha de Onboarding',
            'description' => 'Trilha para novos colaboradores',
            'color' => '#10B981',
            'sort_order' => 1,
            'active' => true,
            'trainings' => [$training1->id, $training2->id],
        ]);

        $path = Path::where('title', 'Trilha de Onboarding')->first();
        $response->assertRedirect(route('paths.show', $path));
        $this->assertDatabaseHas('paths', [
            'title' => 'Trilha de Onboarding',
            'company_id' => $admin->company_id,
            'color' => '#10B981',
        ]);
        $this->assertDatabaseHas('path_training', [
            'path_id' => $path->id,
            'training_id' => $training1->id,
            'sort_order' => 0,
        ]);
        $this->assertDatabaseHas('path_training', [
            'path_id' => $path->id,
            'training_id' => $training2->id,
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_view_path_details(): void
    {
        $admin = $this->createAdmin();
        $path = Path::factory()->create(['company_id' => $admin->company_id]);

        $response = $this->actingAs($admin)->get("/paths/{$path->id}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.paths.show');
        $response->assertViewHas('path');
    }

    public function test_admin_can_update_path(): void
    {
        $admin = $this->createAdmin();
        $path = Path::factory()->create(['company_id' => $admin->company_id]);
        $training = Training::factory()->create(['company_id' => $admin->company_id, 'created_by' => $admin->id, 'active' => true]);

        $response = $this->actingAs($admin)->put("/paths/{$path->id}", [
            'title' => 'Trilha Atualizada',
            'description' => 'Descricao atualizada',
            'color' => '#EF4444',
            'sort_order' => 2,
            'active' => true,
            'trainings' => [$training->id],
        ]);

        $response->assertRedirect(route('paths.show', $path));
        $this->assertDatabaseHas('paths', ['id' => $path->id, 'title' => 'Trilha Atualizada', 'color' => '#EF4444']);
        $this->assertDatabaseHas('path_training', ['path_id' => $path->id, 'training_id' => $training->id]);
    }

    public function test_admin_can_delete_path(): void
    {
        $admin = $this->createAdmin();
        $path = Path::factory()->create(['company_id' => $admin->company_id]);

        $response = $this->actingAs($admin)->delete("/paths/{$path->id}");

        $response->assertRedirect(route('paths.index'));
        $this->assertSoftDeleted('paths', ['id' => $path->id]);
    }

    public function test_employee_cannot_access_paths(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee($admin->company_id);

        $response = $this->actingAs($employee)->get('/paths');

        $response->assertStatus(403);
    }

    public function test_path_from_other_company_returns_404(): void
    {
        $admin = $this->createAdmin();

        // BelongsToCompany global scope filters out other company's paths => 404
        $otherCompany = Company::factory()->create(['slug' => 'other-' . uniqid()]);
        $otherPath = Path::create([
            'company_id' => $otherCompany->id,
            'title' => 'Other Path',
            'color' => '#3B82F6',
            'sort_order' => 0,
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->get("/paths/{$otherPath->id}");

        $response->assertStatus(404);
    }
}
