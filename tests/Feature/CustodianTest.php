<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sector;
use App\Models\Custodian;
use Illuminate\Support\Str;
use App\Models\Organisation;
use App\Models\DecisionModel;
use App\Models\PendingInvite;
use Tests\Traits\Authorisation;
use App\Models\ProjectHasCustodian;
use App\Models\CustodianModelConfig;
use App\Models\ProjectHasUser;
use Illuminate\Support\Facades\Queue;
use KeycloakGuard\ActingAsKeycloakUser;

class CustodianTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;

    public const TEST_URL = '/api/v1/custodians';

    private $projectUniqueId = '';
    private $organisationUniqueId = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->withMiddleware();
        $this->withUsers();
        $this->projectUniqueId = Str::random(40);
        $this->organisationUniqueId = Str::random(40);
    }

    public function test_custodian_can_list_custodians(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL
            );

        $response->assertStatus(200);
        $this->assertArrayHaskey('data', $response);
    }

    public function test_non_custodians_cannot_list_custodians(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL
            );

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'forbidden',
            ]);
    }

    public function test_the_users_cannot_see_a_custodian(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'forbidden',
            ]);
    }

    public function test_custodian_can_see_details(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_custodian_cannot_see_details(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '/' . $custodianIdTest
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_invite_a_custodian(): void
    {

        Queue::assertNothingPushed();

        $responseCreate = $this->actingAs($this->admin)
                ->json(
                    'POST',
                    self::TEST_URL,
                    [
                        'contact_email' => 'test.custodian.invite@test.com',
                        'name' => 'Test Custodian',
                        'enabled' => false,
                    ]
                );

        $decoded = $responseCreate->decodeResponseJson();
        $custodianId = $decoded['data'];

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . '/' . $custodianId . '/invite/',
            );

        $response->assertStatus(201);

        $invites = PendingInvite::all();

        $this->assertTrue(count($invites) === 1);
    }


    public function test_the_application_cannot_invite_a_user_for_a_custodian_they_dont_administer(): void
    {

        Queue::assertNothingPushed();

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL . '/' . 1 . '/invite/',
            );

        $response->assertStatus(403);

        $invites = PendingInvite::all();

        $this->assertTrue(count($invites) === 0);
    }

    public function test_users_cannot_create_custodians(): void
    {
        foreach ([$this->user, $this->custodian_admin, $this->organisation_admin] as $user) {
            $response = $this->actingAs($user)
                ->json(
                    'POST',
                    self::TEST_URL,
                    [
                        'name' => 'Test Custodian',
                        'contact_email' => 'test@test.com',
                        'enabled' => true,
                        'idvt_required' => false,
                    ]
                );
            $response->assertStatus(403);
        }
    }

    public function test_an_admin_can_create_custodians(): void
    {
        $user = $this->user;
        $user->update([
            'user_group' => User::GROUP_ADMINS
        ]);

        $response = $this->actingAs($user)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'name' => 'Test Custodian',
                    'contact_email' => 'test@test.com',
                    'enabled' => true,
                    'idvt_required' => false,
                ]
            );
        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_can_create_projects(): void
    {
        $user = $this->user;
        $user->update([
            'user_group' => User::GROUP_ADMINS
        ]);
        $response = $this->actingAs($user)
            ->json(
                'POST',
                self::TEST_URL . '/1/projects',
                [
                    'title' => 'Test project',
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $conf = ProjectHasCustodian::where([
            'custodian_id' => 1,
            'project_id' => $response['data']
        ])->get()->toArray();

        $this->assertTrue(count($conf) === 1);
    }

    public function test_the_application_adds_decision_models_to_newly_created_custodians(): void
    {
        $this->enableObservers();
        CustodianModelConfig::truncate();

        $user = $this->user;
        $user->update([
            'user_group' => User::GROUP_ADMINS
        ]);

        $response = $this->actingAs($user)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'name' => 'Test Custodian',
                    'contact_email' => 'test@test.com',
                    'enabled' => true,
                    'idvt_required' => false,
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson();

        $entities = DecisionModel::all();
        $conf = CustodianModelConfig::where([
            'custodian_id' => $content['data'],
        ])->get()->toArray();

        $this->assertTrue(count($conf) === count($entities));
    }

    public function test_the_application_creates_action_log(): void
    {
        $this->enableObservers();

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'name' => 'Test Custodian',
                    'contact_email' => 'test@test.com',
                    'enabled' => true,
                    'idvt_required' => false,
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson();
        $this->assertGreaterThan(0, $content['data']);


        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . '/' . $content['data'] . '/action_log'
            );

        $response->assertStatus(200);
        $responseData = $response['data'];
        $actionLog = collect($responseData)
            ->firstWhere('action', Custodian::ACTION_COMPLETE_CONFIGURATION);

        $this->assertNull($actionLog['completed_at']);
    }

    public function test_the_application_can_update_custodian_they_own(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/2',
                [
                    'name' => 'Updated Custodian',
                    'enabled' => false,
                    'idvt_required' => true,
                ]
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];

        $this->assertEquals($content['name'], 'Updated Custodian');
        $this->assertEquals($content['enabled'], false);
        $this->assertEquals($content['idvt_required'], true);
    }

    public function test_the_application_cannot_update_custodian_they_dont_own(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/1',
                [
                    'name' => 'Updated Custodian',
                    'enabled' => false,
                    'idvt_required' => true,
                ]
            );
        $response->assertStatus(403);
    }

    public function test_the_application_can_delete_custodian(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'DELETE',
                self::TEST_URL . '/2'
            );

        $response->assertStatus(200);
    }

    public function test_the_application_cannot_delete_custodian_they_dont_own(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'DELETE',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(403);
    }

    public function test_the_application_can_get_custodians_by_unique_identifier(): void
    {
        $custodianCreated = Custodian::first();

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '/identifier/' . $custodianCreated->unique_identifier
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson()['data'];

        $this->assertEquals($content['name'], $custodianCreated->name);
    }


    public function test_the_application_cannot_get_custodians_by_unique_identifier(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '/identifier/uniqueIdentifierForTest'
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_receive_custodian_pushes_with_valid_key(): void
    {
        $user = $this->user;
        $user->update([
            'user_group' => User::GROUP_ADMINS
        ]);
        $custodian = Custodian::first();
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                self::TEST_URL . '/push',
                [
                    'researchers' => [],
                    'projects' => [
                        [
                            'unique_id' => $this->projectUniqueId,
                            'title' => 'This is a Test Project',
                            'lay_summary' => 'Test Lay Summary',
                            'public_benefit' => 'No one dies, ever.',
                            'request_category_type' => 'category type',
                            'technical_summary' => 'Technical Summary',
                            'other_approval_committees' => 'Does anyone actually know what this means?',
                            'start_date' => Carbon::now()->addMonths(6),
                            'end_date' => Carbon::now()->addYears(2),
                            'affiliate_id' => 1,
                        ],
                    ],
                    'organisations' => [
                        [
                            'organisation_name' => 'HEALTH DATA RESEARCH UK',
                            'address_1' => '215 Euston Road',
                            'address_2' => '',
                            'town' => 'Blah',
                            'county' => 'London',
                            'country' => 'United Kingdom',
                            'postcode' => 'NW1 2BE',
                            'lead_applicant_organisation_name' => 'Some One',
                            'lead_applicant_email' => fake()->email(),
                            'password' => 'tempP4ssword',
                            'organisation_unique_id' => Str::random(40),
                            'applicant_names' => 'Some One, Some Two, Some Three',
                            'funders_and_sponsors' => 'UKRI, MRC',
                            'sub_license_arrangements' => 'N/A',
                            'verified' => false,
                            'companies_house_no' => '10887014',
                            'dsptk_expiry_date' => now()->addYears(1),
                            'dsptk_ods_code' => '12345Z',
                            'iso_expiry_date' => now()->addYears(1),
                            'iso_27001_certification_num' => '',
                            'ce_expiry_date' => now()->addYears(1),
                            'ce_certification_num' => 'A1234',
                            'sector_id' => fake()->randomElement([0, count(Sector::SECTORS)]),
                            'charities' => [
                                'registration_id' => '1186569',
                            ],
                            'ror_id' => '02wnqcb97',
                            'smb_status' => false,
                            'organisation_size' => 2,
                            'website' => 'https://www.website.com/',
                        ],
                    ],
                ],
                [
                    'x-custodian-key' => $custodian->unique_identifier,
                ]
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];

        $this->assertEquals('0', $content['researchers_created']);
        $this->assertEquals('1', $content['projects_created']);
        $this->assertEquals('1', $content['organisations_created']);
    }

    public function test_the_application_can_refuse_pushes_with_missing_key(): void
    {
        $user = $this->user;
        $user->update([
            'user_group' => User::GROUP_ADMINS
        ]);
        $response = $this->actingAs($user)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'name' => 'Test Custodian ABCDEF',
                    'contact_email' => 'test@test.com',
                    'enabled' => true,
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson();

        $custodian = Custodian::where('id', $content['data'])->first();

        $response =  $this->actingAs($user)
            ->json(
                'POST',
                self::TEST_URL . '/push',
                [
                    'researchers' => [],
                    'projects' => [
                        [
                            'unique_id' => $this->projectUniqueId,
                            'title' => 'This is a Test Project',
                            'lay_summary' => 'Test Lay Summary',
                            'public_benefit' => 'No one dies, ever.',
                            'request_category_type' => 'category type',
                            'technical_summary' => 'Technical Summary',
                            'other_approval_committees' => 'Does anyone actually know what this means?',
                            'start_date' => Carbon::now()->addMonths(6),
                            'end_date' => Carbon::now()->addYears(2),
                            'affiliate_id' => 1,
                        ],
                    ],
                    'organisations' => [
                        [
                            'organisation_name' => 'HEALTH DATA RESEARCH UK',
                            'address_1' => '215 Euston Road',
                            'address_2' => '',
                            'town' => 'Blah',
                            'county' => 'London',
                            'country' => 'United Kingdom',
                            'postcode' => 'NW1 2BE',
                            'lead_applicant_organisation_name' => 'Some One',
                            'lead_applicant_email' => fake()->email(),
                            'password' => 'tempP4ssword',
                            'organisation_unique_id' => Str::random(40),
                            'applicant_names' => 'Some One, Some Two, Some Three',
                            'funders_and_sponsors' => 'UKRI, MRC',
                            'sub_license_arrangements' => 'N/A',
                            'verified' => false,
                            'companies_house_no' => '10887014',
                            'dsptk_expiry_date' => now()->addYears(1),
                            'dsptk_ods_code' => '12345Z',
                            'iso_expiry_date' => now()->addYears(1),
                            'iso_27001_certification_num' => '',
                            'ce_expiry_date' => now()->addYears(1),
                            'ce_certification_num' => 'A1234',
                            'sector_id' => fake()->randomElement([0, count(Sector::SECTORS)]),
                            'charities' => [
                                'registration_id' => '1186569',
                            ],
                            'ror_id' => '02wnqcb97',
                            'smb_status' => false,
                            'organisation_size' => 2,
                            'website' => 'https://www.website.com/',
                        ],
                    ],
                ]
            );

        $response->assertStatus(401);
        $content = $response->decodeResponseJson();

        $this->assertEquals($content['message'], 'you must be a trusted custodian and provide your custodian-key within the request headers');
    }


    public function test_the_application_can_sort_returned_data(): void
    {

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '?sort=name:desc'
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson();

        $this->assertTrue(count($content['data']['data']) > 0);
        $this->assertTrue($content['data']['data'][0]['name'] === 'SAIL Databank');

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '?sort=name:asc'
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson();

        $this->assertTrue(count($content['data']['data']) > 0);
        $this->assertTrue($content['data']['data'][0]['name'] === 'NHS England');
    }

    public function test_can_get_rules_for_existing_custodian(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/rules'
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'title',
                    'description'
                ]
            ]
        ]);

        $nonexistentId = 99999;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$nonexistentId}/rules"
            );

        $response->assertStatus(400);
    }

    public function test_custodian_can_get_project_users(): void
    {
        $custodianId = $this->custodian_admin->custodian_user->custodian_id;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '/' . $custodianId . '/projects_users'
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson();

        $this->assertEquals('success', $content['message']);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('data', $content['data']);
        $this->assertIsArray($content['data']['data']);
    }

    public function test_user_cannot_get_project_users_for_other_custodian(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects_users'
            );

        $response->assertStatus(403)
            ->assertJson(['message' => 'forbidden']);
    }

    public function test_custodian_cannot_get_projects(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;


        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}/projects"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_custodian_cannot_get_projects_and_users(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;

        $latestUser = User::query()->orderBy('id', 'desc')->first();
        $userIdTest = $latestUser->id + 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}/users/{$userIdTest}/projects"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);

    }

    public function test_custodian_cannot_get_projects_and_organisations(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}/organisations"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_custodian_cannot_get_custodian_users(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}/custodian_users"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_custodian_cannot_get_custodian_projects_users(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}/projects_users"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_custodian_cannot_get_custodian_rules(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}/rules"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_custodian_cannot_get_organisations_users(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;

        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation->id + 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}/organisations/{$organisationIdTest}/users"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_custodian_cannot_get_statuses(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian->id + 1;

        $latestProjectHasUser = ProjectHasUser::query()->orderBy('id', 'desc')->first();
        $projectHasUserIdTest = $latestProjectHasUser ? $latestProjectHasUser->id + 1 : 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}/projectUsers/{$projectHasUserIdTest}/statuses"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_invite_a_custodian(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian ? $latestCustodian->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . '/' . $custodianIdTest . '/invite/',
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_create_projects(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian ? $latestCustodian->id + 1 : 1;

        $user = $this->user;
        $user->update([
            'user_group' => User::GROUP_ADMINS
        ]);
        $response = $this->actingAs($user)
            ->json(
                'POST',
                self::TEST_URL . "/{$custodianIdTest}/projects",
                [
                    'title' => 'Test project',
                ]
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_update_custodian(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian ? $latestCustodian->id + 1 : 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$custodianIdTest}",
                [
                    'name' => 'Updated Custodian',
                    'enabled' => false,
                    'idvt_required' => true,
                ]
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_delete_custodian(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian ? $latestCustodian->id + 1 : 1;

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'DELETE',
                self::TEST_URL . "/{$custodianIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }
}
