<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('organisation_has_custodian_approvals', function (Blueprint $table) {
            $table->tinyInteger('approved')->default(0)->after('custodian_id');
            $table->text('comment')->nullable()->after('approved');
            $table->timestamp('created_at')->useCurrent()->after('comment');
        });
    }

    public function down(): void
    {
        Schema::table('organisation_has_custodian_approvals', function (Blueprint $table) {
            $table->dropColumn(['approved', 'comment', 'created_at']);
        });
    }
};
