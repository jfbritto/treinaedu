<?php

namespace Tests\Feature\Admin;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminWithSubscription(int $maxUsers = 50): User
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => $maxUsers, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        return User::create([
            'name' => 'Admin User', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin', 'active' => true,
        ]);
    }

    public function test_admin_dashboard_returns_200(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_dashboard_passes_all_required_metric_keys(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertViewHas('metrics');
        $metrics = $response->viewData('metrics');

        foreach ([
            'total_employees', 'trainings_created', 'trainings_completed',
            'trainings_pending', 'certificates_issued', 'completion_rate',
            'top_trainings', 'recent_employees', 'recent_completions', 'plan_user_limit',
        ] as $key) {
            $this->assertArrayHasKey($key, $metrics, "Missing key: {$key}");
        }
    }

    public function test_completion_rate_is_zero_with_no_views(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->get('/dashboard');
        $metrics = $response->viewData('metrics');
        $this->assertSame(0.0, (float) $metrics['completion_rate']);
    }

    public function test_completion_rate_calculated_correctly(): void
    {
        $admin = $this->createAdminWithSubscription();
        $emp1 = User::create(['name' => 'Emp1', 'email' => 'emp1@test.com', 'password' => 'x', 'company_id' => $admin->company_id, 'role' => 'employee', 'active' => true]);
        $emp2 = User::create(['name' => 'Emp2', 'email' => 'emp2@test.com', 'password' => 'x', 'company_id' => $admin->company_id, 'role' => 'employee', 'active' => true]);
        $training = Training::create([
            'company_id' => $admin->company_id, 'created_by' => $admin->id,
            'title' => 'Test Training', 'video_url' => 'https://youtube.com/watch?v=test',
            'video_provider' => 'youtube', 'duration_minutes' => 10, 'active' => true,
        ]);
        // 2 completed, 1 pending = 66.7%
        TrainingView::create(['company_id' => $admin->company_id, 'training_id' => $training->id, 'user_id' => $admin->id, 'completed_at' => now()]);
        TrainingView::create(['company_id' => $admin->company_id, 'training_id' => $training->id, 'user_id' => $emp1->id, 'completed_at' => now()]);
        TrainingView::create(['company_id' => $admin->company_id, 'training_id' => $training->id, 'user_id' => $emp2->id, 'completed_at' => null]);

        \Illuminate\Support\Facades\Cache::flush();
        $response = $this->actingAs($admin)->get('/dashboard');
        $metrics = $response->viewData('metrics');
        $this->assertSame(66.7, $metrics['completion_rate']);
    }

    public function test_plan_user_limit_reflects_plan(): void
    {
        $admin = $this->createAdminWithSubscription(25);
        $response = $this->actingAs($admin)->get('/dashboard');
        $metrics = $response->viewData('metrics');
        $this->assertSame(25, $metrics['plan_user_limit']);
    }
}
