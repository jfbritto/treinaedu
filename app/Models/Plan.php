<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'max_users', 'max_trainings', 'features', 'active', 'company_id'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'active' => 'boolean',
        ];
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isCustom(): bool
    {
        return $this->company_id !== null;
    }

    /**
     * Plans visible to a specific company: public plans + their custom plan (if any).
     */
    public static function visibleTo(int $companyId)
    {
        return static::where('active', true)
            ->where(function ($q) use ($companyId) {
                $q->whereNull('company_id')
                  ->orWhere('company_id', $companyId);
            });
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
