<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingView;

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

        // Check user has access via group assignment
        $assignedTrainingIds = $user->assignedTrainings()->pluck('trainings.id');
        if (!$assignedTrainingIds->contains($training->id)) {
            abort(403);
        }

        // Load modules with lessons
        $training->load([
            'modules.lessons',
            'modules.quiz',
            'trainingQuiz',
        ]);

        $lessonIds = $training->lessons->pluck('id');

        $lessonViews = \App\Models\LessonView::withoutGlobalScope('company')
            ->where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');

        // Determine current lesson (from query param or first incomplete)
        $currentLessonId = request('lesson');
        $currentLesson = null;

        if ($currentLessonId) {
            $currentLesson = \App\Models\TrainingLesson::find($currentLessonId);
        }

        if (!$currentLesson) {
            // Find first incomplete lesson
            foreach ($training->modules as $module) {
                foreach ($module->lessons as $lesson) {
                    $view = $lessonViews[$lesson->id] ?? null;
                    if (!$view || !$view->completed_at) {
                        $currentLesson = $lesson;
                        break 2;
                    }
                }
            }
            // If all complete, show last lesson
            if (!$currentLesson) {
                $currentLesson = $training->lessons->last();
            }
        }

        // Create/update lesson view for current lesson
        if ($currentLesson) {
            \App\Models\LessonView::withoutGlobalScope('company')->firstOrCreate(
                ['lesson_id' => $currentLesson->id, 'user_id' => $user->id],
                ['company_id' => $user->company_id, 'started_at' => now()]
            );
            // Refresh to include newly created
            $lessonViews = \App\Models\LessonView::withoutGlobalScope('company')
                ->where('user_id', $user->id)
                ->whereIn('lesson_id', $lessonIds)
                ->get()
                ->keyBy('lesson_id');
        }

        // Calculate unlock states
        $unlockStates = $this->calculateUnlockStates($training, $lessonViews, $user);

        // Training view
        $trainingView = \App\Models\TrainingView::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->first();

        // Overall progress
        $trainingProgress = $trainingView?->progress_percent ?? 0;

        // Check completion conditions
        $allLessonsComplete = $lessonIds->every(fn($id) => isset($lessonViews[$id]) && $lessonViews[$id]->completed_at);

        // Module quiz states
        $allModuleQuizzesPassed = true;
        foreach ($training->modules as $module) {
            if ($module->quiz) {
                $passed = $user->quizAttempts()
                    ->where('quiz_id', $module->quiz->id)
                    ->where('passed', true)->exists();
                if (!$passed) $allModuleQuizzesPassed = false;
            }
        }

        // Training quiz state
        $trainingQuizPassed = true;
        if ($training->trainingQuiz) {
            $trainingQuizPassed = $user->quizAttempts()
                ->where('quiz_id', $training->trainingQuiz->id)
                ->where('passed', true)->exists();
        }

        $isCompleted = $trainingView?->completed_at !== null;
        $canComplete = $allLessonsComplete && $allModuleQuizzesPassed && $trainingQuizPassed && !$isCompleted;

        $canGenerateCertificate = $isCompleted && $allModuleQuizzesPassed && $trainingQuizPassed;

        // Get assignment info for due date
        $assignment = $training->assignments()
            ->whereIn('group_id', $user->groups()->pluck('groups.id'))
            ->first();

        return view('employee.trainings.show', compact(
            'training', 'currentLesson', 'lessonViews', 'unlockStates',
            'trainingView', 'trainingProgress', 'canComplete', 'isCompleted',
            'canGenerateCertificate', 'assignment'
        ));
    }

    public function complete(Training $training)
    {
        $user = auth()->user();

        // Verify all lessons completed
        $lessonIds = $training->lessons()->pluck('training_lessons.id');
        $completedCount = \App\Models\LessonView::withoutGlobalScope('company')
            ->where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->whereNotNull('completed_at')
            ->count();

        if ($completedCount < $lessonIds->count()) {
            return back()->with('error', 'Complete todas as aulas antes de finalizar o treinamento.');
        }

        // Verify all quizzes passed
        $quizzes = $training->quizzes;
        foreach ($quizzes as $quiz) {
            $passed = $user->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists();
            if (!$passed) {
                return back()->with('error', 'Complete todos os quizzes antes de finalizar.');
            }
        }

        $trainingView = \App\Models\TrainingView::where('training_id', $training->id)
            ->where('user_id', $user->id)->first();

        if ($trainingView && !$trainingView->completed_at) {
            $trainingView->update(['completed_at' => now(), 'progress_percent' => 100]);
        }

        return redirect()->route('employee.trainings.show', $training)
            ->with('success', 'Treinamento concluído com sucesso!');
    }

    private function calculateUnlockStates(Training $training, $lessonViews, $user): array
    {
        $states = ['modules' => [], 'lessons' => []];
        $prevModuleComplete = true;

        foreach ($training->modules as $module) {
            $moduleUnlocked = !$training->is_sequential || $prevModuleComplete;
            $states['modules'][$module->id] = $moduleUnlocked;

            $prevLessonComplete = true;
            $allLessonsComplete = true;

            foreach ($module->lessons as $lesson) {
                $view = $lessonViews[$lesson->id] ?? null;
                $lessonComplete = $view && $view->completed_at;

                $lessonUnlocked = $moduleUnlocked && (!$module->is_sequential || $prevLessonComplete);
                $states['lessons'][$lesson->id] = $lessonUnlocked;

                if (!$lessonComplete) $allLessonsComplete = false;
                $prevLessonComplete = $lessonComplete;
            }

            $quizPassed = !$module->quiz || $user->quizAttempts()
                ->where('quiz_id', $module->quiz->id)
                ->where('passed', true)->exists();
            $prevModuleComplete = $allLessonsComplete && $quizPassed;
        }

        return $states;
    }
}
