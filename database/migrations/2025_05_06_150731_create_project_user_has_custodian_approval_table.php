<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('project_user_has_custodian_approval', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('custodian_id');

            $table->tinyInteger('approved')->default(0);
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('custodian_id')
                ->references('id')
                ->on('custodians')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user_has_custodian_approval');
    }
};
