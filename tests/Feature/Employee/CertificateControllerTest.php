<?php

namespace Tests\Feature\Employee;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateControllerTest extends TestCase
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

    public function test_employee_can_view_certificates_index(): void
    {
        ['employee' => $employee, 'company' => $company, 'training' => $training] = $this->createEmployeeWithTraining();

        Certificate::factory()->create([
            'company_id' => $company->id,
            'user_id' => $employee->id,
            'training_id' => $training->id,
        ]);

        $response = $this->actingAs($employee)->get(route('employee.certificates.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.certificates.index');
        $response->assertViewHas('certificates');
    }

    public function test_employee_can_view_certificate_show_page(): void
    {
        ['employee' => $employee, 'company' => $company, 'training' => $training] = $this->createEmployeeWithTraining();

        $certificate = Certificate::factory()->create([
            'company_id' => $company->id,
            'user_id' => $employee->id,
            'training_id' => $training->id,
        ]);

        $response = $this->actingAs($employee)->get(route('employee.certificates.show', $certificate));

        $response->assertStatus(200);
        $response->assertViewIs('employee.certificates.show');
        $response->assertViewHas('certificate');
    }

    public function test_employee_can_view_certificate_success_page(): void
    {
        ['employee' => $employee, 'company' => $company, 'training' => $training] = $this->createEmployeeWithTraining();

        $certificate = Certificate::factory()->create([
            'company_id' => $company->id,
            'user_id' => $employee->id,
            'training_id' => $training->id,
        ]);

        $response = $this->actingAs($employee)->get(route('employee.certificates.success', $certificate));

        $response->assertStatus(200);
        $response->assertViewIs('employee.certificates.success');
    }

    public function test_employee_cannot_view_other_users_certificate(): void
    {
        ['employee' => $employee, 'company' => $company, 'training' => $training] = $this->createEmployeeWithTraining();

        $otherEmployee = User::factory()->create(['company_id' => $company->id, 'role' => 'employee', 'active' => true]);

        $certificate = Certificate::factory()->create([
            'company_id' => $company->id,
            'user_id' => $otherEmployee->id,
            'training_id' => $training->id,
        ]);

        $response = $this->actingAs($employee)->get(route('employee.certificates.show', $certificate));
        $response->assertStatus(403);

        $response = $this->actingAs($employee)->get(route('employee.certificates.success', $certificate));
        $response->assertStatus(403);
    }

    public function test_employee_can_download_certificate_pdf_when_file_exists(): void
    {
        ['employee' => $employee, 'company' => $company, 'training' => $training] = $this->createEmployeeWithTraining();

        $pdfPath = 'certificates/test-cert.pdf';

        $certificate = Certificate::factory()->create([
            'company_id' => $company->id,
            'user_id' => $employee->id,
            'training_id' => $training->id,
            'pdf_path' => $pdfPath,
        ]);

        // Create the file on disk so the controller finds it
        $fullPath = storage_path("app/{$pdfPath}");
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        file_put_contents($fullPath, 'fake-pdf-content');

        $response = $this->actingAs($employee)->get(route('employee.certificates.download', $certificate));

        $response->assertStatus(200);
        $response->assertDownload('test-cert.pdf');

        // Cleanup
        @unlink($fullPath);
    }
}
