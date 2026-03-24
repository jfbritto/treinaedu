<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingAssignment extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['company_id', 'training_id', 'group_id', 'due_date', 'mandatory'];

    protected function casts(): array
    {
        return [
            'due_date'  => 'date',
            'mandatory' => 'boolean',
        ];
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
