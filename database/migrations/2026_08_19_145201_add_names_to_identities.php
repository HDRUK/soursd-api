<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('identities', function (Blueprint $table) {
            $table->string('idvt_document_first_name')->nullable()->after('idvt_document_valid_until');
            $table->string('idvt_document_last_name')->nullable()->after('idvt_document_first_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('identities', function (Blueprint $table) {
            $table->dropColumn(['idvt_document_first_name', 'idvt_document_last_name']);
        });
    }
};
