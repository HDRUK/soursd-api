<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sector;
use App\Models\Subsidiary;
use Illuminate\Support\Str;
use App\Models\Organisation;
use Tests\Traits\Authorisation;
use KeycloakGuard\ActingAsKeycloakUser;
use App\Models\OrganisationHasSubsidiary;

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
            'dsptk_ods_code' => '12345Z',
            'dsptk_expiry_date' => now()->addYears(1),
            'dsptk_expiry_evidence' => null,
            'iso_27001_certification_num' => '',
            'iso_expiry_date' => '',
            'iso_expiry_evidence' => null,
            'ce_certification_num' => 'A1234',
            'ce_expiry_date' => now()->addYears(1),
            'ce_expiry_evidence' => null,
            'ce_plus_certification_num' => 'B5678',
            'ce_plus_expiry_date' => now()->addYears(1),
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
        $name = fake()->company();
        $payload = [
            'name' => $name,
            'address_1' => 'Building 1',
            'address_2' => '10 Euston Rd',
            'town' => 'London',
            'county' => 'None',
            'country' => 'United Kingdom',
            'postcode' => 'SG5 4PF',
            'website' => fake()->url(),
        ];

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . '/organisations/1',
                $payload
            );

        $this->assertDatabaseHas('subsidiaries', $payload);

        $content = $response->decodeResponseJson();
        $newSubsidiaryId = $content['data'];

        $this->assertDatabaseHas('organisation_has_subsidiaries', [
            'organisation_id' => 1,
            'subsidiary_id' => $newSubsidiaryId,
        ]);

        $response->assertStatus(201);
    }

    public function test_the_application_can_create_a_subsidiary_parent(): void
    {
        $name = fake()->company();
        $payload = [
            'name' => $name,
            'address_1' => 'Building 1',
            'address_2' => '10 Euston Rd',
            'town' => 'London',
            'county' => 'None',
            'country' => 'United Kingdom',
            'postcode' => 'SG5 4PF',
            'website' => fake()->url(),
            'is_parent' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . '/organisations/1',
                $payload
            );

        $this->assertDatabaseHas('subsidiaries', $payload);

        $content = $response->decodeResponseJson();
        $newSubsidiaryId = $content['data'];

        $this->assertDatabaseHas('organisation_has_subsidiaries', [
            'organisation_id' => 1,
            'subsidiary_id' => $newSubsidiaryId,
        ]);

        $response->assertStatus(201);

        $isParent = Subsidiary::where('id', $newSubsidiaryId)->first()->is_parent;
        $this->assertEquals(1, $isParent);
    }

    public function test_the_application_cannot_create_a_subsidiary(): void
    {
        $payload = [
            'name' => fake()->company(),
            'address_1' => 'Building 1',
            'address_2' => '10 Euston Rd',
            'town' => 'London',
            'county' => 'None',
            'country' => 'United Kingdom',
            'postcode' => 'SG5 4PF',
            'website' => fake()->url(),
        ];

        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . "/organisations/{$organisationIdTest}",
                $payload
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_update_a_subsidiary(): void
    {
        $payload = [
            'name' => fake()->company(),
            'address_1' => 'Building 1',
            'address_2' => '10 Euston Rd',
            'town' => 'London',
            'county' => 'None',
            'country' => 'United Kingdom',
            'postcode' => 'SG5 4PF',
            'website' => fake()->url(),
        ];

        $response = $this->actingAs($this->admin)
            ->json(
                'PUT',
                self::TEST_URL . '/1/organisations/1',
                $payload
            );

        $this->assertDatabaseHas('subsidiaries', $payload);

        $response->assertStatus(200);
    }

    public function test_the_application_cannot_update_a_subsidiary(): void
    {
        $payload = [
            'name' => fake()->company(),
            'address_1' => 'Building 1',
            'address_2' => '10 Euston Rd',
            'town' => 'London',
            'county' => 'None',
            'country' => 'United Kingdom',
            'postcode' => 'SG5 4PF',
            'website' => fake()->url(),
        ];

        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $latestSubsidiary = Subsidiary::query()->orderBy('id', 'desc')->first();
        $subsidiaryIdTest = $latestSubsidiary ? $latestSubsidiary->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$subsidiaryIdTest}/organisations/{$organisationIdTest}",
                $payload
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
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

    public function test_the_application_cannot_delete_a_subsidiary(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $latestSubsidiary = Subsidiary::query()->orderBy('id', 'desc')->first();
        $subsidiaryIdTest = $latestSubsidiary ? $latestSubsidiary->id + 1 : 1;


        $response = $this->actingAs($this->admin)
            ->json(
                'DELETE',
                self::TEST_URL . "/{$subsidiaryIdTest}/organisations/{$organisationIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }
}
