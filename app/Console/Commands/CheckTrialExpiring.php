<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\TrialExpiringNotification;
use Illuminate\Console\Command;

class CheckTrialExpiring extends Command
{
    protected $signature = 'subscriptions:check-trial-expiring';
    protected $description = 'Notify companies whose trial expires in 2 days';

    public function handle(): int
    {
        $expiringSubscriptions = Subscription::withoutGlobalScopes()
            ->where('status', 'trial')
            ->whereDate('trial_ends_at', now()->addDays(2)->toDateString())
            ->get();

        foreach ($expiringSubscriptions as $subscription) {
            $admin = User::withoutGlobalScopes()
                ->where('company_id', $subscription->company_id)
                ->where('role', 'admin')
                ->first();

            $admin?->notify(new TrialExpiringNotification());
        }

        $this->info("Notified {$expiringSubscriptions->count()} companies.");
        return Command::SUCCESS;
    }
}
