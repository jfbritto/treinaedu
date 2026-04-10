<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\TrialExpiredNotification;
use Illuminate\Console\Command;

class ExpireOverdueSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire-overdue';
    protected $description = 'Expire subscriptions that have been past_due for more than 7 days, and expire ended trials';

    public function handle(): int
    {
        // Expire past_due subscriptions with overdue payment older than 7 days
        $pastDueSubscriptions = Subscription::withoutGlobalScopes()
            ->where('status', 'past_due')
            ->get();

        $expired = 0;

        foreach ($pastDueSubscriptions as $subscription) {
            $latestOverduePayment = Payment::withoutGlobalScopes()
                ->where('subscription_id', $subscription->id)
                ->where('status', 'overdue')
                ->latest('due_date')
                ->first();

            if ($latestOverduePayment && $latestOverduePayment->due_date->addDays(7)->isPast()) {
                $subscription->update(['status' => 'expired']);
                $expired++;
            }
        }

        $this->info("Expired {$expired} past_due subscriptions.");

        // Also expire trials that have ended — notify admin before updating
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
