<?php

namespace Tests\Feature\Billing;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
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

        $this->company = Company::factory()->create();
        $this->plan = Plan::factory()->create(['name' => 'Plano Pro', 'price' => 99.90, 'active' => true]);
        $this->subscription = Subscription::factory()->create([
            'company_id' => $this->company->id,
            'plan_id' => $this->plan->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);
        $this->admin = User::factory()->admin()->create([
            'company_id' => $this->company->id,
        ]);
    }

    public function test_plans_page_shows_active_plans(): void
    {
        $planBasic = Plan::factory()->create(['name' => 'Plano Basic', 'active' => true]);
        $planInactive = Plan::factory()->create(['name' => 'Plano Inativo', 'active' => false]);

        $response = $this->actingAs($this->admin)->get('/subscription/plans');

        $response->assertStatus(200);
        $response->assertSee('Plano Pro');
        $response->assertSee('Plano Basic');
        $response->assertDontSee('Plano Inativo');
    }

    public function test_subscribe_creates_subscription(): void
    {
        Http::fake([
            '*/customers' => Http::response(['id' => 'cus_test_001'], 200),
            '*/subscriptions' => Http::response(['id' => 'sub_test_001'], 200),
        ]);

        $response = $this->actingAs($this->admin)->post('/subscription/subscribe', [
            'plan_id' => $this->plan->id,
            'holder_name' => 'John Doe',
            'card_number' => '4111111111111111',
            'expiry_month' => '12',
            'expiry_year' => '2030',
            'ccv' => '123',
            'cpf_cnpj' => '12345678901',
            'phone' => '11999999999',
            'postal_code' => '01001000',
            'address_number' => '123',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->subscription->refresh();
        $this->assertEquals('active', $this->subscription->status);
        $this->assertEquals('sub_test_001', $this->subscription->asaas_subscription_id);

        $this->company->refresh();
        $this->assertEquals('cus_test_001', $this->company->asaas_customer_id);
    }

    public function test_subscribe_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post('/subscription/subscribe', []);

        $response->assertSessionHasErrors([
            'plan_id',
            'holder_name',
            'card_number',
            'expiry_month',
            'expiry_year',
            'ccv',
            'cpf_cnpj',
            'phone',
            'postal_code',
            'address_number',
        ]);
    }

    public function test_subscription_show_page(): void
    {
        // Subscription must be active for the admin to access the show route
        $this->subscription->update(['status' => 'active']);

        $response = $this->actingAs($this->admin)->get('/subscription');

        $response->assertStatus(200);
    }
}
