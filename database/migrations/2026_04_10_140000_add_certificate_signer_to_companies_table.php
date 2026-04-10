<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('cert_signer_name')->nullable()->after('secondary_color');
            $table->string('cert_signer_role')->nullable()->after('cert_signer_name');
            $table->string('cert_signer_registry')->nullable()->after('cert_signer_role');
            $table->string('cert_signer_signature_path')->nullable()->after('cert_signer_registry');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'cert_signer_name',
                'cert_signer_role',
                'cert_signer_registry',
                'cert_signer_signature_path',
            ]);
        });
    }
};
