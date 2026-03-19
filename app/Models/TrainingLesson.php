<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingLesson extends Model
{
    protected $fillable = [
        'module_id', 'title', 'type', 'video_url', 'video_provider',
        'content', 'file_path', 'duration_minutes', 'sort_order',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(TrainingModule::class, 'module_id');
    }

    public function lessonViews(): HasMany
    {
        return $this->hasMany(LessonView::class, 'lesson_id');
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function completionThreshold(): int
    {
        return $this->isVideo() ? 90 : 100;
    }

    public static function detectProvider(string $url): string
    {
        return Training::detectProvider($url);
    }
}
