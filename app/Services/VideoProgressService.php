<?php

namespace App\Services;

use App\Models\TrainingView;

class VideoProgressService
{
    public function updateProgress(int $trainingId, int $userId, int $companyId, int $percent): TrainingView
    {
        $view = TrainingView::updateOrCreate(
            ['training_id' => $trainingId, 'user_id' => $userId],
            ['company_id' => $companyId]
        );

        if (!$view->started_at) {
            $view->started_at = now();
        }

        if ($percent > $view->progress_percent) {
            $view->progress_percent = min($percent, 100);
        }

        $view->save();

        return $view;
    }

    public function markCompleted(int $trainingId, int $userId): ?TrainingView
    {
        $view = TrainingView::where('training_id', $trainingId)
            ->where('user_id', $userId)
            ->first();

        if (!$view || $view->progress_percent < 90) {
            return null;
        }

        $view->update([
            'completed_at' => now(),
            'progress_percent' => 100,
        ]);

        return $view;
    }
}
