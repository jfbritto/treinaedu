<?php

namespace Tests\Feature\Middleware;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
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

    public function test_employee_cannot_access_admin_routes(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create([
            'company_id' => $company->id, 'plan_id' => $plan->id,
            'status' => 'trial', 'trial_ends_at' => now()->addDays(7),
        ]);
        $employee = User::create([
            'name' => 'Emp', 'email' => 'emp@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'employee',
        ]);

        $response = $this->actingAs($employee)->get('/users');
        $response->assertStatus(403);
    }
}
