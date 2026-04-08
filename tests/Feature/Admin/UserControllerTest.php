<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function createAdminWithSubscription(int $maxUsers = 50): User
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => $maxUsers, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create([
            'company_id' => $company->id, 'plan_id' => $plan->id,
            'status' => 'active',
        ]);
        return User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin', 'active' => true,
        ]);
    }

    public function test_admin_can_list_users(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_user(): void
    {
        \Illuminate\Support\Facades\Notification::fake();

        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'New Employee',
            'email' => 'employee@test.com',
            'role' => 'employee',
        ]);
        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'employee@test.com']);

        // Garante que o convite foi enviado
        $newUser = User::where('email', 'employee@test.com')->first();
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $newUser,
            \App\Notifications\UserInvitedNotification::class
        );
    }

    public function test_admin_cannot_create_user_beyond_plan_limit(): void
    {
        $admin = $this->createAdminWithSubscription(maxUsers: 1);
        // Create an employee to fill the limit
        User::create([
            'name' => 'E1', 'email' => 'e1@test.com',
            'password' => 'password', 'company_id' => $admin->company_id, 'role' => 'employee',
        ]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'E2', 'email' => 'e2@test.com',
            'role' => 'employee',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_manage_users_from_other_company(): void
    {
        $admin = $this->createAdminWithSubscription();

        $otherCompany = Company::create(['name' => 'Other', 'slug' => 'other']);
        $otherUser = User::create([
            'name' => 'Other User', 'email' => 'other@test.com',
            'password' => 'password', 'company_id' => $otherCompany->id, 'role' => 'employee',
        ]);

        $response = $this->actingAs($admin)->get("/users/{$otherUser->id}/edit");
        $response->assertStatus(403);
    }

    public function test_admin_cannot_update_user_from_other_company(): void
    {
        $admin = $this->createAdminWithSubscription();

        $otherCompany = Company::create(['name' => 'Other', 'slug' => 'other2']);
        $otherUser = User::create([
            'name' => 'Other User', 'email' => 'other2@test.com',
            'password' => 'password', 'company_id' => $otherCompany->id, 'role' => 'employee', 'active' => true,
        ]);

        $response = $this->actingAs($admin)->put("/users/{$otherUser->id}", [
            'name' => 'Hacked', 'email' => 'other2@test.com', 'role' => 'employee', 'active' => '1',
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_cannot_delete_user_from_other_company(): void
    {
        $admin = $this->createAdminWithSubscription();

        $otherCompany = Company::create(['name' => 'Other', 'slug' => 'other3']);
        $otherUser = User::create([
            'name' => 'Other User', 'email' => 'other3@test.com',
            'password' => 'password', 'company_id' => $otherCompany->id, 'role' => 'employee', 'active' => true,
        ]);

        $response = $this->actingAs($admin)->delete("/users/{$otherUser->id}");
        $response->assertStatus(403);
    }
}
