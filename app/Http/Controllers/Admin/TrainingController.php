<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingRequest;
use App\Models\Group;
use App\Models\Training;
use App\Models\TrainingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::withCount([
                'views',
                'views as completed_count' => fn ($q) => $q->whereNotNull('completed_at'),
                'assignments',
            ])
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
        $training->load(['assignments.group']);

        $assignedGroupIds = $training->assignments->pluck('group_id');
        $availableGroups  = Group::whereNotIn('id', $assignedGroupIds)->get();

        return view('admin.trainings.show', compact('training', 'availableGroups'));
    }

    public function storeAssignment(Request $request, Training $training)
    {
        $this->authorizeCompany($training);

        $request->validate([
            'group_ids'   => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id,company_id,' . auth()->user()->company_id,
            'due_date'    => 'nullable|date|after:today',
            'mandatory'   => 'boolean',
        ]);

        foreach ($request->group_ids as $groupId) {
            TrainingAssignment::updateOrCreate(
                ['training_id' => $training->id, 'group_id' => $groupId],
                [
                    'company_id' => auth()->user()->company_id,
                    'due_date'   => $request->due_date,
                    'mandatory'  => $request->boolean('mandatory'),
                ]
            );
        }

        return back()->with('success', 'Grupos atribuídos ao treinamento.');
    }

    public function destroyAssignment(Training $training, TrainingAssignment $assignment)
    {
        $this->authorizeCompany($training);
        if ((int) $assignment->training_id !== $training->id) {
            abort(403);
        }
        $assignment->delete();
        return back()->with('success', 'Atribuição removida.');
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
            'video_url' => ['required', 'url', function ($attribute, $value, $fail) {
                if (!str_contains($value, 'youtube.com') && !str_contains($value, 'youtu.be') && !str_contains($value, 'vimeo.com')) {
                    $fail('A URL deve ser do YouTube ou Vimeo.');
                }
            }],
            'duration_minutes' => 'required|integer|min:1',
            'active' => 'boolean',
            'has_quiz' => 'boolean',
            'passing_score' => 'nullable|required_if:has_quiz,1|integer|min:1|max:100',
            'questions' => 'nullable|required_if:has_quiz,1|array|min:1',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.options' => 'required_with:questions|array|min:2',
            'questions.*.options.*.text' => 'required|string|max:500',
            'questions.*.correct' => 'required_with:questions|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $training) {
            $training->update([
                'title' => $request->title,
                'description' => $request->description,
                'video_url' => $request->video_url,
                'video_provider' => Training::detectProvider($request->video_url),
                'duration_minutes' => $request->duration_minutes,
                'active' => $request->boolean('active'),
                'has_quiz' => $request->boolean('has_quiz'),
                'passing_score' => $request->passing_score,
            ]);

            if ($request->boolean('has_quiz') && $request->has('questions')) {
                if ($training->quiz) {
                    $training->quiz->questions->each(fn ($q) => $q->options()->delete());
                    $training->quiz->questions()->delete();
                    $training->quiz->delete();
                }

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
            } elseif (!$request->boolean('has_quiz') && $training->quiz) {
                $training->quiz->questions->each(fn ($q) => $q->options()->delete());
                $training->quiz->questions()->delete();
                $training->quiz->delete();
            }
        });

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
