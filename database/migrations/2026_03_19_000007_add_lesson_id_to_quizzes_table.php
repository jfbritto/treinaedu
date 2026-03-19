<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Collect existing foreign keys to handle idempotency after partial failures
        $foreignKeys = collect(DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quizzes' AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        ))->pluck('CONSTRAINT_NAME');

        // Step 1: Drop all foreign keys that depend on the unique index
        Schema::table('quizzes', function (Blueprint $table) use ($foreignKeys) {
            if ($foreignKeys->contains('quizzes_training_id_foreign')) {
                $table->dropForeign(['training_id']);
            }
            if ($foreignKeys->contains('quizzes_module_id_foreign')) {
                $table->dropForeign(['module_id']);
            }
        });

        // Step 2: Drop old unique, add lesson_id column + FK, re-add FKs, new unique
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropUnique(['training_id', 'module_id']);

            $table->foreignId('lesson_id')->nullable()->after('module_id')
                ->constrained('training_lessons')->nullOnDelete();

            $table->foreign('training_id')->references('id')->on('trainings')->cascadeOnDelete();
            $table->foreign('module_id')->references('id')->on('training_modules')->nullOnDelete();

            $table->unique(['training_id', 'module_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropUnique(['training_id', 'module_id', 'lesson_id']);
            $table->dropForeign(['training_id']);
            $table->dropForeign(['module_id']);
            $table->dropConstrainedForeignId('lesson_id');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreign('training_id')->references('id')->on('trainings')->cascadeOnDelete();
            $table->foreign('module_id')->references('id')->on('training_modules')->nullOnDelete();
            $table->unique(['training_id', 'module_id']);
        });
    }
};
