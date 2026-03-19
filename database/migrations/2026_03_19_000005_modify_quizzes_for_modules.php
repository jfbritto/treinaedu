<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Drop FK first, then unique index, then re-add FK as regular index
            $table->dropForeign(['training_id']);
            $table->dropUnique(['training_id']);
            $table->foreign('training_id')->references('id')->on('trainings')->cascadeOnDelete();

            // Add module_id
            $table->foreignId('module_id')->nullable()->after('training_id')
                ->constrained('training_modules')->nullOnDelete();

            // Composite unique
            $table->unique(['training_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropUnique(['training_id', 'module_id']);
            $table->dropConstrainedForeignId('module_id');
            $table->dropForeign(['training_id']);
            $table->unique('training_id');
            $table->foreign('training_id')->references('id')->on('trainings')->cascadeOnDelete();
        });
    }
};
