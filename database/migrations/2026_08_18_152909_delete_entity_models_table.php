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
        Schema::dropIfExists('entity_models');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('entity_models', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 255);
            $table->string('description', 1024)->nullable();
            $table->bigInteger('entity_model_type_id')->default(0);
            $table->tinyInteger('calls_file')->default(0);
            $table->string('file_path', 1024)->nullable();
            $table->tinyInteger('calls_operation')->default(1);
            $table->text('operation')->nullable();
            $table->tinyInteger('active')->default(1);

            $table->index('entity_model_type_id');
        });
    }
};
