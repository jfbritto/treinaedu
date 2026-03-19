<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingRequest;
use App\Models\Group;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingLesson;
use App\Models\TrainingModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $modules = [];

        return view('admin.trainings.create', compact('modules'));
    }

    public function store(StoreTrainingRequest $request)
    {
        DB::transaction(function () use ($request) {
            $companyId = auth()->user()->company_id;

            $training = Training::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes' => 0,
                'duration_minutes_override' => $request->duration_minutes_override,
                'is_sequential' => $request->boolean('is_sequential'),
                'has_quiz' => $request->boolean('has_quiz'),
                'passing_score' => $request->passing_score,
                'created_by' => auth()->id(),
            ]);

            // Create modules and lessons
            foreach ($request->modules as $mi => $moduleData) {
                $module = $training->modules()->create([
                    'title' => $moduleData['title'],
                    'description' => $moduleData['description'] ?? null,
                    'sort_order' => $mi,
                    'is_sequential' => !empty($moduleData['is_sequential']),
                ]);

                $this->createLessons($module, $moduleData['lessons'] ?? [], $companyId, $request, "modules.{$mi}");
            }

            // Training-level quiz
            if ($request->boolean('has_quiz') && $request->has('questions')) {
                $this->createQuiz($training, null, $request->questions, $companyId);
            }

            // Check if any quiz exists (module or training level)
            $hasAnyQuiz = $request->boolean('has_quiz') || $training->quizzes()->exists();
            if ($hasAnyQuiz !== $training->has_quiz) {
                $training->update(['has_quiz' => $hasAnyQuiz]);
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
        $training->load(['modules.lessons', 'quiz.questions.options']);

        return view('admin.trainings.edit', compact('training'));
    }

    public function update(Request $request, Training $training)
    {
        $this->authorizeCompany($training);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes_override' => 'nullable|integer|min:1',
            'is_sequential' => 'boolean',

            'modules' => 'required|array|min:1',
            'modules.*.id' => 'nullable|integer',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.description' => 'nullable|string',
            'modules.*.is_sequential' => 'boolean',
            'modules.*.lessons' => 'required|array|min:1',
            'modules.*.lessons.*.id' => 'nullable|integer',
            'modules.*.lessons.*.title' => 'required|string|max:255',
            'modules.*.lessons.*.type' => 'required|in:video,document,text',
            'modules.*.lessons.*.video_url' => 'required_if:modules.*.lessons.*.type,video|nullable|url',
            'modules.*.lessons.*.duration_minutes' => 'nullable|integer|min:0',
            'modules.*.lessons.*.content' => 'required_if:modules.*.lessons.*.type,text|nullable|string',

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
            $companyId = auth()->user()->company_id;

            $training->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes_override' => $request->duration_minutes_override,
                'is_sequential' => $request->boolean('is_sequential'),
                'active' => $request->boolean('active'),
                'has_quiz' => $request->boolean('has_quiz'),
                'passing_score' => $request->passing_score,
            ]);

            // Sync modules
            $submittedModuleIds = collect($request->modules)
                ->pluck('id')
                ->filter()
                ->toArray();

            // Delete removed modules (cascades to lessons via DB or manual cleanup)
            $removedModules = $training->modules()->whereNotIn('id', $submittedModuleIds)->get();
            foreach ($removedModules as $removedModule) {
                // Clean up document files from lessons
                foreach ($removedModule->lessons as $lesson) {
                    if ($lesson->file_path) {
                        Storage::disk('public')->delete($lesson->file_path);
                    }
                }
                $removedModule->lessons()->delete();
                if ($removedModule->quiz) {
                    $removedModule->quiz->questions->each(fn ($q) => $q->options()->delete());
                    $removedModule->quiz->questions()->delete();
                    $removedModule->quiz->delete();
                }
                $removedModule->delete();
            }

            // Create or update modules
            foreach ($request->modules as $mi => $moduleData) {
                if (!empty($moduleData['id'])) {
                    $module = TrainingModule::find($moduleData['id']);
                    if ($module && (int) $module->training_id === $training->id) {
                        $module->update([
                            'title' => $moduleData['title'],
                            'description' => $moduleData['description'] ?? null,
                            'sort_order' => $mi,
                            'is_sequential' => !empty($moduleData['is_sequential']),
                        ]);

                        $this->syncLessons($module, $moduleData['lessons'] ?? [], $companyId, $request, "modules.{$mi}");
                    }
                } else {
                    $module = $training->modules()->create([
                        'title' => $moduleData['title'],
                        'description' => $moduleData['description'] ?? null,
                        'sort_order' => $mi,
                        'is_sequential' => !empty($moduleData['is_sequential']),
                    ]);

                    $this->createLessons($module, $moduleData['lessons'] ?? [], $companyId, $request, "modules.{$mi}");
                }
            }

            // Training-level quiz
            if ($request->boolean('has_quiz') && $request->has('questions')) {
                // Remove existing training-level quiz
                if ($training->quiz) {
                    $training->quiz->questions->each(fn ($q) => $q->options()->delete());
                    $training->quiz->questions()->delete();
                    $training->quiz->delete();
                }

                $this->createQuiz($training, null, $request->questions, $companyId);
            } elseif (!$request->boolean('has_quiz') && $training->quiz) {
                $training->quiz->questions->each(fn ($q) => $q->options()->delete());
                $training->quiz->questions()->delete();
                $training->quiz->delete();
            }

            // Update has_quiz flag based on any quiz existence
            $hasAnyQuiz = $request->boolean('has_quiz') || $training->quizzes()->whereNotNull('module_id')->exists();
            $training->update(['has_quiz' => $hasAnyQuiz]);
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

    /**
     * Create lessons for a module.
     */
    private function createLessons(TrainingModule $module, array $lessonsData, int $companyId, $request, string $modulePrefix): void
    {
        foreach ($lessonsData as $li => $lessonData) {
            $lessonAttributes = [
                'title' => $lessonData['title'],
                'type' => $lessonData['type'],
                'duration_minutes' => $lessonData['duration_minutes'] ?? null,
                'sort_order' => $li,
            ];

            switch ($lessonData['type']) {
                case 'video':
                    $lessonAttributes['video_url'] = $lessonData['video_url'];
                    $lessonAttributes['video_provider'] = TrainingLesson::detectProvider($lessonData['video_url']);
                    break;

                case 'document':
                    $fileKey = "{$modulePrefix}.lessons.{$li}.file";
                    if ($request->hasFile($fileKey)) {
                        $lessonAttributes['file_path'] = $request->file($fileKey)->store("lessons/{$companyId}", 'public');
                    }
                    break;

                case 'text':
                    $lessonAttributes['content'] = $lessonData['content'] ?? null;
                    break;
            }

            $module->lessons()->create($lessonAttributes);
        }
    }

    /**
     * Sync lessons for an existing module (update, create, delete).
     */
    private function syncLessons(TrainingModule $module, array $lessonsData, int $companyId, $request, string $modulePrefix): void
    {
        $submittedLessonIds = collect($lessonsData)
            ->pluck('id')
            ->filter()
            ->toArray();

        // Delete removed lessons
        $removedLessons = $module->lessons()->whereNotIn('id', $submittedLessonIds)->get();
        foreach ($removedLessons as $removedLesson) {
            if ($removedLesson->file_path) {
                Storage::disk('public')->delete($removedLesson->file_path);
            }
            $removedLesson->delete();
        }

        // Create or update lessons
        foreach ($lessonsData as $li => $lessonData) {
            $lessonAttributes = [
                'title' => $lessonData['title'],
                'type' => $lessonData['type'],
                'duration_minutes' => $lessonData['duration_minutes'] ?? null,
                'sort_order' => $li,
            ];

            switch ($lessonData['type']) {
                case 'video':
                    $lessonAttributes['video_url'] = $lessonData['video_url'];
                    $lessonAttributes['video_provider'] = TrainingLesson::detectProvider($lessonData['video_url']);
                    $lessonAttributes['file_path'] = null;
                    $lessonAttributes['content'] = null;
                    break;

                case 'document':
                    $fileKey = "{$modulePrefix}.lessons.{$li}.file";
                    if ($request->hasFile($fileKey)) {
                        // Delete old file if replacing
                        if (!empty($lessonData['id'])) {
                            $existingLesson = TrainingLesson::find($lessonData['id']);
                            if ($existingLesson && $existingLesson->file_path) {
                                Storage::disk('public')->delete($existingLesson->file_path);
                            }
                        }
                        $lessonAttributes['file_path'] = $request->file($fileKey)->store("lessons/{$companyId}", 'public');
                    }
                    $lessonAttributes['video_url'] = null;
                    $lessonAttributes['video_provider'] = null;
                    $lessonAttributes['content'] = null;
                    break;

                case 'text':
                    $lessonAttributes['content'] = $lessonData['content'] ?? null;
                    $lessonAttributes['video_url'] = null;
                    $lessonAttributes['video_provider'] = null;
                    $lessonAttributes['file_path'] = null;
                    break;
            }

            if (!empty($lessonData['id'])) {
                $lesson = TrainingLesson::find($lessonData['id']);
                if ($lesson && (int) $lesson->module_id === $module->id) {
                    $lesson->update($lessonAttributes);
                }
            } else {
                $module->lessons()->create($lessonAttributes);
            }
        }
    }

    /**
     * Create a quiz for a training (training-level or module-level).
     */
    private function createQuiz(Training $training, ?int $moduleId, array $questions, int $companyId): void
    {
        $quiz = $training->quizzes()->create([
            'company_id' => $companyId,
            'module_id' => $moduleId,
        ]);

        foreach ($questions as $i => $questionData) {
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
}
