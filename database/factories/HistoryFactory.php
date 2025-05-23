<?php

namespace Database\Factories;

use Carbon\Carbon;
use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\History>
 */
class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data = [
            'affiliation_id' => 1,
            'endorsement_id' => 1,
            'infringement_id' => 1,
            'project_id' => 1,
            'access_key_id' => 1,
            'custodian_identifier' => 'ABC1234DEF-56789-0',
            'history_entry_ts' => Carbon::now()->toDateTimeString(),
        ];

        $ledgerHash = Hash::make(json_encode($data));

        return [
            'affiliation_id' => 1,
            'endorsement_id' => 1,
            'infringement_id' => 1,
            'project_id' => 1,
            'access_key_id' => 1,
            'custodian_identifier' => 'ABC1234DEF-56789-0',
            'ledger_hash' => $ledgerHash,
        ];
    }
}
