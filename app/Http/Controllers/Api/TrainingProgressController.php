<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VideoProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingProgressController extends Controller
{
    public function update(Request $request, VideoProgressService $service): JsonResponse
    {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $user = $request->user();
        $view = $service->updateProgress(
            $request->training_id,
            $user->id,
            $user->company_id,
            $request->progress_percent
        );

        return response()->json([
            'progress_percent' => $view->progress_percent,
            'can_complete' => $view->progress_percent >= 90,
        ]);
    }
}
