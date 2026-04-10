<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'asaas_customer_id',
        'logo_path', 'primary_color', 'secondary_color',
        'cert_signer_name', 'cert_signer_role', 'cert_signer_registry', 'cert_signer_signature_path',
        'cert_border_style', 'cert_title_text', 'cert_subtitle_text',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function isOnTrial(): bool
    {
        return $this->subscription
            && $this->subscription->status === 'trial'
            && $this->subscription->trial_ends_at
            && $this->subscription->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(): bool
    {
        if (!$this->subscription) {
            return false;
        }

        // Active or grace period
        if ($this->isOnTrial() || in_array($this->subscription->status, ['active', 'past_due'])) {
            return true;
        }

        // Cancelled but still within paid period
        if ($this->subscription->status === 'cancelled'
            && $this->subscription->current_period_end
            && $this->subscription->current_period_end->isFuture()) {
            return true;
        }

        return false;
    }

    public function hasReachedUserLimit(): bool
    {
        // Trial has no limits
        if ($this->isOnTrial()) {
            return false;
        }

        $plan = $this->subscription?->plan;

        if (!$plan || !$plan->max_users) {
            return false;
        }

        return $this->users()->whereIn('role', ['instructor', 'employee'])->count()
            >= $plan->max_users;
    }

    public function hasReachedTrainingLimit(): bool
    {
        // Trial has no limits
        if ($this->isOnTrial()) {
            return false;
        }

        $plan = $this->subscription?->plan;

        if (!$plan || !$plan->max_trainings) {
            return false;
        }

        return Training::withoutGlobalScopes()
            ->where('company_id', $this->id)
            ->count() >= $plan->max_trainings;
    }

    public function planHasFeature(string $feature): bool
    {
        // During trial, all features are unlocked so the user can try everything
        if ($this->isOnTrial()) {
            return true;
        }

        return $this->subscription?->plan?->hasFeature($feature) ?? false;
    }
}
