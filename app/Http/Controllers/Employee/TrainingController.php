<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\TrainingView;
use App\Services\VideoProgressService;

class TrainingController extends Controller
{
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

        return view('employee.trainings.show', compact(
            'training', 'view', 'canComplete', 'isCompleted',
            'quizPassed', 'canGenerateCertificate', 'existingCertificate'
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
