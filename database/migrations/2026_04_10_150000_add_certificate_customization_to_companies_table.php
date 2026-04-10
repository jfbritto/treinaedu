<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('cert_border_style', 20)->default('classic')->after('cert_signer_signature_path');
            $table->string('cert_title_text', 100)->default('CERTIFICADO')->after('cert_border_style');
            $table->string('cert_subtitle_text', 100)->default('de Conclusão')->after('cert_title_text');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['cert_border_style', 'cert_title_text', 'cert_subtitle_text']);
        });
    }
};
