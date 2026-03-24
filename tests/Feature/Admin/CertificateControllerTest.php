<?php

namespace Tests\Feature\Admin;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateControllerTest extends TestCase
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

    public function test_admin_can_view_certificates_index(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee($admin->company_id);
        $training = Training::factory()->create(['company_id' => $admin->company_id, 'created_by' => $admin->id]);

        Certificate::factory()->create([
            'company_id' => $admin->company_id,
            'user_id' => $employee->id,
            'training_id' => $training->id,
        ]);

        $response = $this->actingAs($admin)->get('/certificates');

        $response->assertStatus(200);
        $response->assertViewIs('admin.certificates.index');
        $response->assertViewHas('certificates');
    }

    public function test_employee_cannot_access_admin_certificates(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee($admin->company_id);

        $response = $this->actingAs($employee)->get('/certificates');

        $response->assertStatus(403);
    }
}
