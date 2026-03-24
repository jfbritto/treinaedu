<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateVerificationTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $admin;
    private User $employee;
    private Training $training;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $plan = Plan::factory()->create();
        $this->company = Company::factory()->create();
        Subscription::factory()->create([
            'company_id' => $this->company->id,
            'plan_id' => $plan->id,
        ]);
        $this->admin = User::factory()->admin()->create([
            'company_id' => $this->company->id,
            'active' => true,
        ]);
        $this->employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
            'active' => true,
        ]);
        $this->training = Training::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->admin->id,
        ]);
    }

    // ---- Verification page loads ----

    public function test_verification_page_loads(): void
    {
        $response = $this->get(route('certificate.verify'));

        $response->assertStatus(200);
        $response->assertViewIs('certificates.verify');
    }

    // ---- Valid certificate code shows certificate info ----

    public function test_valid_certificate_code_shows_certificate_info(): void
    {
        $certificate = Certificate::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
            'training_id' => $this->training->id,
            'certificate_code' => 'TH-2026-ABCD-EFGH',
        ]);

        $response = $this->post(route('certificate.verify.post'), [
            'code' => 'TH-2026-ABCD-EFGH',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('certificates.verify');
        $response->assertViewHas('certificate', function ($viewCert) use ($certificate) {
            return $viewCert->id === $certificate->id;
        });
    }

    // ---- Invalid certificate code shows no certificate ----

    public function test_invalid_certificate_code_returns_null_certificate(): void
    {
        $response = $this->post(route('certificate.verify.post'), [
            'code' => 'INVALID-CODE-12345',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('certificates.verify');
        $response->assertViewHas('certificate', null);
    }

    // ---- Validation: code is required ----

    public function test_verification_requires_code(): void
    {
        $response = $this->post(route('certificate.verify.post'), [
            'code' => '',
        ]);

        $response->assertSessionHasErrors('code');
    }

    // ---- Certificate accessible by code URL ----

    public function test_certificate_accessible_by_code_url(): void
    {
        $certificate = Certificate::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
            'training_id' => $this->training->id,
            'certificate_code' => 'TH-2026-WXYZ-1234',
        ]);

        $response = $this->get(route('certificate.show', ['code' => 'TH-2026-WXYZ-1234']));

        $response->assertStatus(200);
        $response->assertViewIs('certificates.show');
        $response->assertViewHas('certificate', function ($viewCert) use ($certificate) {
            return $viewCert->id === $certificate->id;
        });
    }

    public function test_certificate_by_code_url_returns_404_for_invalid_code(): void
    {
        $response = $this->get(route('certificate.show', ['code' => 'NONEXISTENT-CODE']));

        $response->assertStatus(404);
    }

    // ---- Certificate loads related models ----

    public function test_verified_certificate_loads_user_and_training(): void
    {
        Certificate::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
            'training_id' => $this->training->id,
            'certificate_code' => 'TH-2026-LOAD-TEST',
        ]);

        $response = $this->post(route('certificate.verify.post'), [
            'code' => 'TH-2026-LOAD-TEST',
        ]);

        $response->assertViewHas('certificate', function ($certificate) {
            return $certificate->relationLoaded('user')
                && $certificate->relationLoaded('training')
                && $certificate->relationLoaded('company')
                && $certificate->user->id === $this->employee->id
                && $certificate->training->id === $this->training->id;
        });
    }
}
