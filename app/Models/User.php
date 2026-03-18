<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'company_id', 'role', 'active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    public function trainingViews()
    {
        return $this->hasMany(TrainingView::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function assignedTrainings()
    {
        $groupIds = $this->groups()->pluck('groups.id');

        return Training::whereHas('assignments', function ($query) use ($groupIds) {
            $query->whereIn('group_id', $groupIds);
        })
        ->where('active', true)
        ->with(['assignments' => function ($query) use ($groupIds) {
            $query->whereIn('group_id', $groupIds)->select(['training_id', 'due_date', 'mandatory']);
        }]);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
