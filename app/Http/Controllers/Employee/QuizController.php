<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LessonView;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Training;
use App\Models\TrainingModule;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Request $request, Training $training)
    {
        $user = auth()->user();
        $moduleId = $request->query('module');

        if ($moduleId) {
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

        if ($moduleId) {
            $module = $training->modules()->where('id', $moduleId)->firstOrFail();
            $this->ensureModuleLessonsCompleted($user, $module);
            $quiz = $module->quiz()->with('questions.options')->firstOrFail();
        } else {
            $this->ensureTrainingCompleted($user, $training);
            $quiz = $training->quiz()->with('questions.options')->firstOrFail();
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer',
        ]);

        $totalQuestions = $quiz->questions->count();

        // Ensure all questions were answered
        if (count($request->answers) !== $totalQuestions) {
            return back()->withErrors(['answers' => 'Responda todas as perguntas.']);
        }

        $alreadyPassed = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->where('passed', true)
            ->exists();

        if ($alreadyPassed) {
            return redirect()->route('employee.quiz.show', array_filter([
                'training' => $training->id,
                'module' => $moduleId,
            ]))->with('info', 'Você já foi aprovado neste quiz.');
        }

        $correctAnswers = 0;

        foreach ($quiz->questions as $question) {
            $selectedOptionId = $request->answers[$question->id] ?? null;
            $correctOption = $question->options->where('is_correct', true)->first();

            if ($correctOption && (int) $selectedOptionId === $correctOption->id) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        $passed = $score >= ($training->passing_score ?? 70);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'score' => $score,
            'passed' => $passed,
            'completed_at' => now(),
        ]);

        return view('employee.quiz.result', compact('training', 'attempt', 'score', 'passed'));
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
        $modules = $training->modules()->with(['lessons', 'quiz'])->get();

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
