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
        $this->baseUrl = config('services.asaas.base_url', 'https://sandbox.asaas.com/api/v3');
        $this->apiKey = config('services.asaas.api_key', '');
    }

    public function createCustomer(Company $company, string $email): ?string
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/customers", [
                'name' => $company->name,
                'email' => $email,
                'externalReference' => $company->id,
            ]);

        if ($response->successful()) {
            $customerId = $response->json('id');
            $company->update(['asaas_customer_id' => $customerId]);
            return $customerId;
        }

        Log::error('Asaas createCustomer failed', ['response' => $response->json()]);
        return null;
    }

    public function createSubscription(Company $company, Plan $plan, string $paymentMethod): ?string
    {
        $billingType = match ($paymentMethod) {
            'boleto' => 'BOLETO',
            'pix' => 'PIX',
            'credit_card' => 'CREDIT_CARD',
            default => 'PIX',
        };

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/subscriptions", [
                'customer' => $company->asaas_customer_id,
                'billingType' => $billingType,
                'value' => $plan->price,
                'cycle' => 'MONTHLY',
                'description' => "TreinaEdu - Plano {$plan->name}",
                'externalReference' => $company->id,
            ]);

        if ($response->successful()) {
            $subscriptionId = $response->json('id');

            $company->subscription()->update([
                'plan_id' => $plan->id,
                'asaas_subscription_id' => $subscriptionId,
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            return $subscriptionId;
        }

        Log::error('Asaas createSubscription failed', ['response' => $response->json()]);
        return null;
    }

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

        // Idempotency check
        $asaasPaymentId = $payment['id'] ?? null;
        if ($asaasPaymentId && Payment::withoutGlobalScopes()->where('asaas_payment_id', $asaasPaymentId)->exists()) {
            return;
        }

        match ($event) {
            'PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED' => $this->handlePaymentConfirmed($subscription, $payment),
            'PAYMENT_OVERDUE' => $this->handlePaymentOverdue($subscription, $payment),
            default => null,
        };
    }

    private function mapPaymentMethod(string $billingType): string
    {
        return match (strtolower($billingType)) {
            'boleto' => 'boleto',
            'pix' => 'pix',
            'credit_card' => 'credit_card',
            default => 'pix',
        };
    }

    private function handlePaymentConfirmed(Subscription $subscription, array $payment): void
    {
        $subscription->update([
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        try {
            Payment::create([
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'asaas_payment_id' => $payment['id'] ?? null,
                'amount' => $payment['value'] ?? 0,
                'status' => 'confirmed',
                'payment_method' => $this->mapPaymentMethod($payment['billingType'] ?? 'PIX'),
                'paid_at' => now(),
                'due_date' => $payment['dueDate'] ?? now()->toDateString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Asaas webhook payment create failed', ['error' => $e->getMessage()]);
        }

        // Notify admin
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
                'payment_method' => $this->mapPaymentMethod($payment['billingType'] ?? 'PIX'),
                'due_date' => $payment['dueDate'] ?? now()->toDateString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Asaas webhook payment create failed', ['error' => $e->getMessage()]);
        }

        // Notify admin
        $admin = \App\Models\User::withoutGlobalScopes()
            ->where('company_id', $subscription->company_id)
            ->where('role', 'admin')
            ->first();
        $admin?->notify(new \App\Notifications\PaymentOverdueNotification());
    }

    public function cancelSubscription(Subscription $subscription): bool
    {
        if (!$subscription->asaas_subscription_id) {
            return false;
        }

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->delete("{$this->baseUrl}/subscriptions/{$subscription->asaas_subscription_id}");

        if ($response->successful()) {
            $subscription->update(['status' => 'cancelled']);
            return true;
        }

        return false;
    }
}
