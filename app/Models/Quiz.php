<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use BelongsToCompany;

    protected $fillable = ['training_id', 'company_id', 'module_id', 'lesson_id'];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function module()
    {
        return $this->belongsTo(TrainingModule::class, 'module_id');
    }

    public function lesson()
    {
        return $this->belongsTo(TrainingLesson::class, 'lesson_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
