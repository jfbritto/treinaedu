<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\PaymentOverdueNotification;
use App\Notifications\SubscriptionCancelledNotification;
use App\Notifications\TrialExpiredNotification;
use Illuminate\Console\Command;

class ExpireOverdueSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire-overdue';
    protected $description = 'Expire subscriptions past grace period, expire ended trials, and catch stuck past_due';

    public function handle(): int
    {
        $expired = 0;

        // 1. Expire past_due subscriptions with overdue payment older than 7 days
        $pastDueSubscriptions = Subscription::withoutGlobalScopes()
            ->where('status', 'past_due')
            ->get();

        foreach ($pastDueSubscriptions as $subscription) {
            $shouldExpire = false;

            // Check overdue payment > 7 days
            $latestOverduePayment = Payment::withoutGlobalScopes()
                ->where('subscription_id', $subscription->id)
                ->where('status', 'overdue')
                ->latest('due_date')
                ->first();

            if ($latestOverduePayment && $latestOverduePayment->due_date->addDays(7)->isPast()) {
                $shouldExpire = true;
            }

            // Fallback: if current_period_end + 7 days has passed (webhook may not have arrived)
            if (!$shouldExpire && $subscription->current_period_end && $subscription->current_period_end->addDays(7)->isPast()) {
                $shouldExpire = true;
            }

            if ($shouldExpire) {
                $subscription->update(['status' => 'expired']);
                $expired++;

                $admin = User::withoutGlobalScopes()
                    ->where('company_id', $subscription->company_id)
                    ->where('role', 'admin')
                    ->first();
                $admin?->notify(new SubscriptionCancelledNotification());
            }
        }

        $this->info("Expired {$expired} past_due subscriptions.");

        // 2. Catch active subscriptions whose period ended but no webhook arrived
        //    (card expired, Asaas down, webhook failed, etc.)
        $stuckActive = Subscription::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereNotNull('current_period_end')
            ->where('current_period_end', '<', now()->subDays(3))
            ->get();

        foreach ($stuckActive as $subscription) {
            $subscription->update(['status' => 'past_due']);

            $admin = User::withoutGlobalScopes()
                ->where('company_id', $subscription->company_id)
                ->where('role', 'admin')
                ->first();
            $admin?->notify(new PaymentOverdueNotification());
        }

        if ($stuckActive->count() > 0) {
            $this->info("Moved {$stuckActive->count()} stuck active subscriptions to past_due.");
        }

        // 3. Expire trials that have ended
        $expiredTrialSubs = Subscription::withoutGlobalScopes()
            ->where('status', 'trial')
            ->where('trial_ends_at', '<', now())
            ->get();

        foreach ($expiredTrialSubs as $sub) {
            $sub->update(['status' => 'expired']);

            $admin = User::withoutGlobalScopes()
                ->where('company_id', $sub->company_id)
                ->where('role', 'admin')
                ->first();
            $admin?->notify(new TrialExpiredNotification());
        }

        $this->info("Expired {$expiredTrialSubs->count()} trials.");

        return Command::SUCCESS;
    }
}
