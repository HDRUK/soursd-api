<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('affiliations', function (Blueprint $table) {
            $table->unsignedBigInteger('verdict_user_id')->nullable()->after('registry_id');
            $table->dateTime('verdict_date_actioned')->nullable()->after('verdict_user_id');
            $table->tinyInteger('verdict_outcome')->nullable()->after('verdict_date_actioned');

            $table->foreign('verdict_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliations', function (Blueprint $table) {
            $table->dropForeign(['verdict_user_id']);
            $table->dropColumn(['verdict_user_id', 'verdict_date_actioned', 'verdict_outcome']);
        });
    }
};
