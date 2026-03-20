<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\TrainingView;
use App\Models\User;
use Carbon\Carbon;

class EngagementController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        // Get filter parameters (default to current month)
        if (request('date_from')) {
            $dateFrom = Carbon::createFromFormat('Y-m-d', request('date_from'));
        } else {
            $dateFrom = Carbon::now()->startOfMonth();
        }

        if (request('date_to')) {
            $dateTo = Carbon::createFromFormat('Y-m-d', request('date_to'))->endOfDay();
        } else {
            $dateTo = Carbon::now()->endOfMonth();
        }

        // Top 10 most engaged users (employees only)
        $topUsers = User::where('company_id', $companyId)
            ->where('active', true)
            ->where('role', 'employee')
            ->with(['trainingViews' => function ($q) use ($dateFrom, $dateTo) {
                $q->select('id', 'user_id', 'completed_at', 'progress_percent', 'created_at');
                if ($dateFrom) {
                    $q->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $q->where('created_at', '<=', $dateTo);
                }
            }])
            ->get()
            ->map(function ($user) {
                $views = $user->trainingViews;
                $completed = $views->where('completed_at', '!=', null)->count();
                $total = $views->count();
                $avgProgress = $total > 0 ? $views->avg('progress_percent') : 0;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_trainings' => $total,
                    'completed' => $completed,
                    'pending' => $total - $completed,
                    'avg_progress' => round($avgProgress, 2),
                    'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                    'days_since_start' => $views->count() > 0 ? $views->min('created_at')->diffInDays(now()) : 0,
                ];
            })
            ->sortByDesc('completion_rate')
            ->take(10)
            ->values();

        // Group rankings (employees only)
        $groupRankings = Group::where('company_id', $companyId)
            ->with(['users' => function ($q) {
                $q->where('active', true)
                  ->where('role', 'employee');
            }])
            ->get()
            ->map(function ($group) use ($dateFrom, $dateTo) {
                $userIds = $group->users->pluck('id');
                $query = TrainingView::whereIn('user_id', $userIds);

                if ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }

                $views = $query->get();
                $completed = $views->where('completed_at', '!=', null)->count();
                $total = $views->count();

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'total_trainings' => $total,
                    'completed' => $completed,
                    'pending' => $total - $completed,
                    'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                    'members' => $group->users->count(),
                    'avg_per_member' => $group->users->count() > 0 ? round($total / $group->users->count(), 1) : 0,
                ];
            })
            ->sortByDesc('completion_rate')
            ->values();

        // Users at risk (inactive for 30+ days, employees only)
        $atRiskUsers = User::where('company_id', $companyId)
            ->where('active', true)
            ->where('role', 'employee')
            ->with(['trainingViews' => function ($q) use ($dateFrom, $dateTo) {
                $q->orderBy('created_at', 'desc')->limit(1);
                if ($dateFrom) {
                    $q->where('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $q->where('created_at', '<=', $dateTo);
                }
            }])
            ->get()
            ->filter(function ($user) {
                if ($user->trainingViews->isEmpty()) {
                    return true; // Never started
                }
                $lastActivity = $user->trainingViews->first()->created_at;
                return $lastActivity->diffInDays(now()) >= 30;
            })
            ->map(function ($user) {
                $lastActivity = $user->trainingViews->isEmpty()
                    ? null
                    : $user->trainingViews->first()->created_at;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_activity' => $lastActivity,
                    'days_inactive' => $lastActivity ? $lastActivity->diffInDays(now()) : 999,
                ];
            })
            ->sortByDesc('days_inactive')
            ->take(10)
            ->values();

        // Overall stats (employees only)
        $allViewsQuery = TrainingView::whereHas('user', function ($q) use ($companyId) {
            $q->where('company_id', $companyId)
              ->where('active', true)
              ->where('role', 'employee');
        });

        if ($dateFrom) {
            $allViewsQuery->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $allViewsQuery->where('created_at', '<=', $dateTo);
        }

        $allViews = $allViewsQuery->get();

        // Get users with activity in the period
        $usersWithActivityInPeriod = User::where('company_id', $companyId)
            ->where('active', true)
            ->where('role', 'employee')
            ->whereHas('trainingViews', function ($q) use ($dateFrom, $dateTo) {
                $q->where('created_at', '>=', $dateFrom)
                  ->where('created_at', '<=', $dateTo);
            })
            ->pluck('id')
            ->toArray();

        $stats = [
            'total_users' => User::where('company_id', $companyId)
                ->where('active', true)
                ->where('role', 'employee')
                ->count(),
            'users_engaged' => count($usersWithActivityInPeriod),
            'total_trainings_assigned' => $allViews->count(),
            'total_completed' => $allViews->where('completed_at', '!=', null)->count(),
            'overall_completion_rate' => $allViews->count() > 0
                ? round(($allViews->where('completed_at', '!=', null)->count() / $allViews->count()) * 100, 2)
                : 0,
            'avg_progress' => round($allViews->avg('progress_percent'), 2),
        ];

        return view('admin.engagement.index', compact(
            'topUsers',
            'groupRankings',
            'atRiskUsers',
            'stats',
            'dateFrom',
            'dateTo'
        ));
    }
}
