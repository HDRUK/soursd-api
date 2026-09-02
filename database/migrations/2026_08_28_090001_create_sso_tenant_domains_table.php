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
        Schema::create('sso_tenant_domains', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('sso_tenant_id');
            $table->string('domain', 255)->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_tenant_domains');
    }
};
