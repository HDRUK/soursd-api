<?php

namespace Tests\Feature;

use KeycloakGuard\ActingAsKeycloakUser;
use App\Models\Sector;
use App\Models\Subsidiary;
use App\Models\OrganisationHasSubsidiary;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\Authorisation;

class SubsidiaryTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;

    public const TEST_URL = '/api/v1/subsidiaries';
    private $testOrg = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->withMiddleware();
        $this->withUsers();

        $this->testOrg = [
            'organisation_name' => 'HEALTH DATA RESEARCH UK',
            'address_1' => '215 Euston Road',
            'address_2' => '',
            'town' => 'Blah',
            'county' => 'London',
            'country' => 'United Kingdom',
            'postcode' => 'NW1 2BE',
            'lead_applicant_organisation_name' => 'Some One',
            'lead_applicant_email' => fake()->email(),
            'organisation_unique_id' => Str::random(40),
            'applicant_names' => 'Some One, Some Two, Some Three',
            'funders_and_sponsors' => 'UKRI, MRC',
            'sub_license_arrangements' => 'N/A',
            'verified' => false,
            'companies_house_no' => '10887014',
            'dsptk_certified' => 1,
            'dsptk_ods_code' => '12345Z',
            'dsptk_expiry_date' => '',
            'dsptk_expiry_evidence' => null,
            'iso_27001_certified' => 0,
            'iso_27001_certification_num' => '',
            'iso_expiry_date' => '',
            'iso_expiry_evidence' => null,
            'ce_certified' => 1,
            'ce_certification_num' => 'A1234',
            'ce_expiry_date' => '',
            'ce_expiry_evidence' => null,
            'ce_plus_certified' => 1,
            'ce_plus_certification_num' => 'B5678',
            'ce_plus_expiry_date' => '',
            'ce_plus_expiry_evidence' => null,
            'sector_id' => fake()->randomElement([0, count(Sector::SECTORS)]),
            'charities' => [
                'registration_id' => '1186569',
            ],
            'ror_id' => '02wnqcb97',
            'smb_status' => false,
            'organisation_size' => 2,
            'website' => 'https://www.website.com/',
        ];
    }

    public function test_the_application_can_create_a_subsidiary(): void
    {
        $payload = [
            "name" => "test sub",
            "address_1" => "Building 1",
            "address_2" => "10 Euston Rd",
            "town" => "London",
            "county" => "None",
            "country" => "United Kingdom",
            "postcode" => "SG5 4PF"
        ];

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . '/organisations/1',
                $payload
            );

        $this->assertDatabaseHas('subsidiaries', $payload);

        $subsidiaryId = Subsidiary::where('name', 'test sub')->first()->id;

        $this->assertDatabaseHas('organisation_has_subsidiaries', [
            'organisation_id' => 1,
            'subsidiary_id' => $subsidiaryId,
        ]);

        $response->assertStatus(201);
    }

    public function test_the_application_can_update_a_subsidiary(): void
    {
        $payload = [
            "name" => "renamed test sub",
            "address_1" => "Building 1",
            "address_2" => "10 Euston Rd",
            "town" => "London",
            "county" => "None",
            "country" => "United Kingdom",
            "postcode" => "SG5 4PF"
        ];

        $response = $this->actingAs($this->admin)
            ->json(
                'PUT',
                self::TEST_URL . '/1/organisations/1',
                $payload
            );

        $this->assertDatabaseHas('subsidiaries', $payload);

        $subsidiaryId = Subsidiary::where('name', 'renamed test sub')->first()->id;

        $response->assertStatus(200);
    }

    public function test_the_application_can_delete_a_subsidiary(): void
    {
        $response = $this->actingAs($this->admin)
            ->json(
                'DELETE',
                self::TEST_URL . '/1/organisations/1'
            );

        $subsidiaryId = Subsidiary::where('id', 1)->first();
        $ohsId = OrganisationHasSubsidiary::where('subsidiary_id', 1)->first();

        $this->assertNull($subsidiaryId);
        $this->assertNull($ohsId);

        $response->assertStatus(200);
    }
}
