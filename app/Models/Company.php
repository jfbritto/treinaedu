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

        return $this->isOnTrial()
            || in_array($this->subscription->status, ['active', 'past_due']);
    }

    public function hasReachedUserLimit(): bool
    {
        $plan = $this->subscription?->plan;

        if (!$plan || !$plan->max_users) {
            return false;
        }

        return $this->users()->whereIn('role', ['instructor', 'employee'])->count()
            >= $plan->max_users;
    }

    public function hasReachedTrainingLimit(): bool
    {
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
        return $this->subscription?->plan?->hasFeature($feature) ?? false;
    }
}
