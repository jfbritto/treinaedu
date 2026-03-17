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

    public function test_user_with_active_trial_can_access(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create([
            'company_id' => $company->id, 'plan_id' => $plan->id,
            'status' => 'trial', 'trial_ends_at' => now()->addDays(7),
        ]);
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_with_expired_subscription_is_redirected(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create([
            'company_id' => $company->id, 'plan_id' => $plan->id,
            'status' => 'expired', 'trial_ends_at' => now()->subDay(),
        ]);
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect(route('subscription.plans'));
    }
}
