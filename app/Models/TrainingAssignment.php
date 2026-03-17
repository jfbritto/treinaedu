<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TrainingAssignment extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'training_id', 'group_id', 'due_date'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
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
