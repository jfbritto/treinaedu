<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LessonView;
use App\Models\TrainingLesson;
use App\Models\TrainingView;
use App\Services\LessonProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function __invoke(Request $request, LessonProgressService $service): JsonResponse
    {
        $validated = $request->validate([
            'lesson_id' => 'required|integer|exists:training_lessons,id',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $user = auth()->user();
        $lesson = TrainingLesson::find($validated['lesson_id']);

        if (!$lesson) {
            abort(404);
        }

        // Verify lesson belongs to user's company via training
        $module = $lesson->module;
        $training = $module ? \App\Models\Training::withoutGlobalScopes()->find($module->training_id) : null;

        if (!$training) {
            abort(404);
        }
        if ($training->company_id !== $user->company_id) {
            abort(403);
        }

        $lessonView = $service->updateProgress(
            $validated['lesson_id'],
            $user->id,
            $user->company_id,
            $validated['progress_percent']
        );

        // Calculate module progress
        $moduleLessons = $lesson->module->lessons;
        $moduleLessonIds = $moduleLessons->pluck('id');
        $moduleViews = LessonView::withoutGlobalScope('company')
            ->where('user_id', $user->id)
            ->whereIn('lesson_id', $moduleLessonIds)
            ->get()
            ->keyBy('lesson_id');
        $moduleProgress = (int) round($moduleLessons->avg(fn ($l) => $moduleViews[$l->id]->progress_percent ?? 0));

        // Get training progress
        $trainingView = TrainingView::withoutGlobalScope('company')
            ->where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'progress_percent' => $lessonView->progress_percent,
            'lesson_completed' => $lessonView->completed_at !== null,
            'module_progress' => $moduleProgress,
            'training_progress' => $trainingView?->progress_percent ?? 0,
        ]);
    }
}
