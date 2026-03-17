<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_assignments')
            ->withPivot('due_date')
            ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(TrainingAssignment::class);
    }
}
