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
        Schema::rename('entity_model_types', 'decision_model_types');

        Schema::table('decision_models', function (Blueprint $table) {
            $table->renameColumn('entity_model_type_id', 'decision_model_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('decision_model_types', 'entity_model_types');

        Schema::table('decision_models', function (Blueprint $table) {
            $table->renameColumn('decision_model_type_id', 'entity_model_type_id');
        });
    }
};
