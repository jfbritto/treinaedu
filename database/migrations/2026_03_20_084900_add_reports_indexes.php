<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('training_views', function (Blueprint $table) {
            $table->index('training_id');
            $table->index('user_id');
            $table->index('completed_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_views', function (Blueprint $table) {
            $table->dropIndex('training_views_training_id_index');
            $table->dropIndex('training_views_user_id_index');
            $table->dropIndex('training_views_completed_at_index');
            $table->dropIndex('training_views_created_at_index');
        });
    }
};
