<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->unsignedInteger('duration_minutes_override')->nullable()->after('duration_minutes');
            $table->boolean('is_sequential')->default(true)->after('active');
        });

        // Make video_url and video_provider nullable using raw SQL (avoids doctrine/dbal dependency)
        \DB::statement('ALTER TABLE trainings MODIFY video_url VARCHAR(500) NULL');
        \DB::statement("ALTER TABLE trainings MODIFY video_provider ENUM('youtube','vimeo') NULL");
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes_override', 'is_sequential']);
        });
        \DB::statement("ALTER TABLE trainings MODIFY video_url VARCHAR(500) NOT NULL");
        \DB::statement("ALTER TABLE trainings MODIFY video_provider ENUM('youtube','vimeo') NOT NULL");
    }
};
