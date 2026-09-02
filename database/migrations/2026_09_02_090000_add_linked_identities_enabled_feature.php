<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('features')->updateOrInsert(
            ['name' => 'LinkedIdentitiesEnabled', 'scope' => '__laravel_null'],
            [
                'value' => 'true',
                'description' => 'Enable linking external identity providers (ORCID, GitHub) to a researcher account.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('features')->where('name', 'LinkedIdentitiesEnabled')->delete();
    }
};
