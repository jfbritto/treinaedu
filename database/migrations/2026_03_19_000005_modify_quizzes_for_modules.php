<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropUnique(['training_id']);
            $table->foreignId('module_id')->nullable()->after('training_id')
                ->constrained('training_modules')->nullOnDelete();
            $table->unique(['training_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropUnique(['training_id', 'module_id']);
            $table->dropConstrainedForeignId('module_id');
            $table->unique('training_id');
        });
    }
};
