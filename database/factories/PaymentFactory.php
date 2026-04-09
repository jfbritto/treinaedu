<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'subscription_id' => Subscription::factory(),
            'asaas_payment_id' => 'pay_' . $this->faker->uuid(),
            'amount' => 99.90,
            'status' => 'confirmed',
            'payment_method' => 'credit_card',
            'paid_at' => now(),
            'due_date' => now()->toDateString(),
        ];
    }
}
