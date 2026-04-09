<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LessonView;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Training;
use App\Models\TrainingLesson;
use App\Models\TrainingModule;
use App\Models\TrainingView;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Request $request, Training $training)
    {
        $user = auth()->user();
        $moduleId = $request->query('module');
        $lessonId = $request->query('lesson');

        if ($lessonId) {
            // Lesson-level quiz
            $lesson = TrainingLesson::whereHas('module', fn ($q) => $q->where('training_id', $training->id))
                ->where('id', $lessonId)
                ->with('quiz.questions.options')
                ->firstOrFail();

            // Verify lesson has a quiz
            if (!$lesson->quiz) {
                abort(404, 'Este quiz de aula não existe.');
            }

            $this->ensureLessonCompleted($user, $lesson);
            $quiz = $lesson->quiz;
        } elseif ($moduleId) {
            // Module-level quiz
            $module = $training->modules()->where('id', $moduleId)->firstOrFail();
            $this->ensureModuleLessonsCompleted($user, $module);
            $quiz = $module->quiz()->with('questions.options')->firstOrFail();
        } else {
            // Training-level quiz: all modules must be completed (lessons + module quizzes passed)
            $this->ensureTrainingCompleted($user, $training);
            $quiz = $training->quiz()->with('questions.options')->firstOrFail();
        }

        return view('employee.quiz.show', compact('training', 'quiz'));
    }

    public function submit(Request $request, Training $training)
    {
        $user = auth()->user();
        $moduleId = $request->query('module');
        $lessonId = $request->query('lesson');

        if ($lessonId) {
            $lesson = TrainingLesson::whereHas('module', fn ($q) => $q->where('training_id', $training->id))
                ->where('id', $lessonId)->firstOrFail();
            $this->ensureLessonCompleted($user, $lesson);
            $quiz = $lesson->quiz()->with('questions.options')->firstOrFail();
        } elseif ($moduleId) {
            $module = $training->modules()->where('id', $moduleId)->firstOrFail();
            $this->ensureModuleLessonsCompleted($user, $module);
            $quiz = $module->quiz()->with('questions.options')->firstOrFail();
        } else {
            $this->ensureTrainingCompleted($user, $training);
            $quiz = $training->quiz()->with('questions.options')->firstOrFail();
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer',
        ]);

        $totalQuestions = $quiz->questions->count();

        // Ensure all questions were answered
        if (count($validated['answers']) !== $totalQuestions) {
            return back()->withErrors(['answers' => 'Responda todas as perguntas.']);
        }

        // Normalize answers to an int-keyed/int-valued map
        $answers = [];
        foreach ($validated['answers'] as $qId => $optId) {
            $answers[(int) $qId] = (int) $optId;
        }

        $alreadyPassed = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->where('passed', true)
            ->exists();

        if ($alreadyPassed) {
            // If already passed, redirect to next lesson
            $nextLessonUrl = null;
            if ($lessonId) {
                $lesson = TrainingLesson::find($lessonId);
                $allLessons = $training->modules->flatMap->lessons;
                $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
                $nextLesson = $currentIndex !== false ? $allLessons->get($currentIndex + 1) : null;
                $nextLessonUrl = $nextLesson
                    ? route('employee.trainings.show', ['training' => $training, 'lesson' => $nextLesson->id, 'autoplay' => 1])
                    : route('employee.trainings.show', $training);
            } else {
                $nextLessonUrl = route('employee.trainings.show', $training);
            }
            return redirect($nextLessonUrl)->with('info', 'Você já foi aprovado neste quiz.');
        }

        $correctAnswers = 0;

        foreach ($quiz->questions as $question) {
            $selectedOptionId = $answers[(int) $question->id] ?? null;
            if ($selectedOptionId === null) {
                continue;
            }

            // Find the selected option among this question's options and check if it's correct.
            // Using first(callback) with explicit int cast avoids any type coercion surprises.
            $selectedOption = $question->options->first(
                fn ($opt) => (int) $opt->id === $selectedOptionId
            );

            if ($selectedOption && $selectedOption->is_correct) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? (int) round(($correctAnswers / $totalQuestions) * 100) : 0;
        $passed = $score >= ($training->passing_score ?? 70);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'score' => $score,
            'passed' => $passed,
            'completed_at' => now(),
        ]);

        // Determine quiz level and next action
        $quizLevel = $lessonId ? 'lesson' : ($moduleId ? 'module' : 'training');

        // Auto-complete the training when the final quiz is passed
        if ($quizLevel === 'training' && $passed) {
            $trainingView = TrainingView::withoutGlobalScope('company')
                ->where('training_id', $training->id)
                ->where('user_id', $user->id)
                ->first();

            if ($trainingView && !$trainingView->completed_at) {
                $trainingView->update(['completed_at' => now(), 'progress_percent' => 100]);
            }
        }

        // Calculate next lesson URL if this was a lesson/module quiz
        $nextLessonUrl = null;
        $nextQuizUrl = null;
        $nextQuizLabel = null;

        if (($quizLevel === 'lesson' || $quizLevel === 'module') && $passed) {
            $allLessons = $training->modules->flatMap->lessons;
            $currentLesson = $lessonId ? TrainingLesson::find($lessonId) : $allLessons->first();
            $currentIndex = $allLessons->search(fn($l) => $l->id === $currentLesson->id);
            $nextLesson = $currentIndex !== false ? $allLessons->get($currentIndex + 1) : null;
            if ($nextLesson) {
                $nextLessonUrl = route('employee.trainings.show', ['training' => $training, 'lesson' => $nextLesson->id, 'autoplay' => 1]);
            }

            // If no next lesson, check if the training has a final quiz that is now available
            if (!$nextLessonUrl && $training->trainingQuiz && $this->canTakeTrainingQuiz($user, $training)) {
                $nextQuizUrl = route('employee.quiz.show', $training);
                $nextQuizLabel = 'Fazer quiz final';
            }
        }

        return view('employee.quiz.result', compact(
            'training', 'attempt', 'score', 'passed', 'moduleId', 'lessonId',
            'quizLevel', 'nextLessonUrl', 'nextQuizUrl', 'nextQuizLabel'
        ));
    }

    /**
     * Check if the user can now take the training-level (final) quiz.
     * Returns false if training has no final quiz, user already passed it,
     * or any prerequisite is missing.
     */
    private function canTakeTrainingQuiz($user, Training $training): bool
    {
        if (!$training->trainingQuiz) {
            return false;
        }

        // Already passed?
        $alreadyPassed = $user->quizAttempts()
            ->where('quiz_id', $training->trainingQuiz->id)
            ->where('passed', true)
            ->exists();
        if ($alreadyPassed) {
            return false;
        }

        $modules = $training->modules()->with(['lessons.quiz', 'quiz'])->get();

        foreach ($modules as $module) {
            $lessonIds = $module->lessons->pluck('id');
            if ($lessonIds->isNotEmpty()) {
                $completedCount = LessonView::withoutGlobalScope('company')
                    ->where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->whereNotNull('completed_at')
                    ->count();
                if ($completedCount < $lessonIds->count()) {
                    return false;
                }
            }

            foreach ($module->lessons as $lesson) {
                if ($lesson->quiz) {
                    $passed = $user->quizAttempts()
                        ->where('quiz_id', $lesson->quiz->id)
                        ->where('passed', true)
                        ->exists();
                    if (!$passed) {
                        return false;
                    }
                }
            }

            if ($module->quiz) {
                $passed = $user->quizAttempts()
                    ->where('quiz_id', $module->quiz->id)
                    ->where('passed', true)
                    ->exists();
                if (!$passed) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Ensure a lesson is completed (progress meets threshold).
     */
    private function ensureLessonCompleted($user, TrainingLesson $lesson): void
    {
        $view = LessonView::withoutGlobalScope('company')
            ->where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->whereNotNull('completed_at')
            ->first();

        if (!$view) {
            abort(403, 'Você precisa concluir a aula antes de fazer o quiz.');
        }
    }

    /**
     * Ensure all lessons in the module are completed.
     */
    private function ensureModuleLessonsCompleted($user, TrainingModule $module): void
    {
        $lessonIds = $module->lessons()->pluck('id');

        if ($lessonIds->isEmpty()) {
            return;
        }

        $completedCount = LessonView::withoutGlobalScope('company')
            ->where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->whereNotNull('completed_at')
            ->count();

        if ($completedCount < $lessonIds->count()) {
            abort(403, 'Você precisa concluir todas as aulas do módulo antes de fazer o quiz.');
        }
    }

    /**
     * Ensure all modules are completed: all lessons done + all module quizzes passed.
     */
    private function ensureTrainingCompleted($user, Training $training): void
    {
        $modules = $training->modules()->with(['lessons.quiz', 'quiz'])->get();

        foreach ($modules as $module) {
            // Check all lessons completed
            $lessonIds = $module->lessons->pluck('id');
            if ($lessonIds->isNotEmpty()) {
                $completedCount = LessonView::withoutGlobalScope('company')
                    ->where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->whereNotNull('completed_at')
                    ->count();

                if ($completedCount < $lessonIds->count()) {
                    abort(403, 'Você precisa concluir todos os módulos antes de fazer o quiz final.');
                }
            }

            // Check lesson quizzes passed (if any)
            foreach ($module->lessons as $lesson) {
                if ($lesson->quiz) {
                    $passed = $user->quizAttempts()
                        ->where('quiz_id', $lesson->quiz->id)
                        ->where('passed', true)
                        ->exists();

                    if (!$passed) {
                        abort(403, 'Você precisa ser aprovado em todos os quizzes de aula antes de fazer o quiz final.');
                    }
                }
            }

            // Check module quiz passed (if exists)
            if ($module->quiz) {
                $passed = $user->quizAttempts()
                    ->where('quiz_id', $module->quiz->id)
                    ->where('passed', true)
                    ->exists();

                if (!$passed) {
                    abort(403, 'Você precisa ser aprovado em todos os quizzes de módulo antes de fazer o quiz final.');
                }
            }
        }
    }
}
