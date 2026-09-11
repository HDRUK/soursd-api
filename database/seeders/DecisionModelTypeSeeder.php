<?php

namespace Database\Seeders;

use App\Models\DecisionModelType;
use Illuminate\Database\Seeder;

class DecisionModelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DecisionModelType::truncate();

        foreach (DecisionModelType::ENTITY_TYPES as $type) {
            DecisionModelType::create([
                'name' => $type,
            ]);
        }
    }
}
