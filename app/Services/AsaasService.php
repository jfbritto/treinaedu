<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.asaas.base_url') ?? 'https://sandbox.asaas.com/api/v3';
        $this->apiKey = config('services.asaas.api_key') ?? '';
    }

    /**
     * Create a customer in Asaas and store the customer ID in the company.
     */
    public function createCustomer(Company $company, string $email): ?string
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/customers", [
                'name' => $company->name,
                'email' => $email,
                'externalReference' => (string) $company->id,
            ]);

        if ($response->successful()) {
            $customerId = $response->json('id');
            $company->update(['asaas_customer_id' => $customerId]);
            return $customerId;
        }

        Log::error('Asaas createCustomer failed', [
            'status' => $response->status(),
            'response' => $response->json(),
        ]);
        return null;
    }

    /**
     * Create a credit card subscription in Asaas.
     */
    public function createSubscription(Company $company, Plan $plan, array $cardData): ?string
    {
        $payload = [
            'customer' => $company->asaas_customer_id,
            'billingType' => 'CREDIT_CARD',
            'value' => (float) $plan->price,
            'cycle' => 'MONTHLY',
            'description' => "TreinaEdu - Plano {$plan->name}",
            'externalReference' => (string) $company->id,
            'creditCard' => [
                'holderName' => $cardData['holder_name'],
                'number' => preg_replace('/\D/', '', $cardData['number']),
                'expiryMonth' => $cardData['expiry_month'],
                'expiryYear' => $cardData['expiry_year'],
                'ccv' => $cardData['ccv'],
            ],
            'creditCardHolderInfo' => [
                'name' => $cardData['holder_name'],
                'email' => $cardData['holder_email'],
                'cpfCnpj' => preg_replace('/\D/', '', $cardData['cpf_cnpj']),
                'postalCode' => preg_replace('/\D/', '', $cardData['postal_code']),
                'addressNumber' => $cardData['address_number'],
                'phone' => preg_replace('/\D/', '', $cardData['phone']),
            ],
        ];

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/subscriptions", $payload);

        if ($response->successful()) {
            $subscriptionId = $response->json('id');

            $subscription = $company->subscription;
            $subscription->update([
                'plan_id' => $plan->id,
                'asaas_subscription_id' => $subscriptionId,
                'status' => 'active',
                'trial_ends_at' => null,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            // Create initial payment record (credit card is charged immediately)
            try {
                Payment::create([
                    'company_id' => $company->id,
                    'subscription_id' => $subscription->id,
                    'asaas_payment_id' => null, // Will be updated by webhook
                    'amount' => (float) $plan->price,
                    'status' => 'confirmed',
                    'payment_method' => 'credit_card',
                    'paid_at' => now(),
                    'due_date' => now()->toDateString(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create initial payment record', ['error' => $e->getMessage()]);
            }

            // Notify admin about subscription creation
            $admin = \App\Models\User::withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('role', 'admin')
                ->first();
            $admin?->notify(new \App\Notifications\SubscriptionCreatedNotification($plan));

            return $subscriptionId;
        }

        Log::error('Asaas createSubscription failed', [
            'status' => $response->status(),
            'response' => $response->json(),
        ]);
        return null;
    }

    /**
     * Process an Asaas webhook event.
     */
    public function handleWebhook(array $payload): void
    {
        $event = $payload['event'] ?? null;
        $payment = $payload['payment'] ?? [];
        $externalReference = $payment['externalReference'] ?? null;

        if (!$event || !$externalReference) {
            return;
        }

        $subscription = Subscription::withoutGlobalScopes()
            ->where('company_id', $externalReference)
            ->first();

        if (!$subscription) {
            return;
        }

        $asaasPaymentId = $payment['id'] ?? null;

        // Events that UPDATE an existing payment (don't need idempotency on creation)
        $updateEvents = ['PAYMENT_REFUNDED', 'PAYMENT_DELETED', 'PAYMENT_CHARGEBACK_REQUESTED', 'PAYMENT_REPROVED_BY_RISK_ANALYSIS'];

        // For creation events, skip if already processed (idempotency)
        if (!in_array($event, $updateEvents) && $asaasPaymentId && Payment::withoutGlobalScopes()->where('asaas_payment_id', $asaasPaymentId)->exists()) {
            return;
        }

        match ($event) {
            'PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED' => $this->handlePaymentConfirmed($subscription, $payment),
            'PAYMENT_OVERDUE' => $this->handlePaymentOverdue($subscription, $payment),
            'PAYMENT_REFUNDED' => $this->handlePaymentRefunded($subscription, $payment),
            'PAYMENT_DELETED' => $this->handlePaymentDeleted($payment),
            'PAYMENT_CHARGEBACK_REQUESTED' => $this->handleChargeback($subscription, $payment),
            'PAYMENT_REPROVED_BY_RISK_ANALYSIS' => $this->handlePaymentReproved($subscription, $payment),
            default => null,
        };
    }

    /**
     * Update the credit card on an existing subscription.
     */
    public function updateCreditCard(Subscription $subscription, array $cardData): bool
    {
        if (!$subscription->asaas_subscription_id) {
            return false;
        }

        $payload = [
            'creditCard' => [
                'holderName' => $cardData['holder_name'],
                'number' => preg_replace('/\D/', '', $cardData['number']),
                'expiryMonth' => $cardData['expiry_month'],
                'expiryYear' => $cardData['expiry_year'],
                'ccv' => $cardData['ccv'],
            ],
            'creditCardHolderInfo' => [
                'name' => $cardData['holder_name'],
                'email' => $cardData['holder_email'],
                'cpfCnpj' => preg_replace('/\D/', '', $cardData['cpf_cnpj']),
                'postalCode' => preg_replace('/\D/', '', $cardData['postal_code']),
                'addressNumber' => $cardData['address_number'],
                'phone' => preg_replace('/\D/', '', $cardData['phone']),
            ],
        ];

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->put("{$this->baseUrl}/subscriptions/{$subscription->asaas_subscription_id}", $payload);

        if ($response->successful()) {
            return true;
        }

        Log::error('Asaas updateCreditCard failed', [
            'status' => $response->status(),
            'response' => $response->json(),
        ]);
        return false;
    }

    /**
     * Cancel an active subscription in Asaas.
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        if (!$subscription->asaas_subscription_id) {
            return false;
        }

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->delete("{$this->baseUrl}/subscriptions/{$subscription->asaas_subscription_id}");

        if ($response->successful()) {
            $subscription->update(['status' => 'cancelled']);

            $admin = \App\Models\User::withoutGlobalScopes()
                ->where('company_id', $subscription->company_id)
                ->where('role', 'admin')
                ->first();
            $admin?->notify(new \App\Notifications\SubscriptionCancelledNotification());

            return true;
        }

        Log::error('Asaas cancelSubscription failed', [
            'status' => $response->status(),
            'response' => $response->json(),
        ]);
        return false;
    }

    private function handlePaymentConfirmed(Subscription $subscription, array $payment): void
    {
        $subscription->update([
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $asaasPaymentId = $payment['id'] ?? null;
        $dueDate = $payment['dueDate'] ?? now()->toDateString();

        // Check if initial payment already exists (created by createSubscription)
        // If so, update it with the Asaas ID instead of creating a duplicate
        $existing = Payment::withoutGlobalScopes()
            ->where('subscription_id', $subscription->id)
            ->where('status', 'confirmed')
            ->whereDate('due_date', $dueDate)
            ->whereNull('asaas_payment_id')
            ->first();

        if ($existing) {
            $existing->update(['asaas_payment_id' => $asaasPaymentId]);
            return;
        }

        try {
            Payment::create([
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'asaas_payment_id' => $payment['id'] ?? null,
                'amount' => $payment['value'] ?? 0,
                'status' => 'confirmed',
                'payment_method' => 'credit_card',
                'paid_at' => now(),
                'due_date' => $payment['dueDate'] ?? now()->toDateString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Asaas webhook: payment record creation failed', ['error' => $e->getMessage()]);
        }

        $admin = \App\Models\User::withoutGlobalScopes()
            ->where('company_id', $subscription->company_id)
            ->where('role', 'admin')
            ->first();
        $admin?->notify(new \App\Notifications\PaymentConfirmedNotification($payment['value'] ?? 0));
    }

    private function handlePaymentOverdue(Subscription $subscription, array $payment): void
    {
        $subscription->update(['status' => 'past_due']);

        try {
            Payment::create([
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'asaas_payment_id' => $payment['id'] ?? null,
                'amount' => $payment['value'] ?? 0,
                'status' => 'overdue',
                'payment_method' => 'credit_card',
                'due_date' => $payment['dueDate'] ?? now()->toDateString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Asaas webhook: payment record creation failed', ['error' => $e->getMessage()]);
        }

        $admin = \App\Models\User::withoutGlobalScopes()
            ->where('company_id', $subscription->company_id)
            ->where('role', 'admin')
            ->first();
        $admin?->notify(new \App\Notifications\PaymentOverdueNotification());
    }

    private function handlePaymentRefunded(Subscription $subscription, array $payment): void
    {
        $asaasPaymentId = $payment['id'] ?? null;

        // Update existing payment record to refunded
        if ($asaasPaymentId) {
            Payment::withoutGlobalScopes()
                ->where('asaas_payment_id', $asaasPaymentId)
                ->update(['status' => 'refunded']);
        }

        Log::info('Asaas webhook: payment refunded', [
            'company_id' => $subscription->company_id,
            'payment_id' => $asaasPaymentId,
        ]);

        $admin = \App\Models\User::withoutGlobalScopes()
            ->where('company_id', $subscription->company_id)
            ->where('role', 'admin')
            ->first();
        $admin?->notify(new \App\Notifications\PaymentRefundedNotification($payment['value'] ?? 0));
    }

    private function handlePaymentDeleted(array $payment): void
    {
        $asaasPaymentId = $payment['id'] ?? null;

        if ($asaasPaymentId) {
            Payment::withoutGlobalScopes()
                ->where('asaas_payment_id', $asaasPaymentId)
                ->where('status', 'pending')
                ->delete();
        }
    }

    private function handleChargeback(Subscription $subscription, array $payment): void
    {
        // Chargeback is critical — suspend subscription immediately
        $subscription->update(['status' => 'past_due']);

        $asaasPaymentId = $payment['id'] ?? null;
        if ($asaasPaymentId) {
            Payment::withoutGlobalScopes()
                ->where('asaas_payment_id', $asaasPaymentId)
                ->update(['status' => 'refunded']);
        }

        Log::warning('Asaas webhook: CHARGEBACK requested', [
            'company_id' => $subscription->company_id,
            'payment_id' => $asaasPaymentId,
            'amount' => $payment['value'] ?? 0,
        ]);

        $admin = \App\Models\User::withoutGlobalScopes()
            ->where('company_id', $subscription->company_id)
            ->where('role', 'admin')
            ->first();
        $admin?->notify(new \App\Notifications\PaymentChargebackNotification($payment['value'] ?? 0));
    }

    private function handlePaymentReproved(Subscription $subscription, array $payment): void
    {
        // Card reproved by risk analysis — treat like overdue
        $subscription->update(['status' => 'past_due']);

        Log::warning('Asaas webhook: payment reproved by risk analysis', [
            'company_id' => $subscription->company_id,
            'payment_id' => $payment['id'] ?? null,
        ]);

        $admin = \App\Models\User::withoutGlobalScopes()
            ->where('company_id', $subscription->company_id)
            ->where('role', 'admin')
            ->first();
        $admin?->notify(new \App\Notifications\PaymentOverdueNotification());
    }
}
