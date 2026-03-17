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
        $quiz = $training->quiz()->with('questions.options')->firstOrFail();

        return view('employee.quiz.show', compact('training', 'quiz'));
    }

    public function submit(Request $request, Training $training)
    {
        $quiz = $training->quiz()->with('questions.options')->firstOrFail();

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer',
        ]);

        $totalQuestions = $quiz->questions->count();
        $correctAnswers = 0;

        foreach ($quiz->questions as $question) {
            $selectedOptionId = $request->answers[$question->id] ?? null;
            $correctOption = $question->options->where('is_correct', true)->first();

            if ($correctOption && $selectedOptionId == $correctOption->id) {
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
