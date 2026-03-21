<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Models\TrainingView;

class PathController extends Controller
{
    public function index()
    {
        $user = auth()->user();

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

        return view('employee.paths.index', compact('paths'));
    }

    public function show(Path $path)
    {
        if (!$path->active || (int) $path->company_id !== (int) auth()->user()->company_id) {
            abort(404);
        }

        $user = auth()->user();
        $path->load(['trainings']);

        $path->trainings->each(function ($training) use ($user) {
            $view = TrainingView::withoutGlobalScope('company')
                ->where('user_id', $user->id)
                ->where('training_id', $training->id)
                ->first();

            $training->user_status = match (true) {
                (bool) $view?->completed_at => 'completed',
                (bool) $view?->started_at => 'in_progress',
                default => 'not_started',
            };
            $training->user_progress = $view?->progress_percent ?? 0;
        });

        $completedCount = $path->trainings->where('user_status', 'completed')->count();
        $totalCount = $path->trainings->count();
        $progressPercent = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        return view('employee.paths.show', compact('path', 'completedCount', 'totalCount', 'progressPercent'));
    }
}
