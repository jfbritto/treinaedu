<?php

use App\Http\Controllers\Api\LessonProgressController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/lesson-progress', LessonProgressController::class);
});
