<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use HasFactory, BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id', 'created_by', 'title', 'description',
        'video_url', 'video_provider', 'duration_minutes',
        'passing_score', 'has_quiz', 'active',
    ];

    protected function casts(): array
    {
        return [
            'has_quiz' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
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

    public static function detectProvider(string $url): string
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }

        return 'vimeo';
    }
}
