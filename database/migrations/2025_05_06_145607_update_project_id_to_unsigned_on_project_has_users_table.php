<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// project_id should be unsiggnedBigInteger - causes SQL errors
return new class () extends Migration {
    public function up(): void
    {
        Schema::table('project_has_users', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
        });

        Schema::table('project_has_users', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->change();
        });

        Schema::table('project_has_users', function (Blueprint $table) {
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('project_has_users', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->bigInteger('project_id')->change();
            $table->index('project_id');
        });
    }
};
