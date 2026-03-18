<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::where('created_by', auth()->id())
            ->latest()
            ->paginate(15);

        return view('instructor.trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('instructor.trainings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'video_url'        => ['required', 'url', function ($attribute, $value, $fail) {
                if (!preg_match('/(?:youtube\.com|youtu\.be|vimeo\.com)/', $value)) {
                    $fail('Use um link do YouTube ou Vimeo.');
                }
            }],
            'duration_minutes' => 'required|integer|min:1',
            'has_quiz'         => 'boolean',
            'passing_score'    => 'nullable|required_if:has_quiz,1|integer|min:1|max:100',
            'questions'        => 'nullable|required_if:has_quiz,1|array|min:1',
            'questions.*.question' => 'required_if:has_quiz,1|string',
            'questions.*.options'  => 'required_if:has_quiz,1|array|min:2',
            'questions.*.options.*.text' => 'required_if:has_quiz,1|string',
        ]);

        DB::transaction(function () use ($request) {
            $training = Training::create([
                'title'            => $request->title,
                'description'      => $request->description,
                'video_url'        => $request->video_url,
                'video_provider'   => \App\Models\Training::detectProvider($request->video_url),
                'duration_minutes' => $request->duration_minutes,
                'active'           => $request->boolean('active'),
                'has_quiz'         => $request->boolean('has_quiz'),
                'passing_score'    => $request->boolean('has_quiz') ? $request->passing_score : null,
                'created_by'       => auth()->id(),
                'company_id'       => auth()->user()->company_id,
            ]);

            if ($request->boolean('has_quiz') && $request->has('questions')) {
                $quiz = $training->quiz()->create(['company_id' => auth()->user()->company_id]);
                foreach ($request->questions as $i => $questionData) {
                    $question = $quiz->questions()->create([
                        'question'   => $questionData['question'],
                        'order'      => $i + 1,
                        'company_id' => auth()->user()->company_id,
                    ]);
                    foreach ($questionData['options'] as $j => $optionData) {
                        $question->options()->create([
                            'option_text' => $optionData['text'],
                            'is_correct'  => (int) ($questionData['correct'] ?? 0) === $j,
                            'order'       => $j + 1,
                            'company_id'  => auth()->user()->company_id,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('instructor.trainings.index')
            ->with('success', 'Treinamento criado com sucesso!');
    }

    public function edit(Training $training)
    {
        abort_if($training->created_by !== auth()->id(), 403);

        return view('instructor.trainings.edit', compact('training'));
    }

    public function update(Request $request, Training $training)
    {
        abort_if($training->created_by !== auth()->id(), 403);

        $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'video_url'        => ['required', 'url', function ($attribute, $value, $fail) {
                if (!preg_match('/(?:youtube\.com|youtu\.be|vimeo\.com)/', $value)) {
                    $fail('Use um link do YouTube ou Vimeo.');
                }
            }],
            'duration_minutes' => 'required|integer|min:1',
            'has_quiz'         => 'boolean',
            'passing_score'    => 'nullable|required_if:has_quiz,1|integer|min:1|max:100',
            'questions'        => 'nullable|required_if:has_quiz,1|array|min:1',
            'questions.*.question' => 'required_if:has_quiz,1|string',
            'questions.*.options'  => 'required_if:has_quiz,1|array|min:2',
            'questions.*.options.*.text' => 'required_if:has_quiz,1|string',
        ]);

        DB::transaction(function () use ($request, $training) {
            $training->update([
                'title'            => $request->title,
                'description'      => $request->description,
                'video_url'        => $request->video_url,
                'video_provider'   => \App\Models\Training::detectProvider($request->video_url),
                'duration_minutes' => $request->duration_minutes,
                'active'           => $request->boolean('active'),
                'has_quiz'         => $request->boolean('has_quiz'),
                'passing_score'    => $request->boolean('has_quiz') ? $request->passing_score : null,
            ]);

            if ($request->boolean('has_quiz') && $request->has('questions')) {
                if ($training->quiz) {
                    $training->quiz->questions->each(fn ($q) => $q->options()->delete());
                    $training->quiz->questions()->delete();
                    $training->quiz->delete();
                }
                $quiz = $training->quiz()->create(['company_id' => auth()->user()->company_id]);
                foreach ($request->questions as $i => $questionData) {
                    $question = $quiz->questions()->create([
                        'question'   => $questionData['question'],
                        'order'      => $i + 1,
                        'company_id' => auth()->user()->company_id,
                    ]);
                    foreach ($questionData['options'] as $j => $optionData) {
                        $question->options()->create([
                            'option_text' => $optionData['text'],
                            'is_correct'  => (int) ($questionData['correct'] ?? 0) === $j,
                            'order'       => $j + 1,
                            'company_id'  => auth()->user()->company_id,
                        ]);
                    }
                }
            } elseif (!$request->boolean('has_quiz') && $training->quiz) {
                $training->quiz->questions->each(fn ($q) => $q->options()->delete());
                $training->quiz->questions()->delete();
                $training->quiz->delete();
            }
        });

        return redirect()->route('instructor.trainings.index')
            ->with('success', 'Treinamento atualizado!');
    }

    public function destroy(Training $training)
    {
        abort_if($training->created_by !== auth()->id(), 403);

        $training->delete();

        return redirect()->route('instructor.trainings.index')
            ->with('success', 'Treinamento removido!');
    }
}
