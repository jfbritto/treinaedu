<?php

namespace App\Services;

use App\Models\LessonView;
use App\Models\Training;
use App\Models\TrainingLesson;
use App\Models\TrainingView;
use Illuminate\Support\Facades\DB;

class LessonProgressService
{
    public function updateProgress(int $lessonId, int $userId, int $companyId, int $percent): LessonView
    {
        $cappedPercent = min($percent, 100);

        DB::table('lesson_views')->updateOrInsert(
            ['lesson_id' => $lessonId, 'user_id' => $userId],
            [
                'company_id' => $companyId,
                'started_at' => DB::raw('COALESCE(started_at, NOW())'),
                'progress_percent' => DB::raw("GREATEST(COALESCE(progress_percent, 0), {$cappedPercent})"),
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
            ]
        );

        $lessonView = LessonView::withoutGlobalScope('company')
            ->where('lesson_id', $lessonId)
            ->where('user_id', $userId)
            ->first();

        // Auto-complete lesson if threshold reached
        $lesson = TrainingLesson::find($lessonId);
        if ($lesson && !$lessonView->completed_at && $lessonView->progress_percent >= $lesson->completionThreshold()) {
            $lessonView->update(['completed_at' => now()]);
        }

        // Recalculate training progress
        $training = Training::withoutGlobalScopes()->find($lesson->module->training_id);
        $this->recalculateTrainingProgress($training, $userId, $companyId);

        return $lessonView->fresh();
    }

    public function recalculateTrainingProgress(Training $training, int $userId, int $companyId): void
    {
        $lessonIds = $training->lessons()->pluck('training_lessons.id');

        if ($lessonIds->isEmpty()) {
            return;
        }

        // Calculate average across ALL lessons (not just ones with views)
        $viewsMap = LessonView::withoutGlobalScope('company')
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->pluck('progress_percent', 'lesson_id');

        $totalLessons = $lessonIds->count();
        $sumProgress = $lessonIds->sum(fn($id) => $viewsMap[$id] ?? 0);
        $avgProgress = $totalLessons > 0 ? $sumProgress / $totalLessons : 0;

        TrainingView::withoutGlobalScope('company')->updateOrCreate(
            ['training_id' => $training->id, 'user_id' => $userId],
            [
                'company_id' => $companyId,
                'progress_percent' => (int) round($avgProgress),
                'started_at' => DB::raw('COALESCE(started_at, NOW())'),
            ]
        );
    }
}
