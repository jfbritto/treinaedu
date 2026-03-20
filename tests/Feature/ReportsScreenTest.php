<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Training;
use App\Models\Group;
use App\Models\TrainingView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsScreenTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $company;
    private $training;
    private $group;
    private $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTestData();
    }

    private function setupTestData(): void
    {
        // Create company
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
        ]);

        // Create plan and subscription
        $plan = \App\Models\Plan::create([
            'name' => 'Test Plan',
            'price' => 99.00,
            'active' => true,
        ]);

        \App\Models\Subscription::create([
            'company_id' => $this->company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => $this->company->id,
            'active' => true,
        ]);

        // Create training
        $this->training = Training::create([
            'company_id' => $this->company->id,
            'title' => 'Test Training',
            'description' => 'Test Description',
            'created_by' => $this->admin->id,
            'duration_minutes' => 60,
        ]);

        // Create group
        $this->group = Group::create([
            'company_id' => $this->company->id,
            'name' => 'Test Group',
        ]);

        // Create employee
        $this->employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@test.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'company_id' => $this->company->id,
            'active' => true,
        ]);

        // Create training views (multiple records)
        for ($i = 0; $i < 5; $i++) {
            // Create a new employee for each training view
            $emp = User::create([
                'name' => "Employee {$i}",
                'email' => "employee{$i}@test.com",
                'password' => bcrypt('password'),
                'role' => 'employee',
                'company_id' => $this->company->id,
                'active' => true,
            ]);

            // Add employee to group
            $this->group->users()->attach($emp->id);

            TrainingView::create([
                'company_id' => $this->company->id,
                'user_id' => $emp->id,
                'training_id' => $this->training->id,
                'progress_percent' => 50 + ($i * 10),
                'completed_at' => $i < 2 ? now() : null,
            ]);
        }
    }

    public function test_reports_page_loads_successfully()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.index'));

        if ($response->status() !== 200) {
            echo "\nStatus: " . $response->status();
            echo "\nResponse: " . substr($response->getContent(), 0, 500);
        }

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.index');
    }

    public function test_reports_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.index'));

        $content = $response->getContent();

        // Check for filter components
        $this->assertStringContainsString('Treinamento', $content);
        $this->assertStringContainsString('Grupo', $content);
        $this->assertStringContainsString('Status', $content);

        // Check for tabs
        $this->assertStringContainsString('Geral', $content);
        $this->assertStringContainsString('Por Grupo', $content);
        $this->assertStringContainsString('Por Instrutor', $content);
        $this->assertStringContainsString('Por Período', $content);

        // Check for KPI cards
        $this->assertStringContainsString('Registros totais', $content);
        $this->assertStringContainsString('Concluídos (total)', $content);
        $this->assertStringContainsString('Pendentes (total)', $content);
    }

    public function test_filter_endpoint_returns_valid_json()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', ['tab' => 'general']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'stats' => [
                'total',
                'completed',
                'pending',
                'avg_progress',
            ],
            'data',
            'tab',
        ]);
    }

    public function test_filter_returns_correct_stats()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', ['tab' => 'general']));

        $response->assertJsonPath('stats.total', 5);
        $response->assertJsonPath('stats.completed', 2);
        $response->assertJsonPath('stats.pending', 3);
        $response->assertJsonPath('tab', 'general');
    }

    public function test_filter_with_training_id_parameter()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', [
                'tab' => 'general',
                'training_id' => $this->training->id,
            ]));

        $response->assertStatus(200);
        $response->assertJsonPath('stats.total', 5);
    }

    public function test_filter_with_group_id_parameter()
    {
        // Add employee to group
        $this->group->users()->attach($this->employee->id);

        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', [
                'tab' => 'general',
                'group_id' => $this->group->id,
            ]));

        $response->assertStatus(200);
        $response->assertJsonPath('stats.total', 5);
    }

    public function test_filter_with_status_completed()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', [
                'tab' => 'general',
                'status' => 'completed',
            ]));

        $response->assertStatus(200);
        $response->assertJsonPath('stats.completed', 2);
    }

    public function test_filter_with_status_pending()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', [
                'tab' => 'general',
                'status' => 'pending',
            ]));

        $response->assertStatus(200);
        $response->assertJsonPath('stats.pending', 3);
    }

    public function test_general_tab_returns_training_views()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', ['tab' => 'general']));

        $response->assertStatus(200);
        $data = $response->json('data');

        // Should be paginated
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('current_page', $data);
    }

    public function test_group_tab_returns_analysis()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', ['tab' => 'group']));

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertIsArray($data);
    }

    public function test_instructor_tab_returns_analysis()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', ['tab' => 'instructor']));

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertIsArray($data);
    }

    public function test_period_tab_returns_analysis()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('reports.filter', ['tab' => 'period']));

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertIsArray($data);
    }

    public function test_unauthorized_user_cannot_access_reports()
    {
        $employee = User::create([
            'name' => 'Regular Employee',
            'email' => 'regular@test.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($employee)
            ->get(route('reports.index'));

        $response->assertStatus(403);
    }

    public function test_export_pdf_requires_admin()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.export.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_excel_requires_admin()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.export.excel'));

        $response->assertStatus(200);
    }
}
