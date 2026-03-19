<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $trainings = DB::table('trainings')->whereNotNull('video_url')->get();

        foreach ($trainings as $training) {
            $moduleId = DB::table('training_modules')->insertGetId([
                'training_id' => $training->id,
                'title' => $training->title,
                'sort_order' => 1,
                'is_sequential' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $lessonId = DB::table('training_lessons')->insertGetId([
                'module_id' => $moduleId,
                'title' => $training->title,
                'type' => 'video',
                'video_url' => $training->video_url,
                'video_provider' => $training->video_provider,
                'duration_minutes' => $training->duration_minutes,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $views = DB::table('training_views')
                ->where('training_id', $training->id)
                ->get();

            foreach ($views as $view) {
                DB::table('lesson_views')->insert([
                    'lesson_id' => $lessonId,
                    'user_id' => $view->user_id,
                    'company_id' => $view->company_id,
                    'progress_percent' => $view->progress_percent,
                    'started_at' => $view->started_at,
                    'completed_at' => $view->completed_at,
                    'created_at' => $view->created_at,
                    'updated_at' => $view->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('lesson_views')->truncate();
        DB::table('training_lessons')->truncate();
        DB::table('training_modules')->truncate();
    }
};
