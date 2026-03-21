<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Path;
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
                    ->get()
                    ->map(fn($t) => [
                        'title'           => $t->title,
                        'completed_count' => $t->completed_count,
                        'completion_rate' => $t->completionRate(),
                    ])
                    ->all(),
                'recent_employees'    => User::where('company_id', $companyId)
                    ->where('role', 'employee')
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get()
                    ->map(fn($u) => [
                        'name'       => $u->name,
                        'email'      => $u->email,
                        'created_at' => $u->created_at,
                    ])
                    ->all(),
                'recent_completions'  => TrainingView::withoutGlobalScope('company')
                    ->where('company_id', $companyId)
                    ->whereNotNull('completed_at')
                    ->with(['user', 'training'])
                    ->orderByDesc('completed_at')
                    ->limit(5)
                    ->get()
                    ->map(fn($v) => [
                        'user_name'      => $v->user?->name,
                        'training_title' => $v->training?->title,
                        'completed_at'   => $v->completed_at,
                    ])
                    ->all(),
            ];
        });

        // plan_user_limit is intentionally set outside the cache: auth() context must not be serialized.
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

        $assignedTrainings->each(function ($training) use ($user) {
            $training->is_mandatory      = $training->assignments->contains('mandatory', true);
            $training->effective_due_date = $training->assignments
                ->whereNotNull('due_date')
                ->sortBy('due_date')
                ->first()?->due_date;
            // Adicionar dados de progresso
            $training->total_lessons = $training->totalLessons();
            $training->completed_lessons = $training->userCompletedLessons($user->id);
        });

        $pending = $assignedTrainings
            ->filter(fn ($t) => !$t->views->first()?->completed_at)
            ->sortBy([
                fn ($a, $b) => $b->is_mandatory <=> $a->is_mandatory,
                fn ($a, $b) => match(true) {
                    $a->effective_due_date && $b->effective_due_date => $a->effective_due_date <=> $b->effective_due_date,
                    (bool) $a->effective_due_date => -1,
                    (bool) $b->effective_due_date => 1,
                    default => 0,
                },
            ])
            ->values();

        $completed = $assignedTrainings->filter(fn ($t) => (bool) $t->views->first()?->completed_at)->values();

        $certificates = $user->certificates()->with('training')->latest()->get();

        // Dados para gráfico de progressão
        $chartData = [
            'labels' => $this->getCertificateMonthLabels($certificates),
            'data' => $this->getCertificateMonthCounts($certificates),
        ];

        // Trilhas com progresso
        $paths = Path::where('active', true)
            ->withCount('trainings')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($path) use ($user) {
                $trainingIds = $path->trainings()->pluck('trainings.id');
                $completed = TrainingView::withoutGlobalScope('company')
                    ->where('user_id', $user->id)
                    ->whereIn('training_id', $trainingIds)
                    ->whereNotNull('completed_at')
                    ->count();
                $path->completed_trainings = $completed;
                $path->progress_percent = $trainingIds->count() > 0
                    ? round(($completed / $trainingIds->count()) * 100)
                    : 0;
                return $path;
            });

        return view('employee.dashboard', compact('pending', 'completed', 'certificates', 'chartData', 'paths'));
    }

    private function getCertificateMonthLabels($certificates)
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('M/y'));
        }
        return $months->toArray();
    }

    private function getCertificateMonthCounts($certificates)
    {
        $counts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = $certificates->filter(fn ($c) =>
                $c->generated_at->format('Y-m') === $month->format('Y-m')
            )->count();
            $counts[] = $count;
        }
        return $counts;
    }
}
