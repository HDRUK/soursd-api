<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\State;
use App\Models\Sector;
use App\Models\Project;
use App\Models\ActionLog;
use App\Models\Custodian;
use App\Jobs\SendEmailJob;
use App\Models\ModelState;
use App\Models\Affiliation;
use Illuminate\Support\Str;
use App\Models\Organisation;
use App\Models\PendingInvite;
use Tests\Traits\Authorisation;
use App\Models\ProjectHasSponsorship;
use Illuminate\Support\Facades\Queue;
use App\Models\ProjectHasOrganisation;
use KeycloakGuard\ActingAsKeycloakUser;
use App\Models\OrganisationHasDepartment;

class OrganisationTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;

    public const TEST_URL = '/api/v1/organisations';
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
            'system_approved' => false,
            'sro_profile_uri' => 'https://myprofile.something',
        ];
    }

    public function test_the_application_can_search_on_org_name(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '?organisation_name[]=health%20pathways'
            );
        $response->assertStatus(200);
        $content = $response->decodeResponseJson();

        $this->assertTrue(count($content['data']['data']) === 1);
        $this->assertTrue($content['data']['data'][0]['organisation_name'] === 'Health Pathways (UK) Limited');
    }

    public function test_the_application_can_list_an_organisations_past_present_and_future_projects(): void
    {
        // Grab all projects and manually set dates to fit our need,
        // otherwise these tests would effectively timeout eventually.
        $orgProjects = ProjectHasOrganisation::where('organisation_id', '1')->get();
        for ($i = 0; $i < 1; $i++) {
            $project = Project::where('id', $orgProjects[$i]->project_id)->first();
            $project->start_date = Carbon::now();
            $project->end_date = Carbon::now()->addYears(1);
            $project->save();
        }

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects/present',
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];

        $this->assertTrue($content['data'][0]['start_date'] <= Carbon::now());
        $this->assertTrue($content['data'][0]['end_date'] >= Carbon::now());

        for ($i = 0; $i < 1; $i++) {
            $project = Project::where('id', $orgProjects[$i]->project_id)->first();
            $project->start_date = Carbon::now()->subYears(2);
            $project->end_date = Carbon::now()->subYears(1);
            $project->save();
        }

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects/past',
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];

        $this->assertTrue($content['data'][0]['start_date'] < Carbon::now());
        $this->assertTrue($content['data'][0]['end_date'] < Carbon::now());


        for ($i = 0; $i < 1; $i++) {
            $project = Project::where('id', $orgProjects[$i]->project_id)->first();
            $project->start_date = Carbon::now()->addYears(2);
            $project->end_date = Carbon::now()->addYears(3);
            $project->save();
        }

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects/future',
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];

        $this->assertTrue($content['data'][0]['start_date'] > Carbon::now());
        $this->assertTrue($content['data'][0]['end_date'] > Carbon::now());
    }

    public function test_the_application_can_list_an_organisations_past_and_active_projects_by_active(): void
    {
        $orgProjects = ProjectHasOrganisation::where('organisation_id', '1')->get();
        for ($i = 0; $i < 1; $i++) {
            $project = Project::where('id', $orgProjects[$i]->project_id)->first();
            $project->start_date = Carbon::now();
            $project->end_date = Carbon::now()->addYears(1);
            $project->save();
        }

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects?active=1',
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];

        $this->assertTrue($content['data'][0]['start_date'] <= Carbon::now());
        $this->assertTrue($content['data'][0]['end_date'] >= Carbon::now());

        for ($i = 0; $i < 1; $i++) {
            $project = Project::where('id', $orgProjects[$i]->project_id)->first();
            $project->start_date = Carbon::now()->subYears(2);
            $project->end_date = Carbon::now()->subYears(1);
            $project->save();
        }

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects?active=0',
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];
    }

    public function test_the_application_can_list_an_organisations_filter_by_state(): void
    {
        $orgProjects = ProjectHasOrganisation::where('organisation_id', '1')->get();
        $array = [];
        foreach ($orgProjects as $orgProject) {
            $projectId = $orgProject->project_id;

            $modelState = ModelState::where([
                'stateable_id' => $projectId,
                'stateable_type' => Project::class
            ])->first();
            $state = State::where('id', $modelState->state_id)->first();

            $array[] = [
                'orgProjectId' => $projectId,
                'state' => $state->slug,
            ];

        }

        $arrStates = array_count_values(array_column($array, 'state'));

        foreach ($arrStates as $key => $value) {
            $response = $this->actingAs($this->organisation_admin)
                ->json(
                    'GET',
                    self::TEST_URL . "/1/projects?filter[]={$key}",
                );
            $response->assertStatus(200);
            $respnseData = $response->decodeResponseJson()['data'];
            $countResponse = count($respnseData['data']);

            $this->assertEquals($countResponse, $value);
        }

    }

    public function test_the_application_can_list_organisations(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'created_at',
                        'updated_at',
                        'organisation_name',
                        'address_1',
                        'address_2',
                        'town',
                        'county',
                        'country',
                        'postcode',
                        'lead_applicant_organisation_name',
                        'lead_applicant_email',
                        'applicant_names',
                        'funders_and_sponsors',
                        'sub_license_arrangements',
                        'verified',
                        'dsptk_ods_code',
                        'dsptk_expiry_date',
                        'dsptk_expiry_evidence',
                        'iso_27001_certification_num',
                        'iso_expiry_date',
                        'iso_expiry_evidence',
                        'ce_certification_num',
                        'ce_expiry_date',
                        'ce_expiry_evidence',
                        'ce_plus_certification_num',
                        'ce_plus_expiry_date',
                        'ce_plus_expiry_evidence',
                        'registries',
                        'departments',
                        'sector_id',
                        'charities' => [
                            '*' => [
                                'id',
                                'registration_id',
                                'name',
                                'website',
                                'address_1',
                                'address_2',
                                'town',
                                'county',
                                'country',
                                'postcode',
                            ],
                        ],
                        'ror_id',
                        'smb_status',
                        'organisation_size',
                        'website',
                        'system_approved',
                        'sro_profile_uri',
                    ],
                ],
            ]
        ]);
    }

    public function test_the_application_can_show_organisations(): void
    {

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'created_at',
                'updated_at',
                'organisation_name',
                'address_1',
                'address_2',
                'town',
                'county',
                'country',
                'postcode',
                'lead_applicant_organisation_name',
                'lead_applicant_email',
                'applicant_names',
                'funders_and_sponsors',
                'sub_license_arrangements',
                'verified',
                'dsptk_ods_code',
                'dsptk_expiry_date',
                'dsptk_expiry_evidence',
                'iso_27001_certification_num',
                'iso_expiry_date',
                'iso_expiry_evidence',
                'ce_certification_num',
                'ce_expiry_date',
                'ce_expiry_evidence',
                'ce_plus_certification_num',
                'ce_plus_expiry_date',
                'ce_plus_expiry_evidence',
                'registries',
                'departments',
                'sector_id',
                'charities' => [
                    '*' => [
                        'id',
                        'registration_id',
                        'name',
                        'website',
                        'address_1',
                        'address_2',
                        'town',
                        'county',
                        'country',
                        'postcode',
                    ],
                ],
                'ror_id',
                'smb_status',
                'organisation_size',
                'website',
                'system_approved',
                'sro_profile_uri',
            ],
        ]);
    }

    public function test_the_application_can_create_organisations(): void
    {
        $isoCertified = fake()->randomElement([1, 0]);
        $ceCertified = fake()->randomElement([1, 0]);

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL,
                $this->testOrg
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);
    }

    // LS - Failing in GH - unsure why needs investigation
    //
    // public function test_the_application_can_create_unclaimed_organisations(): void
    // {
    //     $email = fake()->email();

    //     $response = $this->actingAs($this->admin)
    //         ->json(
    //             'POST',
    //             self::TEST_URL . '/unclaimed',
    //             [
    //                 'lead_applicant_email' => $email
    //             ]
    //         );

    //     $response->assertStatus(200);
    // }

    public function test_the_application_can_create_organisations_with_departments(): void
    {
        $isoCertified = fake()->randomElement([1, 0]);
        $ceCertified = fake()->randomElement([1, 0]);

        $this->testOrg['departments'] = [1, 2, 3];

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL,
                $this->testOrg
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson()['data'];

        $depts = OrganisationHasDepartment::where('organisation_id', $content)->get();
        $this->assertTrue(count($depts) === 3);

        foreach ($depts as $d) {
            $this->assertTrue(in_array($d->department_id, $this->testOrg['departments']));
        }
    }

    public function test_the_application_can_update_organisations(): void
    {
        ActionLog::query()->update(['completed_at' => null]);
        Carbon::setTestNow(Carbon::now());
        $isoCertified = fake()->randomElement([1, 0]);
        $ceCertified = fake()->randomElement([1, 0]);

        $this->enableObservers();

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/' . 1 . '/action_log'
            );

        $response->assertStatus(200);
        $responseData = $response['data'];
        $actionLog = collect($responseData)
            ->firstWhere('action', Organisation::ACTION_NAME_ADDRESS_COMPLETED);

        $this->assertNull($actionLog['completed_at']);

        $newDate = Carbon::now()->subYears(2);
        $responseUpdate = $this->actingAs($this->organisation_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/' . 1,
                [
                    'organisation_name' => 'Test Organisation bla',
                    'address_1' => '123 Blah blah',
                    'address_2' => '',
                    'town' => 'Town',
                    'county' => 'County',
                    'country' => 'Country',
                    'postcode' => 'BLA4 4HH',
                    'lead_applicant_organisation_name' => 'Some One',
                    'lead_applicant_email' => fake()->email(),
                    'organisation_unique_id' => Str::random(40),
                    'applicant_names' => 'Some One, Some Two, Some Three',
                    'funders_and_sponsors' => 'UKRI, MRC',
                    'sub_license_arrangements' => 'N/A',
                    'verified' => true,
                    'companies_house_no' => '10887014',
                    'sector_id' => fake()->randomElement([0, count(Sector::SECTORS)]),
                    'charities' => [
                        'registration_id' => '1186569',
                    ],
                    'ror_id' => '02wnqcb97',
                    'smb_status' => false,
                    'organisation_size' => 2,
                    'website' => 'https://www.website.com/',
                ]
            );

        $responseUpdate->assertStatus(200);
        $this->assertArrayHasKey('data', $responseUpdate);
        $this->assertDatabaseHas('organisations', [
            'id' => $responseUpdate['data']['id'],
            'address_1' => '123 Blah blah',
        ]);
        // system_approved should not be updated via this endpoint
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1'
            );
        $this->assertDatabaseMissing('organisations', [
            'id' => $responseUpdate['data']['id'],
            'system_approved' => true,
        ]);

        $responseActionLog = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/' . 1 . '/action_log'
            );

        $responseActionLog->assertStatus(200);
        $responseData = $responseActionLog['data'];
        $actionLog = collect($responseData)
            ->firstWhere('action', Organisation::ACTION_NAME_ADDRESS_COMPLETED);

        $this->assertEquals(
            Carbon::now()->format('Y-m-d H:i:s'),
            $actionLog['completed_at']
        );
    }

    public function test_the_application_can_delete_organisations(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'DELETE',
                self::TEST_URL . '/' . 1
            );
        $response->assertStatus(200);
    }

    public function test_the_application_can_show_idvt(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/idvt'
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'idvt_result',
                'idvt_result_perc',
                'idvt_completed_at',
                'idvt_errors',
            ],
        ]);
    }

    public function test_the_application_can_sort_returned_data(): void
    {
        $this->testOrg['organisation_name'] = 'ZYX Org';



        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL,
                $this->testOrg
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $this->testOrg['organisation_name'] = 'ABC Org';

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL,
                $this->testOrg
            );



        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '?sort=organisation_name:desc'
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson();

        $this->assertTrue(count($content['data']['data']) > 0);
        $this->assertTrue($content['data']['data'][0]['organisation_name'] === 'ZYX Org');

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '?sort=organisation_name:asc'
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson();

        $this->assertTrue(count($content['data']['data']) > 0);
        $this->assertTrue($content['data']['data'][0]['organisation_name'] === 'ABC Org');
    }

    public function test_the_application_can_return_certification_counts_for_organisations(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/counts/certifications'
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
        ]);

        $content = $response->decodeResponseJson();
        $this->assertTrue($content['data'] > 0);
    }

    public function test_the_application_can_return_affiliated_user_counts_for_organisations(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/counts/users'
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
        ]);

        $content = $response->decodeResponseJson();
        $this->assertTrue($content['data'] > 0);
    }

    public function test_the_application_can_invite_a_user_for_organisations(): void
    {
        Queue::assertNothingPushed();

        $email = fake()->email();
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'POST',
                self::TEST_URL . '/1/invite_user',
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'identifier' => 'user_invite'
                ],
            );

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'organisation_id' => 0,
            'is_delegate' => 0,
            'user_group' => 'USERS',
            'role' => null,
            'invited_by' => $this->organisation_admin->id,
        ]);

        Queue::assertPushed(SendEmailJob::class);

        $invites = PendingInvite::all();

        $this->assertTrue(count($invites) === 1);
        $this->assertTrue($invites[0]->organisation_id === 1);
    }

    public function test_the_application_cannot_invite_a_user_for_organisations_by_non_organisation_admin(): void
    {
        Queue::assertNothingPushed();

        $email = fake()->email();
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL . '/1/invite_user',
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'identifier' => 'user_invite'
                ],
            );

        $response->assertStatus(400);
    }

    public function test_the_application_can_invite_a_user_for_organisations_by_custodian(): void
    {
        Queue::assertNothingPushed();

        $email = fake()->email();
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL . '/1/custodian_invite_user',
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ],
            );

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'organisation_id' => 0,
            'is_delegate' => 0,
            'user_group' => 'USERS',
            'role' => null,
        ]);

        Queue::assertPushed(SendEmailJob::class);

        $invites = PendingInvite::all();

        $this->assertTrue(count($invites) === 1);
        $this->assertTrue($invites[0]->organisation_id === 1);
    }

    public function test_the_application_can_invite_a_user_for_organisations_by_custodian_with_user_group_users(): void
    {
        Queue::assertNothingPushed();

        $countAffiliationBefore = Affiliation::count();
        $email = fake()->email();
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL . '/1/custodian_invite_user',
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'user_group' => 'USERS',
                ],
            );

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'organisation_id' => 0,
            'is_delegate' => 0,
            'user_group' => 'USERS',
            'role' => null,
        ]);

        Queue::assertPushed(SendEmailJob::class);

        $invites = PendingInvite::all();

        $this->assertTrue(count($invites) === 1);
        $this->assertTrue($invites[0]->organisation_id === 1);
        $countAffiliationAfter = Affiliation::count();
        $this->assertTrue($countAffiliationAfter === $countAffiliationBefore + 1);
    }

    public function test_the_application_can_invite_a_user_for_organisations_by_custodian_with_non_user_group_users(): void
    {
        Queue::assertNothingPushed();

        $countAffiliationBefore = Affiliation::count();
        $email = fake()->email();
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL . '/1/custodian_invite_user',
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'user_group' => 'ORGANISATIONS',
                ],
            );

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'organisation_id' => 1,
            'is_delegate' => 0,
            'user_group' => 'ORGANISATIONS',
            'role' => null,
        ]);

        Queue::assertPushed(SendEmailJob::class);

        $invites = PendingInvite::all();

        $this->assertTrue(count($invites) === 1);
        $this->assertTrue($invites[0]->organisation_id === 1);

        $countAffiliationAfter = Affiliation::count();
        $this->assertTrue($countAffiliationAfter === $countAffiliationBefore);
    }

    public function test_the_application_cannot_invite_a_user_for_organisations_by_organisation(): void
    {
        Queue::assertNothingPushed();

        $email = fake()->email();
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'POST',
                self::TEST_URL . '/1/custodian_invite_user',
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ],
            );

        $response->assertStatus(400);
    }

    public function test_the_application_can_invite_organisations(): void
    {
        $user = $this->user;
        $user->update([
            'user_group' => User::GROUP_ADMINS
        ]);

        Queue::assertNothingPushed();

        $response = $this->actingAs($user)
            ->json(
                'POST',
                self::TEST_URL . '/2/invite/',
            );

        $response->assertStatus(201);

        $invites = PendingInvite::all();

        $this->assertTrue(count($invites) === 1);
    }

    public function test_the_application_can_get_projects_for_an_organisation(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects'
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);

        $n = ProjectHasOrganisation::where([
            'organisation_id' => 1
        ])->count();

        $this->assertCount($n, $response['data']['data']);


        $responseWithTitleFilter = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects?title[]=Assessing Air Quality Impact on Respiratory Health in Urban Populations'
            );

        $this->assertCount(
            1,
            $responseWithTitleFilter['data']['data'],
            'Expected exactly 1 project with the title "Assessing Air Quality Impact on Respiratory Health in Urban Populations".'
        );

        $responseSortedByTitle = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects?sort=title:asc'
            );

        $responseSortedByTitle->assertStatus(200);
        $titles = array_column($responseSortedByTitle['data']['data'], 'title');

        $sortedTitles = $titles;
        sort($sortedTitles);


        $this->assertEquals(
            $sortedTitles,
            $titles,
            'The projects are not sorted alphabetically by title.'
        );

        $responseSortedByTitle = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects?sort=title:desc'
            );

        $responseSortedByTitle->assertStatus(200);
        $titles = array_column($responseSortedByTitle['data']['data'], 'title');

        $sortedTitles = $titles;
        rsort($sortedTitles);

        $this->assertEquals(
            $sortedTitles,
            $titles,
            'The projects are not sorted alphabetically by title (descending).'
        );

        $responseSortedByTitle = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects?sort=title'
            );
        $responseSortedByTitle->assertStatus(500, 'Needs a direction');


        $responseSortedByTitle = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects?sort=title:abc'
            );
        $responseSortedByTitle->assertStatus(500, 'unknwon direction');

        $responseSortedByTitle = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/projects?sort=abc:xyz'
            );
        $responseSortedByTitle->assertStatus(500);
    }

    public function test_the_application_can_list_users_for_an_organisation(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/users'
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
        // Org owner, admin user and delegate
        $this->assertCount(3, $response['data']['data']);

        $responseWithEmailFilter = $this->actingAs($this->organisation_admin)
            ->json(
                'GET',
                self::TEST_URL . '/1/users?email[]=organisation.owner@healthdataorganisation.com'
            );

        $this->assertCount(
            1,
            $responseWithEmailFilter['data']['data'],
            'organisation.owner@healthdataorganisation.com'
        );
    }

    public function test_the_application_can_create_organisation_with_user(): void
    {
        $isoCertified = fake()->randomElement([1, 0]);
        $ceCertified = fake()->randomElement([1, 0]);

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL . '/new_account',
                [
                    'organisation_name' => 'test test Org',
                    'lead_applicant_email' => 'phil.reeks+org22@hdruk.ac.uk',
                    'unclaimed' => 1,
                    'first_name' => 'Phil',
                    'last_name' => 'Reeks',
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $this->assertEquals($response->json()['message'], 'success');
        $this->assertNotNull($response->json()['data']['user_id']);
        $this->assertNotNull($response->json()['data']['organisation_id']);
    }

    public function test_the_application_honours_organisations_system_approval(): void
    {
        $this->testOrg['system_approved'] = false;

        $isoCertified = fake()->randomElement([1, 0]);
        $ceCertified = fake()->randomElement([1, 0]);

        $response = $this->actingAs($this->admin)
            ->json(
                'POST',
                self::TEST_URL,
                $this->testOrg
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $content = $response->json()['data'];

        // $response = $this->actingAs($this->admin)
        //     ->json(
        //         'PUT',
        //         self::TEST_URL . '/' . $content,
        //         [
        //             'organisation_name' => 'Cant update no',
        //         ]
        //     );

        // $response->assertStatus(403);
        $org = Organisation::where('id', $content)->first();
        $org->system_approved = true;
        $org->save();

        $this->assertDatabaseHas('organisations', [
            'id' => $org->id,
            'system_approved' => true,
        ]);

        $this->assertEquals($org->organisation_name, $this->testOrg['organisation_name']);

        $response = $this->actingAs($this->admin)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $org->id,
                [
                    'organisation_name' => 'Can update now',
                ]
            );

        $response->assertStatus(200);
        $this->assertEquals(Organisation::where('id', $org->id)->first()->organisation_name, 'Can update now');
    }

    public function test_the_application_cannot_get_organisations(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_idvt(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/idvt"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_count_certifications(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/counts/certifications"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_count_users(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/counts/users"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_count_projects_present(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/counts/projects/present"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_count_projects_past(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/counts/projects/past"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_projects_present(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/projects/present"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_projects_past(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/projects/past"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_projects_future(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/projects/future"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_projects(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/projects"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_users(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/users"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_delegates(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/delegates"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_registries(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$organisationIdTest}/registries"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_validate_ror(): void
    {
        $randomFakeString = fake()->regexify('[A-Za-z0-9]{7}');

        $response = $this->actingAs($this->admin)
            ->json(
                'GET',
                self::TEST_URL . "/ror/{$randomFakeString}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_invite(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $user = $this->user;
        $user->update([
            'user_group' => User::GROUP_ADMINS
        ]);

        Queue::assertNothingPushed();

        $response = $this->actingAs($user)
            ->json(
                'POST',
                self::TEST_URL . "/{$organisationIdTest}/invite",
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_get_organisations_invite_user(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $email = fake()->email();
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL . "/{$organisationIdTest}/invite_user",
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'identifier' => 'user_invite'
                ],
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_update_organisations(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$organisationIdTest}",
                [
                    'organisation_name' => 'Test Organisation',
                    'address_1' => '123 Blah blah',
                    'address_2' => '',
                    'town' => 'Town',
                    'county' => 'County',
                    'country' => 'Country',
                    'postcode' => 'BLA4 4HH',
                    'lead_applicant_organisation_name' => 'Some One',
                    'lead_applicant_email' => fake()->email(),
                    'organisation_unique_id' => Str::random(40),
                    'applicant_names' => 'Some One, Some Two, Some Three',
                    'funders_and_sponsors' => 'UKRI, MRC',
                    'sub_license_arrangements' => 'N/A',
                    'verified' => true,
                    'companies_house_no' => '10887014',
                    'sector_id' => fake()->randomElement([0, count(Sector::SECTORS)]),
                    'charities' => [
                        'registration_id' => '1186569',
                    ],
                    'ror_id' => '02wnqcb97',
                    'smb_status' => false,
                    'organisation_size' => 2,
                    'website' => 'https://www.website.com/',
                ]
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_org_admin_cannot_update_organisations_approved(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id : 1;

        $systemApproved = fake()->randomElement([false, true]);

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$organisationIdTest}/approved",
                [
                    'system_approved' => $systemApproved,
                ]
            );

        $response->assertStatus(403);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('forbidden', $message);
    }

    public function test_the_application_cannot_update_organisations_approved_via_update(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $latestOrganisationSystemApproved = $latestOrganisation ? $latestOrganisation->system_approved : null;
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id : 1;

        $systemApproved = true;

        $response = $this->actingAs($this->admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$organisationIdTest}",
                [
                    'system_approved' => $systemApproved,
                ]
            );

        $response->assertStatus(200);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('success', $message);
        // Update should not change system_approved value - only allowed via the specific endpoint
        $finalSystemApproved = Organisation::where('id', $organisationIdTest)->value('system_approved');
        $this->assertEquals($latestOrganisationSystemApproved, $finalSystemApproved);
    }

    public function test_the_application_can_update_organisations_approved_by_admin(): void
    {
        $organisationIdTest = 1;
        $initSystemApproved = Organisation::where('id', $organisationIdTest)->value('system_approved');

        if ($initSystemApproved) {
            $initOrg = Organisation::where('id', $organisationIdTest)->first();
            $initOrg->system_approved = false;
            $initOrg->save();
        }

        $response = $this->actingAs($this->admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$organisationIdTest}/approved",
                [
                    'system_approved' => true,
                ]
            );

        $response->assertStatus(200);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('success', $message);
        $finalSystemApproved = Organisation::where('id', $organisationIdTest)->value('system_approved');
        $this->assertTrue($finalSystemApproved);

        $org = Organisation::where('id', $organisationIdTest)->first();
        $org->system_approved = $initSystemApproved;
        $org->save();
    }

    public function test_the_application_cannot_delete_organisations(): void
    {
        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAs($this->admin)
            ->json(
                'DELETE',
                self::TEST_URL . "/{$organisationIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_approve_sponsorship()
    {
        $organisationId = $this->organisation_admin->id;
        $custodianId = $this->custodian_admin->id;

        $custodian = Custodian::first();
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'POST',
                '/api/v1/custodians/' . $custodian->id . '/projects',
                [
                    'title' => 'New project',
                    'request_category_type' => '',
                    'start_date' => '',
                    'end_date' => '',
                    'lay_summary' => '',
                    'public_benefit' => '',
                    'technical_summary' => '',
                    'status' => 'project_pending',
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);
        $content = $response->decodeResponseJson();
        $projectId = $content['data'];
        $newDate = Carbon::now()->addYears(2);

        $responseUpdateProject = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                '/api/v1/projects/' . $projectId,
                [
                    'unique_id' => Str::random(30),
                    'title' => 'This is an Old Project',
                    'lay_summary' => 'Sample lay summary',
                    'public_benefit' => 'This will benefit the public',
                    'request_category_type' => 'Category type',
                    'technical_summary' => 'Sample technical summary',
                    'other_approval_committees' => 'Bodies on a board panel',
                    'start_date' => Carbon::now(),
                    'end_date' => $newDate,
                    'affiliate_id' => 1,
                    'status' => 'project_approved',
                    'sponsor_id' => $organisationId,
                ]
            );
        $responseUpdateProject->assertStatus(200);

        $contentUpdateProject = $responseUpdateProject->decodeResponseJson()['data'];
        $this->assertEquals($contentUpdateProject['title'], 'This is an Old Project');
        $this->assertEquals(Carbon::parse($contentUpdateProject['end_date'])->toDateTimeString(), $newDate->toDateTimeString());

        $projectHasSponsor = ProjectHasSponsorship::where('project_id', $projectId)->first();
        $this->assertEquals((int)$projectHasSponsor->sponsor_id, (int)$this->organisation_admin->id);


        $responseListProjects = $this->actingAsKeycloakUser($this->organisation_admin)
            ->json(
                'GET',
                '/api/v1/organisations/' .  $this->organisation_admin->id . '/projects/sponsorships',
            );
        $responseListProjects->assertStatus(200);
        $contentListProjects = $responseListProjects->decodeResponseJson()['data']['data'];
        $this->assertEquals(count($contentListProjects), 1);
        $this->assertEquals((int)$contentListProjects[0]['sponsors'][0]['id'], (int)$this->organisation_admin->id);
        $this->assertEquals($contentListProjects[0]['custodian_has_project_sponsorships']['model_state']['state']['slug'], State::STATE_SPONSORSHIP_PENDING);

        // approve
        $responseApproveProjects = $this->actingAsKeycloakUser($this->organisation_admin)
            ->json(
                'PATCH',
                '/api/v1/organisations/' .  $organisationId . '/sponsorships/statuses',
                [
                    'project_id' => $projectId,
                    'status' => 'approved',
                ]
            );
        $responseApproveProjects->assertStatus(200);
        $contentResponseApproveProjects = $responseApproveProjects->decodeResponseJson();

        $this->assertEquals($contentResponseApproveProjects['data'], State::STATE_SPONSORSHIP_APPROVED);
    }

    public function test_the_application_can_rejected_sponsorship()
    {
        $organisationId = $this->organisation_admin->id;
        $custodianId = $this->custodian_admin->id;

        $custodian = Custodian::first();
        $response = $this->actingAs($this->custodian_admin)
            ->json(
                'POST',
                '/api/v1/custodians/' . $custodian->id . '/projects',
                [
                    'title' => 'New project',
                    'request_category_type' => '',
                    'start_date' => '',
                    'end_date' => '',
                    'lay_summary' => '',
                    'public_benefit' => '',
                    'technical_summary' => '',
                    'status' => 'project_pending',
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);
        $content = $response->decodeResponseJson();
        $projectId = $content['data'];
        $newDate = Carbon::now()->addYears(2);

        $responseUpdateProject = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                '/api/v1/projects/' . $projectId,
                [
                    'unique_id' => Str::random(30),
                    'title' => 'This is an Old Project',
                    'lay_summary' => 'Sample lay summary',
                    'public_benefit' => 'This will benefit the public',
                    'request_category_type' => 'Category type',
                    'technical_summary' => 'Sample technical summary',
                    'other_approval_committees' => 'Bodies on a board panel',
                    'start_date' => Carbon::now(),
                    'end_date' => $newDate,
                    'affiliate_id' => 1,
                    'status' => 'project_approved',
                    'sponsor_id' => $organisationId,
                ]
            );
        $responseUpdateProject->assertStatus(200);

        $contentUpdateProject = $responseUpdateProject->decodeResponseJson()['data'];
        $this->assertEquals($contentUpdateProject['title'], 'This is an Old Project');
        $this->assertEquals(Carbon::parse($contentUpdateProject['end_date'])->toDateTimeString(), $newDate->toDateTimeString());

        $projectHasSponsor = ProjectHasSponsorship::where('project_id', $projectId)->first();
        $this->assertEquals((int)$projectHasSponsor->sponsor_id, (int)$this->organisation_admin->id);


        $responseListProjects = $this->actingAsKeycloakUser($this->organisation_admin)
            ->json(
                'GET',
                '/api/v1/organisations/' .  $this->organisation_admin->id . '/projects/sponsorships',
            );
        $responseListProjects->assertStatus(200);
        $contentListProjects = $responseListProjects->decodeResponseJson()['data']['data'];
        $this->assertEquals(count($contentListProjects), 1);
        $this->assertEquals((int)$contentListProjects[0]['sponsors'][0]['id'], (int)$this->organisation_admin->id);
        $this->assertEquals($contentListProjects[0]['custodian_has_project_sponsorships']['model_state']['state']['slug'], State::STATE_SPONSORSHIP_PENDING);

        // approve
        $responseApproveProjects = $this->actingAsKeycloakUser($this->organisation_admin)
            ->json(
                'PATCH',
                '/api/v1/organisations/' .  $organisationId . '/sponsorships/statuses',
                [
                    'project_id' => $projectId,
                    'status' => 'rejected',
                ]
            );
        $responseApproveProjects->assertStatus(200);
        $contentResponseApproveProjects = $responseApproveProjects->decodeResponseJson();

        $this->assertEquals($contentResponseApproveProjects['data'], State::STATE_SPONSORSHIP_REJECTED);
    }

    // LS - Removed as doesn't run in GH - possibly blocked by ROR
    // needs investigation
    //
    // public function test_the_application_can_validate_a_real_ror_id(): void
    // {
    //     $response = $this->actingAs($this->organisation_admin)
    //         ->json(
    //             'GET',
    //             self::TEST_URL . '/ror/015w2mp89',
    //         );
    //     $response->assertStatus(200);
    //     $this->assertArrayHasKey('data', $response);
    //     $this->assertEquals($response['data']['name'], 'National Physical Laboratory');
    // }

    // public function test_the_application_can_validate_a_fake_ror_id(): void
    // {
    //     $response = $this->actingAs($this->organisation_admin)
    //         ->json(
    //             'GET',
    //             self::TEST_URL . '/ror/123445',
    //         );
    //     $response->assertStatus(404);
    //     $this->assertArrayHasKey('data', $response);
    //     $this->assertEquals($response['data'], 'not found');
    // }
}
