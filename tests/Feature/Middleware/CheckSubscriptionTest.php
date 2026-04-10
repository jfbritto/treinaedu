<?php

namespace Tests\Feature\Middleware;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_user_with_active_trial_can_access(): void
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();
        Subscription::factory()->create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);
        $admin = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_with_expired_subscription_is_redirected(): void
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();
        Subscription::factory()->create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'expired',
            'trial_ends_at' => now()->subDay(),
        ]);
        $admin = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect(route('subscription.plans'));
    }

    public function test_active_subscription_can_access(): void
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();
        Subscription::factory()->create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
        $admin = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_past_due_subscription_can_access(): void
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();
        Subscription::factory()->create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'past_due',
        ]);
        $admin = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_cancelled_subscription_with_expired_period_is_redirected(): void
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();
        Subscription::factory()->create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'cancelled',
            'current_period_end' => now()->subDay(), // Period already ended
        ]);
        $admin = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect(route('subscription.plans'));
    }

    public function test_cancelled_subscription_within_paid_period_can_access(): void
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();
        Subscription::factory()->create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'cancelled',
            'current_period_end' => now()->addDays(15), // Still within paid period
        ]);
        $admin = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_super_admin_bypasses_subscription_check(): void
    {
        // Super admin with NO subscription at all should still access dashboard
        // DashboardController redirects super_admin to /super/dashboard
        $company = Company::factory()->create();
        $superAdmin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertRedirect(route('super.dashboard'));

        // The super dashboard itself should be accessible without subscription
        $response = $this->actingAs($superAdmin)->get('/super/dashboard');
        $response->assertStatus(200);
    }
}
