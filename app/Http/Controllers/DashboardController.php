<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('super.dashboard');
        }

        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'instructor' => $this->instructorDashboard(),
            'employee' => $this->employeeDashboard(),
        };
    }

    private function adminDashboard()
    {
        $companyId = auth()->user()->company_id;
        $planUserLimit = auth()->user()->company->subscription?->plan?->max_users;

        $metrics = Cache::remember("dashboard_metrics_{$companyId}", 300, function () use ($companyId) {
            $completed = TrainingView::withoutGlobalScope('company')
                ->where('company_id', $companyId)->whereNotNull('completed_at')->count();
            $pending = TrainingView::withoutGlobalScope('company')
                ->where('company_id', $companyId)->whereNull('completed_at')->count();
            $total = $completed + $pending;

            return [
                'total_employees'     => User::where('company_id', $companyId)->where('role', 'employee')->count(),
                'trainings_created'   => Training::withoutGlobalScope('company')->where('company_id', $companyId)->count(),
                'trainings_completed' => $completed,
                'trainings_pending'   => $pending,
                'certificates_issued' => Certificate::withoutGlobalScope('company')->where('company_id', $companyId)->count(),
                'completion_rate'     => $total > 0 ? round(($completed / $total) * 100, 1) : 0.0,
                'top_trainings'       => Training::withoutGlobalScope('company')
                    ->where('company_id', $companyId)
                    ->withCount([
                        'views',
                        'views as completed_count' => fn($q) => $q->whereNotNull('completed_at'),
                    ])
                    ->orderByDesc('completed_count')
                    ->limit(5)
                    ->get(),
                'recent_employees'    => User::where('company_id', $companyId)
                    ->where('role', 'employee')
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get(),
                'recent_completions'  => TrainingView::withoutGlobalScope('company')
                    ->where('company_id', $companyId)
                    ->whereNotNull('completed_at')
                    ->with(['user', 'training'])
                    ->orderByDesc('completed_at')
                    ->limit(5)
                    ->get(),
            ];
        });

        $metrics['plan_user_limit'] = $planUserLimit;
        return view('admin.dashboard', compact('metrics'));
    }

    private function instructorDashboard()
    {
        $trainings = Training::where('created_by', auth()->id())
            ->withCount([
                'views',
                'views as completed_count' => fn ($q) => $q->whereNotNull('completed_at'),
            ])
            ->latest()
            ->paginate(15);

        return view('instructor.dashboard', compact('trainings'));
    }

    private function employeeDashboard()
    {
        $user = auth()->user();

        $assignedTrainings = $user->assignedTrainings()
            ->with(['views' => fn ($q) => $q->where('user_id', $user->id)])
            ->get();

        $pending = $assignedTrainings->filter(function ($training) {
            $view = $training->views->first();
            return !$view || !$view->completed_at;
        });

        $completed = $assignedTrainings->filter(function ($training) {
            $view = $training->views->first();
            return $view && $view->completed_at;
        });

        $certificates = $user->certificates()->with('training')->latest()->get();

        return view('employee.dashboard', compact('pending', 'completed', 'certificates'));
    }
}
