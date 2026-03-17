<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\Training;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Training $training)
    {
        $user = auth()->user();
        $completed = \App\Models\TrainingView::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->exists();

        if (!$completed) {
            abort(403, 'Você precisa concluir o treinamento antes de fazer o quiz.');
        }

        $quiz = $training->quiz()->with('questions.options')->firstOrFail();

        return view('employee.quiz.show', compact('training', 'quiz'));
    }

    public function submit(Request $request, Training $training)
    {
        $user = auth()->user();
        $completed = \App\Models\TrainingView::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->exists();

        if (!$completed) {
            abort(403, 'Você precisa concluir o treinamento antes de fazer o quiz.');
        }

        $quiz = $training->quiz()->with('questions.options')->firstOrFail();

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer',
        ]);

        $totalQuestions = $quiz->questions->count();

        // Ensure all questions were answered
        if (count($request->answers) !== $totalQuestions) {
            return back()->withErrors(['answers' => 'Responda todas as perguntas.']);
        }

        $alreadyPassed = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->where('passed', true)
            ->exists();

        if ($alreadyPassed) {
            return redirect()->route('employee.quiz.show', $training)
                ->with('info', 'Você já foi aprovado neste quiz.');
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
}
