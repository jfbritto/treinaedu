<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingRequest;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::withCount(['views', 'views as completed_count' => fn ($q) => $q->whereNotNull('completed_at')])
            ->latest()
            ->paginate(15);

        return view('admin.trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('admin.trainings.create');
    }

    public function store(StoreTrainingRequest $request)
    {
        DB::transaction(function () use ($request) {
            $training = Training::create([
                'title' => $request->title,
                'description' => $request->description,
                'video_url' => $request->video_url,
                'video_provider' => Training::detectProvider($request->video_url),
                'duration_minutes' => $request->duration_minutes,
                'has_quiz' => $request->boolean('has_quiz'),
                'passing_score' => $request->passing_score,
                'created_by' => auth()->id(),
            ]);

            if ($request->boolean('has_quiz') && $request->has('questions')) {
                $quiz = $training->quiz()->create([
                    'company_id' => auth()->user()->company_id,
                ]);

                foreach ($request->questions as $i => $questionData) {
                    $question = $quiz->questions()->create([
                        'question' => $questionData['question'],
                        'order' => $i,
                    ]);

                    foreach ($questionData['options'] as $j => $optionData) {
                        $question->options()->create([
                            'option_text' => $optionData['text'],
                            'is_correct' => $j === (int) $questionData['correct'],
                            'order' => $j,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('trainings.index')->with('success', 'Treinamento criado com sucesso.');
    }

    public function show(Training $training)
    {
        $this->authorizeCompany($training);
        $training->load(['quiz.questions.options', 'assignments.group', 'views']);
        return view('admin.trainings.show', compact('training'));
    }

    public function edit(Training $training)
    {
        $this->authorizeCompany($training);
        $training->load(['quiz.questions.options']);
        return view('admin.trainings.edit', compact('training'));
    }

    public function update(Request $request, Training $training)
    {
        $this->authorizeCompany($training);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'active' => 'boolean',
        ]);

        $training->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('trainings.index')->with('success', 'Treinamento atualizado.');
    }

    public function destroy(Training $training)
    {
        $this->authorizeCompany($training);
        $training->delete();
        return redirect()->route('trainings.index')->with('success', 'Treinamento removido.');
    }

    private function authorizeCompany(Training $training): void
    {
        if ((int) $training->company_id !== (int) auth()->user()->company_id) {
            abort(403);
        }
    }
}
