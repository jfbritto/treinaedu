<?php

namespace Tests\Feature\Services;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_certificate_when_training_completed(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        $admin = User::create(['name' => 'A', 'email' => 'a@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'admin', 'active' => true]);
        $employee = User::create(['name' => 'E', 'email' => 'e@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'employee', 'active' => true]);

        $training = Training::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Test', 'video_url' => 'https://youtube.com/watch?v=x',
            'video_provider' => 'youtube', 'duration_minutes' => 30,
        ]);

        TrainingView::create([
            'company_id' => $company->id, 'training_id' => $training->id,
            'user_id' => $employee->id, 'progress_percent' => 100,
            'started_at' => now(), 'completed_at' => now(),
        ]);

        $service = new CertificateService();
        $this->assertTrue($service->canGenerate($employee, $training));

        $this->actingAs($employee);
        $certificate = $service->generate($employee, $training);

        $this->assertNotNull($certificate);
        $this->assertStringStartsWith('TH-', $certificate->certificate_code);
        $this->assertFileExists(storage_path("app/{$certificate->pdf_path}"));
    }

    public function test_cannot_generate_certificate_without_completion(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        $admin = User::create(['name' => 'A', 'email' => 'a@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'admin', 'active' => true]);
        $employee = User::create(['name' => 'E', 'email' => 'e@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'employee', 'active' => true]);
        $training = Training::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Test', 'video_url' => 'https://youtube.com/watch?v=x',
            'video_provider' => 'youtube', 'duration_minutes' => 30,
        ]);

        $service = new CertificateService();
        $this->assertFalse($service->canGenerate($employee, $training));
    }
}
