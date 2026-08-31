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
        Schema::table('custodian_model_configs', function (Blueprint $table) {
            $table->renameColumn('entity_model_id', 'decision_model_id');
        });
        Schema::table('custodian_model_configs', function (Blueprint $table) {
            $table->index('decision_model_id');
            $table->dropIndex(['entity_model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custodian_model_configs', function (Blueprint $table) {
            $table->renameColumn('decision_model_id', 'entity_model_id');
        });

        Schema::table('custodian_model_configs', function (Blueprint $table) {
            $table->index('entity_model_id');
            $table->dropIndex(['decision_model_id']);
        });

    }
};
