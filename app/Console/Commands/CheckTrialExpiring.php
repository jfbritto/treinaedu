<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\TrialExpiringNotification;
use App\Notifications\TrialLastDayNotification;
use App\Notifications\TrialMiddleNotification;
use Illuminate\Console\Command;

class CheckTrialExpiring extends Command
{
    protected $signature = 'subscriptions:check-trial-expiring';
    protected $description = 'Send trial reminder emails at key milestones (5 days, 2 days, last day)';

    public function handle(): int
    {
        $notified = 0;

        // 5 days left (day 2 of trial) — "explore all features"
        $notified += $this->notifyTrialsExpiringIn(5, TrialMiddleNotification::class);

        // 2 days left (day 5 of trial) — "trial ending soon"
        $notified += $this->notifyTrialsExpiringIn(2, TrialExpiringNotification::class);

        // Last day (day 7 of trial) — "expires today"
        $notified += $this->notifyTrialsExpiringIn(0, TrialLastDayNotification::class);

        $this->info("Notified {$notified} companies about trial status.");
        return Command::SUCCESS;
    }

    private function notifyTrialsExpiringIn(int $days, string $notificationClass): int
    {
        $subscriptions = Subscription::withoutGlobalScopes()
            ->where('status', 'trial')
            ->whereDate('trial_ends_at', now()->addDays($days)->toDateString())
            ->get();

        foreach ($subscriptions as $subscription) {
            $admin = User::withoutGlobalScopes()
                ->where('company_id', $subscription->company_id)
                ->where('role', 'admin')
                ->first();

            if ($admin) {
                if ($notificationClass === TrialMiddleNotification::class) {
                    $admin->notify(new $notificationClass($days));
                } else {
                    $admin->notify(new $notificationClass());
                }
            }
        }

        return $subscriptions->count();
    }
}
