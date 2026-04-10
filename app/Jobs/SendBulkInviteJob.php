<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\UserInvitedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class SendBulkInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;
    public int $timeout = 30;

    public function __construct(
        private int $userId,
        private int $adminId,
        private int $companyId,
    ) {
        $this->onQueue('bulk-emails');
        $this->onConnection('redis');
    }

    public function handle(): void
    {
        $user = User::withoutGlobalScopes()->find($this->userId);
        $admin = User::withoutGlobalScopes()->find($this->adminId);
        $company = \App\Models\Company::find($this->companyId);

        if (!$user || !$admin || !$company) {
            return;
        }

        try {
            $token = Password::broker('invites')->createToken($user);
            $user->notify(new UserInvitedNotification($token, $admin, $company));
        } catch (\Throwable $e) {
            Log::warning('Bulk invite email failed', [
                'user_id' => $this->userId,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Re-throw for retry
        }
    }

    public function tags(): array
    {
        return ['bulk-invite', 'company:' . $this->companyId];
    }
}
