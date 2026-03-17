<?php

namespace App\Services;

use App\Models\TrainingView;

class VideoProgressService
{
    public function updateProgress(int $trainingId, int $userId, int $companyId, int $percent): TrainingView
    {
        $cappedPercent = min($percent, 100);

        \DB::table('training_views')->updateOrInsert(
            ['training_id' => $trainingId, 'user_id' => $userId],
            [
                'company_id' => $companyId,
                'started_at' => \DB::raw('COALESCE(started_at, NOW())'),
                'progress_percent' => \DB::raw("GREATEST(COALESCE(progress_percent, 0), {$cappedPercent})"),
                'updated_at' => now(),
                'created_at' => \DB::raw('COALESCE(created_at, NOW())'),
            ]
        );

        return TrainingView::withoutGlobalScope('company')
            ->where('training_id', $trainingId)
            ->where('user_id', $userId)
            ->first();
    }

    public function markCompleted(int $trainingId, int $userId): ?TrainingView
    {
        $view = TrainingView::where('training_id', $trainingId)
            ->where('user_id', $userId)
            ->first();

        if (!$view || $view->progress_percent < 90 || $view->completed_at) {
            return null;
        }

        $view->update([
            'completed_at' => now(),
            'progress_percent' => 100,
        ]);

        return $view;
    }
}
