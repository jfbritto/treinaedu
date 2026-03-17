<?php

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_has_active_subscription_when_on_trial(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $company->load('subscription');

        $this->assertTrue($company->isOnTrial());
        $this->assertTrue($company->hasActiveSubscription());
    }

    public function test_company_has_no_active_subscription_when_expired(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'expired',
            'trial_ends_at' => now()->subDay(),
        ]);

        $company->load('subscription');

        $this->assertFalse($company->hasActiveSubscription());
    }

    public function test_company_detects_user_limit_reached(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 2, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        User::create(['name' => 'E1', 'email' => 'e1@test.com', 'password' => 'password', 'company_id' => $company->id, 'role' => 'employee']);
        User::create(['name' => 'E2', 'email' => 'e2@test.com', 'password' => 'password', 'company_id' => $company->id, 'role' => 'employee']);

        $company->load('subscription.plan');

        $this->assertTrue($company->hasReachedUserLimit());
    }

    public function test_enterprise_plan_never_reaches_user_limit(): void
    {
        $plan = Plan::create(['name' => 'Enterprise', 'price' => 499.90, 'max_users' => null, 'max_trainings' => null]);
        $company = Company::create(['name' => 'Big Corp', 'slug' => 'big-corp']);
        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        // Criar vários usuários
        for ($i = 1; $i <= 10; $i++) {
            User::create(['name' => "E{$i}", 'email' => "e{$i}@test.com", 'password' => 'password', 'company_id' => $company->id, 'role' => 'employee']);
        }

        $company->load('subscription.plan');

        $this->assertFalse($company->hasReachedUserLimit());
    }
}
