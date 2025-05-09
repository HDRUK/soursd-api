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
        Schema::create('organisation_has_custodian_permissions', function (Blueprint $table) {
            $table->integer('organisation_id');
            $table->integer('permission_id');
            $table->integer('custodian_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_has_custodian_permissions');
    }
};
