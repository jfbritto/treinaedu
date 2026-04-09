<?php

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AsaasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AsaasServiceTest extends TestCase
{
    use RefreshDatabase;

    private AsaasService $service;
    private Company $company;
    private Plan $plan;
    private Subscription $subscription;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        Notification::fake();

        $this->service = new AsaasService();

        $this->company = Company::factory()->create();
        $this->plan = Plan::factory()->create(['name' => 'Pro', 'price' => 99.90]);
        $this->subscription = Subscription::factory()->create([
            'company_id' => $this->company->id,
            'plan_id' => $this->plan->id,
            'status' => 'trial',
            'asaas_subscription_id' => 'sub_existing_123',
            'trial_ends_at' => now()->addDays(7),
        ]);
        $this->admin = User::factory()->admin()->create([
            'company_id' => $this->company->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // createCustomer
    // -------------------------------------------------------------------------

    public function test_create_customer_success(): void
    {
        Http::fake([
            '*/customers' => Http::response(['id' => 'cus_abc123'], 200),
        ]);

        $result = $this->service->createCustomer($this->company, 'admin@test.com');

        $this->assertEquals('cus_abc123', $result);
        $this->company->refresh();
        $this->assertEquals('cus_abc123', $this->company->asaas_customer_id);
    }

    public function test_create_customer_failure(): void
    {
        Http::fake([
            '*/customers' => Http::response(['errors' => [['description' => 'Bad request']]], 400),
        ]);

        $result = $this->service->createCustomer($this->company, 'admin@test.com');

        $this->assertNull($result);
        $this->company->refresh();
        $this->assertNull($this->company->asaas_customer_id);
    }

    // -------------------------------------------------------------------------
    // createSubscription
    // -------------------------------------------------------------------------

    public function test_create_subscription_success(): void
    {
        Http::fake([
            '*/subscriptions' => Http::response(['id' => 'sub_new_456'], 200),
        ]);

        $this->company->update(['asaas_customer_id' => 'cus_abc123']);

        $cardData = $this->validCardData();

        $result = $this->service->createSubscription($this->company, $this->plan, $cardData);

        $this->assertEquals('sub_new_456', $result);

        $this->subscription->refresh();
        $this->assertEquals('active', $this->subscription->status);
        $this->assertEquals('sub_new_456', $this->subscription->asaas_subscription_id);
    }

    public function test_create_subscription_failure(): void
    {
        Http::fake([
            '*/subscriptions' => Http::response(['errors' => [['description' => 'Invalid card']]], 400),
        ]);

        $this->company->update(['asaas_customer_id' => 'cus_abc123']);

        $cardData = $this->validCardData();

        $result = $this->service->createSubscription($this->company, $this->plan, $cardData);

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // handleWebhook
    // -------------------------------------------------------------------------

    public function test_handle_webhook_payment_confirmed(): void
    {
        $payload = [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => [
                'id' => 'pay_confirmed_001',
                'externalReference' => (string) $this->company->id,
                'value' => 99.90,
                'dueDate' => now()->toDateString(),
            ],
        ];

        $this->service->handleWebhook($payload);

        $this->subscription->refresh();
        $this->assertEquals('active', $this->subscription->status);

        $this->assertDatabaseHas('payments', [
            'asaas_payment_id' => 'pay_confirmed_001',
            'status' => 'confirmed',
            'subscription_id' => $this->subscription->id,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_handle_webhook_payment_overdue(): void
    {
        $this->subscription->update(['status' => 'active']);

        $payload = [
            'event' => 'PAYMENT_OVERDUE',
            'payment' => [
                'id' => 'pay_overdue_001',
                'externalReference' => (string) $this->company->id,
                'value' => 99.90,
                'dueDate' => now()->toDateString(),
            ],
        ];

        $this->service->handleWebhook($payload);

        $this->subscription->refresh();
        $this->assertEquals('past_due', $this->subscription->status);

        $this->assertDatabaseHas('payments', [
            'asaas_payment_id' => 'pay_overdue_001',
            'status' => 'overdue',
            'subscription_id' => $this->subscription->id,
        ]);
    }

    public function test_handle_webhook_unknown_event(): void
    {
        $payload = [
            'event' => 'SOME_UNKNOWN_EVENT',
            'payment' => [
                'id' => 'pay_unknown_001',
                'externalReference' => (string) $this->company->id,
                'value' => 99.90,
            ],
        ];

        $this->service->handleWebhook($payload);

        $this->assertDatabaseMissing('payments', [
            'asaas_payment_id' => 'pay_unknown_001',
        ]);

        $this->subscription->refresh();
        $this->assertEquals('trial', $this->subscription->status);
    }

    public function test_handle_webhook_idempotency(): void
    {
        $payload = [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => [
                'id' => 'pay_idempotent_001',
                'externalReference' => (string) $this->company->id,
                'value' => 99.90,
                'dueDate' => now()->toDateString(),
            ],
        ];

        // Process the webhook twice
        $this->service->handleWebhook($payload);
        $this->service->handleWebhook($payload);

        // Only one Payment record should exist
        $count = Payment::withoutGlobalScopes()
            ->where('asaas_payment_id', 'pay_idempotent_001')
            ->count();

        $this->assertEquals(1, $count);
    }

    // -------------------------------------------------------------------------
    // cancelSubscription
    // -------------------------------------------------------------------------

    public function test_cancel_subscription_success(): void
    {
        Http::fake([
            '*/subscriptions/*' => Http::response(['deleted' => true], 200),
        ]);

        $this->subscription->update(['status' => 'active']);

        $result = $this->service->cancelSubscription($this->subscription);

        $this->assertTrue($result);

        $this->subscription->refresh();
        $this->assertEquals('cancelled', $this->subscription->status);
    }

    public function test_cancel_subscription_failure(): void
    {
        Http::fake([
            '*/subscriptions/*' => Http::response(['errors' => [['description' => 'Not found']]], 400),
        ]);

        $this->subscription->update(['status' => 'active']);

        $result = $this->service->cancelSubscription($this->subscription);

        $this->assertFalse($result);

        $this->subscription->refresh();
        $this->assertEquals('active', $this->subscription->status);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function validCardData(): array
    {
        return [
            'holder_name' => 'John Doe',
            'number' => '4111111111111111',
            'expiry_month' => '12',
            'expiry_year' => '2030',
            'ccv' => '123',
            'holder_email' => 'admin@test.com',
            'cpf_cnpj' => '12345678901',
            'phone' => '11999999999',
            'postal_code' => '01001000',
            'address_number' => '123',
        ];
    }
}
