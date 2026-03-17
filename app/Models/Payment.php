<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'subscription_id', 'asaas_payment_id',
        'amount', 'status', 'payment_method', 'paid_at', 'due_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'due_date' => 'date',
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
