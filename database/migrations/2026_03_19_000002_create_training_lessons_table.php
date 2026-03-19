<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('training_modules')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['video', 'document', 'text']);
            $table->string('video_url', 500)->nullable();
            $table->enum('video_provider', ['youtube', 'vimeo'])->nullable();
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
            $table->index(['module_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_lessons');
    }
};
