<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingView;
use App\Services\VideoProgressService;

class TrainingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $assignedTrainings = $user->assignedTrainings()
            ->with(['views' => fn ($q) => $q->where('user_id', $user->id)])
            ->get();

        // Compute mandatory/due_date from the user's group assignments
        $assignedTrainings->each(function ($training) {
            $training->is_mandatory      = $training->assignments->contains('mandatory', true);
            $training->effective_due_date = $training->assignments
                ->whereNotNull('due_date')
                ->sortBy('due_date')
                ->first()?->due_date;
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

        return view('employee.trainings.index', compact('pending', 'completed'));
    }

    public function show(Training $training)
    {
        $user = auth()->user();

        // Verify user has access (is in an assigned group)
        $hasAccess = $user->groups()
            ->whereHas('assignments', fn ($q) => $q->where('training_id', $training->id))
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $view = TrainingView::firstOrCreate(
            ['training_id' => $training->id, 'user_id' => $user->id],
            ['company_id' => $user->company_id, 'started_at' => now()]
        );

        $canComplete = $view->progress_percent >= 90 && !$view->completed_at;
        $isCompleted = (bool) $view->completed_at;

        $quizPassed = false;
        if ($training->has_quiz && $isCompleted) {
            $quizPassed = $user->quizAttempts()
                ->where('quiz_id', $training->quiz?->id)
                ->where('passed', true)
                ->exists();
        }

        $canGenerateCertificate = $isCompleted && (!$training->has_quiz || $quizPassed);
        $existingCertificate = $user->certificates()
            ->where('training_id', $training->id)
            ->first();

        $groupIds = $user->groups()->pluck('groups.id');
        $assignments = TrainingAssignment::where('training_id', $training->id)
            ->whereIn('group_id', $groupIds)
            ->get();
        $isMandatory    = $assignments->contains('mandatory', true);
        $effectiveDue   = $assignments->whereNotNull('due_date')->sortBy('due_date')->first()?->due_date;

        return view('employee.trainings.show', compact(
            'training', 'view', 'canComplete', 'isCompleted',
            'quizPassed', 'canGenerateCertificate', 'existingCertificate',
            'isMandatory', 'effectiveDue'
        ));
    }

    public function complete(Training $training, VideoProgressService $service)
    {
        $result = $service->markCompleted($training->id, auth()->id());

        if (!$result) {
            return back()->with('error', 'Você precisa assistir pelo menos 90% do vídeo.');
        }

        if ($training->has_quiz) {
            return redirect()->route('employee.quiz.show', $training);
        }

        return back()->with('success', 'Treinamento concluído!');
    }
}
