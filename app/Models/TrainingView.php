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
    public function scopeGroupAnalysis($query, array $filters = [])
    {
        return self::withFilters($filters)
            ->select(
                'users_groups.group_id',
                \DB::raw('COUNT(*) as total'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NULL THEN 1 END) as pending'),
                \DB::raw('ROUND(AVG(progress_percent), 2) as avg_progress')
            )
            ->join('users', 'training_views.user_id', '=', 'users.id')
            ->join('users_groups', 'users.id', '=', 'users_groups.user_id')
            ->groupBy('users_groups.group_id')
            ->get()
            ->map(function ($item) {
                return [
                    'group_id' => $item->group_id,
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
                'trainings.instructor_id',
                \DB::raw('COUNT(*) as total'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NULL THEN 1 END) as pending'),
                \DB::raw('ROUND(AVG(progress_percent), 2) as avg_progress')
            )
            ->join('trainings', 'training_views.training_id', '=', 'trainings.id')
            ->groupBy('trainings.instructor_id')
            ->get()
            ->map(function ($item) {
                return [
                    'instructor_id' => $item->instructor_id,
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
        $dateFormat = match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-W%W',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };

        return self::withFilters($filters)
            ->select(
                \DB::raw('DATE(started_at) as period'),
                \DB::raw('COUNT(*) as total'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed'),
                \DB::raw('COUNT(CASE WHEN completed_at IS NULL THEN 1 END) as pending'),
                \DB::raw('ROUND(AVG(progress_percent), 2) as avg_progress')
            )
            ->whereNotNull('started_at')
            ->groupBy(\DB::raw('DATE(started_at)'))
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
