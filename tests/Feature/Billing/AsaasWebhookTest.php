<?php

namespace Tests\Feature\Billing;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AsaasWebhookTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Plan $plan;
    private Subscription $subscription;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        Notification::fake();

        config(['services.asaas.webhook_token' => 'test-token']);

        $this->company = Company::factory()->create();
        $this->plan = Plan::factory()->create(['name' => 'Pro', 'price' => 99.90]);
        $this->subscription = Subscription::factory()->create([
            'company_id' => $this->company->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'asaas_subscription_id' => 'sub_webhook_123',
        ]);
        $this->admin = User::factory()->admin()->create([
            'company_id' => $this->company->id,
        ]);
    }

    public function test_webhook_with_valid_token_returns_200(): void
    {
        $response = $this->postJson('/asaas/webhook', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => [
                'id' => 'pay_valid_001',
                'externalReference' => (string) $this->company->id,
                'value' => 99.90,
                'dueDate' => now()->toDateString(),
            ],
        ], [
            'asaas-access-token' => 'test-token',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_webhook_with_invalid_token_returns_200(): void
    {
        $response = $this->postJson('/asaas/webhook', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => [
                'id' => 'pay_invalid_token_001',
                'externalReference' => (string) $this->company->id,
                'value' => 99.90,
                'dueDate' => now()->toDateString(),
            ],
        ], [
            'asaas-access-token' => 'wrong-token',
        ]);

        // Returns 200 to avoid Asaas retrying (silent ignore)
        $response->assertStatus(200);

        // But no payment should be created
        $this->assertDatabaseMissing('payments', [
            'asaas_payment_id' => 'pay_invalid_token_001',
        ]);
    }

    public function test_webhook_payment_confirmed_creates_payment_record(): void
    {
        $response = $this->postJson('/asaas/webhook', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => [
                'id' => 'pay_webhook_confirmed_001',
                'externalReference' => (string) $this->company->id,
                'value' => 99.90,
                'dueDate' => now()->toDateString(),
            ],
        ], [
            'asaas-access-token' => 'test-token',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'asaas_payment_id' => 'pay_webhook_confirmed_001',
            'status' => 'confirmed',
            'company_id' => $this->company->id,
            'subscription_id' => $this->subscription->id,
        ]);

        $this->subscription->refresh();
        $this->assertEquals('active', $this->subscription->status);
    }

    public function test_webhook_payment_overdue_updates_subscription(): void
    {
        $response = $this->postJson('/asaas/webhook', [
            'event' => 'PAYMENT_OVERDUE',
            'payment' => [
                'id' => 'pay_webhook_overdue_001',
                'externalReference' => (string) $this->company->id,
                'value' => 99.90,
                'dueDate' => now()->toDateString(),
            ],
        ], [
            'asaas-access-token' => 'test-token',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'asaas_payment_id' => 'pay_webhook_overdue_001',
            'status' => 'overdue',
            'company_id' => $this->company->id,
        ]);

        $this->subscription->refresh();
        $this->assertEquals('past_due', $this->subscription->status);
    }
}
