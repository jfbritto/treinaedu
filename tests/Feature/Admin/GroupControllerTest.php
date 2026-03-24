<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupControllerTest extends TestCase
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

    public function test_admin_can_view_groups_index(): void
    {
        $admin = $this->createAdmin();
        Group::factory()->create(['company_id' => $admin->company_id]);

        $response = $this->actingAs($admin)->get('/groups');

        $response->assertStatus(200);
        $response->assertViewIs('admin.groups.index');
        $response->assertViewHas('groups');
    }

    public function test_admin_can_create_group(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee($admin->company_id);

        $response = $this->actingAs($admin)->post('/groups', [
            'name' => 'Equipe Vendas',
            'description' => 'Time de vendas',
            'users' => [$employee->id],
        ]);

        $response->assertRedirect(route('groups.index'));
        $this->assertDatabaseHas('groups', [
            'name' => 'Equipe Vendas',
            'company_id' => $admin->company_id,
        ]);
        $this->assertDatabaseHas('group_user', [
            'user_id' => $employee->id,
        ]);
    }

    public function test_admin_can_update_group_with_users_sync(): void
    {
        $admin = $this->createAdmin();
        $group = Group::factory()->create(['company_id' => $admin->company_id]);
        $employee1 = $this->createEmployee($admin->company_id);
        $employee2 = $this->createEmployee($admin->company_id);

        // Initially assign employee1
        $group->users()->sync([$employee1->id]);

        // Update to only employee2
        $response = $this->actingAs($admin)->put("/groups/{$group->id}", [
            'name' => 'Grupo Atualizado',
            'description' => 'Nova descricao',
            'users' => [$employee2->id],
        ]);

        $response->assertRedirect(route('groups.index'));
        $this->assertDatabaseHas('groups', ['id' => $group->id, 'name' => 'Grupo Atualizado']);
        $this->assertDatabaseHas('group_user', ['group_id' => $group->id, 'user_id' => $employee2->id]);
        $this->assertDatabaseMissing('group_user', ['group_id' => $group->id, 'user_id' => $employee1->id]);
    }

    public function test_admin_can_delete_group(): void
    {
        $admin = $this->createAdmin();
        $group = Group::factory()->create(['company_id' => $admin->company_id]);

        $response = $this->actingAs($admin)->delete("/groups/{$group->id}");

        $response->assertRedirect(route('groups.index'));
        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
    }

    public function test_employee_cannot_access_groups(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee($admin->company_id);

        $response = $this->actingAs($employee)->get('/groups');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_other_company_groups(): void
    {
        $admin = $this->createAdmin();

        // Create group in another company (BelongsToCompany global scope filters it out => 404)
        $otherCompany = Company::factory()->create(['slug' => 'other-' . uniqid()]);
        $otherGroup = Group::create([
            'company_id' => $otherCompany->id,
            'name' => 'Other Group',
        ]);

        $response = $this->actingAs($admin)->get("/groups/{$otherGroup->id}");

        $response->assertStatus(404);
    }
}
