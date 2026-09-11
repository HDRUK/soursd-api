<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_identities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('provider');
            $table->string('provider_user_id');
            $table->string('provider_username')->nullable();
            $table->json('claims')->nullable();
            $table->timestamp('linked_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'provider'], 'user_identities_user_provider_unique');
            $table->index('user_id', 'user_identities_user_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_identities');
    }
};
