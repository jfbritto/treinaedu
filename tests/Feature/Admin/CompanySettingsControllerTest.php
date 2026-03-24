<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanySettingsControllerTest extends TestCase
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

    public function test_admin_can_view_settings_form(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/company/settings');

        $response->assertStatus(200);
        $response->assertViewIs('admin.settings.edit');
        $response->assertViewHas('company');
    }

    public function test_admin_can_update_company_settings(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->put('/company/settings', [
            'name' => 'Empresa Atualizada',
            'primary_color' => '#FF5733',
            'secondary_color' => '#C70039',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->company->refresh();
        $this->assertSame('Empresa Atualizada', $admin->company->name);
        $this->assertSame('#FF5733', $admin->company->primary_color);
        $this->assertSame('#C70039', $admin->company->secondary_color);
        $this->assertNotNull($admin->company->logo_path);
        Storage::disk('public')->assertExists($admin->company->logo_path);
    }

    public function test_admin_can_update_settings_without_logo(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->put('/company/settings', [
            'name' => 'Novo Nome',
            'primary_color' => '#3B82F6',
            'secondary_color' => '#1E40AF',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->company->refresh();
        $this->assertSame('Novo Nome', $admin->company->name);
    }

    public function test_employee_cannot_access_settings(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee($admin->company_id);

        $response = $this->actingAs($employee)->get('/company/settings');

        $response->assertStatus(403);
    }
}
