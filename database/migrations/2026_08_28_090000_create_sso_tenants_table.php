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
        Schema::create('sso_tenants', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 255);
            $table->string('idp_alias', 255)->nullable()->unique();
            $table->string('metadata_url', 2048)->nullable();
            $table->text('metadata_xml')->nullable();
            $table->string('entity_id', 255)->nullable();
            $table->timestamp('metadata_imported_at')->nullable();
            $table->tinyInteger('enabled')->default(false);
            $table->string('status', 20)->default('pending');
            $table->bigInteger('submitted_by_user_id')->nullable();
            $table->text('rejected_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_tenants');
    }
};
