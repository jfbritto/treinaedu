<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'quiz_id', 'user_id', 'company_id',
        'score', 'passed', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'completed_at' => 'datetime',
            'score' => 'integer',
        ];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
