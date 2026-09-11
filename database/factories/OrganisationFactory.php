<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Organisation;
use App\Models\Charity;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation>
 */
class OrganisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organisation_name' => 'HEALTH DATA RESEARCH UK',
            'address_1' => '215 Euston Road',
            'address_2' => null,
            'town' => 'London',
            'county' => 'London',
            'country' => 'United Kingdom',
            'postcode' => 'NW1 2BE',
            'lead_applicant_organisation_name' => fake()->name(),
            'lead_applicant_email' => fake()->email(),
            'organisation_unique_id' => Str::random(40),
            'applicant_names' => fake()->name(),
            'funders_and_sponsors' => fake()->company(),
            'sub_license_arrangements' => fake()->sentence(5),
            'verified' => true,
            'companies_house_no' => '10887014',
            'sector_id' => fake()->randomElement([0, count(Sector::SECTORS)]),
            'dsptk_ods_code' => '012345',
            'dsptk_expiry_date' => Carbon::now()->addYears(1),
            'iso_27001_certification_num' => Str::random(12),
            'iso_expiry_date' => Carbon::now()->addYears(1),
            'ce_certification_num' => '012345',
            'ce_expiry_date' => Carbon::now()->addYears(1),
            'ce_plus_certification_num' => '012345',
            'ce_plus_expiry_date' => Carbon::now()->addYears(1),
            'ror_id' => '02wnqcb97',
            'website' => 'https://www.website.com/',
            'smb_status' => false,
            'organisation_size' => 2,
            'unclaimed' => false,
        ];
    }

    public function withCharity()
    {
        return $this->afterCreating(function (Organisation $organisation) {
            $charity = Charity::factory()->create();
            $organisation->charities()->attach($charity->id);
        });
    }
}
