<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedTinyInteger('cert_size_title')->default(54)->after('cert_subtitle_text');
            $table->unsignedTinyInteger('cert_size_name')->default(34)->after('cert_size_title');
            $table->unsignedTinyInteger('cert_size_training')->default(20)->after('cert_size_name');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['cert_size_title', 'cert_size_name', 'cert_size_training']);
        });
    }
};
