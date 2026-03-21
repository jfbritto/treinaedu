<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use HasFactory, BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id', 'created_by', 'title', 'description',
        'video_url', 'video_provider', 'duration_minutes',
        'passing_score', 'has_quiz', 'active',
        'duration_minutes_override', 'is_sequential',
    ];

    protected function casts(): array
    {
        return [
            'has_quiz' => 'boolean',
            'active' => 'boolean',
            'is_sequential' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class)->whereNull('module_id')->whereNull('lesson_id');
    }

    public function trainingQuiz(): HasOne
    {
        return $this->hasOne(Quiz::class)->whereNull('module_id')->whereNull('lesson_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(TrainingModule::class)->orderBy('sort_order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            TrainingLesson::class,
            TrainingModule::class,
            'training_id',
            'module_id',
        );
    }

    public function calculatedDuration(): int
    {
        return $this->duration_minutes_override
            ?? $this->lessons()->sum('duration_minutes');
    }

    public function views()
    {
        return $this->hasMany(TrainingView::class);
    }

    public function assignments()
    {
        return $this->hasMany(TrainingAssignment::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'training_assignments')
            ->withPivot('due_date')
            ->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function completionRate(): float
    {
        $totalAssigned = $this->assignments()
            ->join('group_user', 'training_assignments.group_id', '=', 'group_user.group_id')
            ->distinct('group_user.user_id')
            ->count('group_user.user_id');

        if ($totalAssigned === 0) {
            return 0;
        }

        $completed = $this->views()->whereNotNull('completed_at')->count();
        return round(($completed / $totalAssigned) * 100, 1);
    }

    public function totalLessons(): int
    {
        return $this->lessons()->count();
    }

    public function userCompletedLessons(int $userId): int
    {
        return $this->lessons()
            ->whereHas('lessonViews', fn ($q) =>
                $q->where('user_id', $userId)->whereNotNull('completed_at')
            )
            ->count();
    }

    public static function detectProvider(string $url): string
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }

        if (str_contains($url, 'vimeo.com')) {
            return 'vimeo';
        }

        throw new \InvalidArgumentException("Provider de vídeo não suportado para a URL: {$url}");
    }
}
