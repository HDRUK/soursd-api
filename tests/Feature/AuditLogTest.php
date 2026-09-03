<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sector;
use Illuminate\Support\Str;
use App\Models\Organisation;
use Tests\Traits\Authorisation;
use Spatie\Activitylog\Models\Activity;

class AuditLogTest extends TestCase
{
    use Authorisation;

    public const TEST_URL = '/api/v1/';
    private $testOrg = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->enableObservers();
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
            'iso_expiry_date' => now()->addYears(1),
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
            'system_approved' => true,
            'sro_profile_uri' => 'https://myprofile.something',
        ];
    }

    public function test_it_creates_audit_logs_when_a_user_is_created_with_success()
    {
        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . 'users',
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                    'provider' => fake()->word(),
                    'provider_sub' => Str::random(10),
                    'public_opt_in' => fake()->randomElement([0, 1]),
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $userId = $response->decodeResponseJson()['data'];
        $this->assertGreaterThan(0, $userId);

        $activity = Activity::where('subject_id', $userId)
            ->where('subject_type', User::class)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('created', $activity->event);
        $this->assertEquals($userId, $activity->subject_id);
    }

    public function test_it_get_audit_logs_by_user_with_success()
    {
        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . 'users',
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                    'provider' => fake()->word(),
                    'provider_sub' => Str::random(10),
                    'public_opt_in' => fake()->randomElement([0, 1]),
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $userId = $response->decodeResponseJson()['data'];
        $this->assertGreaterThan(0, $userId);

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "users/{$userId}/history"
            );

        $response->assertStatus(200);
    }

    public function test_it_get_audit_logs_by_user_with_no_success()
    {
        $latestUserId = User::query()->orderBy('id', 'desc')->first();
        $userIdTest = $latestUserId->id + 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "users/{$userIdTest}/history"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_it_creates_audit_logs_when_an_organisation_is_created_with_success()
    {
        $this->testOrg['departments'] = [1, 2, 3];

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . "organisations",
                $this->testOrg
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $organisationId = $response->decodeResponseJson()['data'];

        $activity = Activity::where('subject_id', $organisationId)
            ->where('subject_type', Organisation::class)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('created', $activity->event);
        $this->assertEquals($organisationId, $activity->subject_id);
    }

    public function test_it_get_audit_logs_by_organisation_with_success()
    {
        $this->testOrg['departments'] = [1, 2, 3];

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . "organisations",
                $this->testOrg
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $organisationId = $response->decodeResponseJson()['data'];

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "organisations/{$organisationId}/history"
            );

        $response->assertStatus(200);
    }

    public function test_it_get_audit_logs_by_organisation_with_no_success()
    {
        $latestOrganisationId = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisationId->id + 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "organisations/{$organisationIdTest}/history"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }
}
