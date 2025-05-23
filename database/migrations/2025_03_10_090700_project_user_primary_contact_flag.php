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
        Schema::table('project_has_users', function (Blueprint $table) {
            $table->tinyInteger('primary_contact')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_has_users', function (Blueprint $table) {
            $table->dropColumn('primary_contact');
        });
    }
};
