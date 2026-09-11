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
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn([
                'dsptk_certified',
                'iso_27001_certified',
                'ce_certified',
                'ce_plus_certified',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->tinyInteger('dsptk_certified')->default(0);
            $table->tinyInteger('iso_27001_certified')->default(0);
            $table->tinyInteger('ce_certified')->default(0);
            $table->tinyInteger('ce_plus_certified')->default(0);
        });
    }
};
