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

        $metrics = Cache::remember("dashboard_metrics_{$companyId}", 300, function () {
            return [
                'total_employees' => User::where('company_id', auth()->user()->company_id)
                    ->where('role', 'employee')->count(),
                'trainings_created' => Training::count(),
                'trainings_completed' => TrainingView::whereNotNull('completed_at')->count(),
                'trainings_pending' => TrainingView::whereNull('completed_at')->count(),
                'certificates_issued' => Certificate::count(),
            ];
        });

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
