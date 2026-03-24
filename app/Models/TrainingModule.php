<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrainingModule extends Model
{
    use HasFactory;
    protected $fillable = [
        'training_id', 'title', 'description', 'sort_order', 'is_sequential',
    ];

    protected function casts(): array
    {
        return [
            'is_sequential' => 'boolean',
        ];
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(TrainingLesson::class, 'module_id')->orderBy('sort_order');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class, 'module_id')->whereNull('lesson_id');
    }
}
