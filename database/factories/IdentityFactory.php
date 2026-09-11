<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Identity>
 */
class IdentityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registry_id' => 1,
            'address_1' => '123 Road',
            'address_2' => null,
            'town' => 'Town',
            'county' => 'County',
            'country' => 'United Kingdom',
            'postcode' => 'AB12 3CD',
            'dob' => '1977-07-25',
            'idvt_result' => null,
            'idvt_completed_at' => null,
            'idvt_success' => 0,
            'idvt_identification_number' => null,
            'idvt_document_type' => null,
            'idvt_document_number' => null,
            'idvt_document_country' => null,
            'idvt_document_valid_until' => null,
            'idvt_document_first_name' => null,
            'idvt_document_last_name' => null,
            'idvt_attempt_id' => null,
            'idvt_context_id' => null,
            'idvt_document_dob' => null,
            'idvt_context' => null,
        ];
    }
}
