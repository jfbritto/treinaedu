<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TrainingView extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'training_id', 'user_id',
        'progress_percent', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Aggregation Scopes and Methods

    /**
     * Scope to apply global filters
     * Filters by training_id, group_id, status, date range
     */
    public function scopeWithFilters($query, array $filters = [])
    {
        if (isset($filters['training_id'])) {
            $query->where('training_id', $filters['training_id']);
        }

        if (isset($filters['group_id'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->whereHas('groups', function ($g) use ($filters) {
                    $g->where('group_id', $filters['group_id']);
                });
            });
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($filters['status'] === 'pending') {
                $query->whereNull('completed_at');
            }
        }

        if (isset($filters['started_from'])) {
            $query->where('started_at', '>=', $filters['started_from']);
        }

        if (isset($filters['started_until'])) {
            $query->where('started_at', '<=', $filters['started_until']);
        }

        if (isset($filters['completed_from'])) {
            $query->where('completed_at', '>=', $filters['completed_from']);
        }

        if (isset($filters['completed_until'])) {
            $query->where('completed_at', '<=', $filters['completed_until']);
        }

        return $query;
    }

    /**
     * Get global statistics for all training views
     */
    public static function getGlobalStats(array $filters = [])
    {
        $query = self::withFilters($filters);

        $total = $query->count();
        $completed = (clone $query)->whereNotNull('completed_at')->count();
        $pending = (clone $query)->whereNull('completed_at')->count();
        $avgProgress = (clone $query)->avg('progress_percent') ?? 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'completed_percent' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'avg_progress' => round($avgProgress, 2),
        ];
    }

    /**
     * Get analysis aggregated by group
     */
    public static function getGroupAnalysis(array $filters = [])
    {
        return self::withFilters($filters)
            ->select(
                'group_id',
                \DB::raw('groups.name as group_name'),
                \DB::raw('COUNT(*) as total'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NULL THEN 1 END) as pending'),
                \DB::raw('ROUND(AVG(progress_percent), 2) as avg_progress')
            )
            ->join('users', 'training_views.user_id', '=', 'users.id')
            ->join('group_user', 'users.id', '=', 'group_user.user_id')
            ->join('groups', 'group_user.group_id', '=', 'groups.id')
            ->groupBy('group_user.group_id')
            ->get()
            ->map(function ($item) {
                return [
                    'group_id' => $item->group_id,
                    'group_name' => $item->group_name ?? 'Unknown',
                    'total' => $item->total,
                    'completed' => $item->completed,
                    'pending' => $item->pending,
                    'completed_percent' => $item->total > 0 ? round(($item->completed / $item->total) * 100, 2) : 0,
                    'avg_progress' => $item->avg_progress,
                ];
            })
            ->toArray();
    }

    /**
     * Get analysis aggregated by instructor
     */
    public static function getInstructorAnalysis(array $filters = [])
    {
        return self::withFilters($filters)
            ->select(
                'trainings.created_by as instructor_id',
                \DB::raw('COUNT(*) as total'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NULL THEN 1 END) as pending'),
                \DB::raw('ROUND(AVG(progress_percent), 2) as avg_progress')
            )
            ->join('trainings', 'training_views.training_id', '=', 'trainings.id')
            ->groupBy('trainings.created_by')
            ->get()
            ->map(function ($item) {
                $instructor = \App\Models\User::find($item->instructor_id);
                return [
                    'instructor_id' => $item->instructor_id,
                    'instructor_name' => $instructor?->name ?? 'Unknown',
                    'total' => $item->total,
                    'completed' => $item->completed,
                    'pending' => $item->pending,
                    'completed_percent' => $item->total > 0 ? round(($item->completed / $item->total) * 100, 2) : 0,
                    'avg_progress' => $item->avg_progress,
                ];
            })
            ->toArray();
    }

    /**
     * Get analysis aggregated by period
     */
    public static function getPeriodAnalysis(array $filters = [], $period = 'month')
    {
        return self::withFilters($filters)
            ->select(
                \DB::raw('DATE(created_at) as period'),
                \DB::raw('COUNT(*) as total'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NULL THEN 1 END) as pending'),
                \DB::raw('ROUND(AVG(progress_percent), 2) as avg_progress')
            )
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->orderBy('period', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->period,
                    'total' => $item->total,
                    'completed' => $item->completed,
                    'pending' => $item->pending,
                    'completed_percent' => $item->total > 0 ? round(($item->completed / $item->total) * 100, 2) : 0,
                    'avg_progress' => $item->avg_progress,
                ];
            })
            ->toArray();
    }
}
