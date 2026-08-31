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
        Schema::dropIfExists('registry_read_requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('registry_read_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('custodian_id');
            $table->bigInteger('registry_id');
            $table->tinyInteger('status');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();

            $table->index('custodian_id');
            $table->index('registry_id');
            $table->index('status');
        });
    }
};
