<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VideoProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrainingProgressController extends Controller
{
    public function update(Request $request, VideoProgressService $service): JsonResponse
    {
        $request->validate([
            'training_id' => ['required', Rule::exists('trainings', 'id')->where('company_id', $request->user()->company_id)],
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $user = $request->user();

        // Verify user has access (is in an assigned group for this training)
        $hasAccess = $user->groups()
            ->whereHas('assignments', fn ($q) => $q->where('training_id', $request->training_id))
            ->exists();

        if (!$hasAccess) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

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
