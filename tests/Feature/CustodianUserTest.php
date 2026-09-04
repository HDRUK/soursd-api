<?php

namespace Tests\Feature;

use Http;
use KeycloakGuard\ActingAsKeycloakUser;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Queue;
use App\Models\Custodian;
use App\Models\CustodianUser;
use App\Models\CustodianUserHasPermission;
use App\Models\PendingInvite;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\Authorisation;

class CustodianUserTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;

    public const TEST_URL = '/api/v1/custodian_users';

    public function setUp(): void
    {
        parent::setUp();
        $this->withUsers(true);

        Http::fake([
            env('KEYCLOAK_BASE_URL') . '/*' => Http::response([
                'access_token' => 'fake-token-123',
                'success' => true,
                'error' => null,
            ], 200, [
                'Content-Type' => 'application/json',
            ])
        ]);
    }

    public function test_the_application_can_list_custodian_users(): void
    {
        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'GET',
                self::TEST_URL
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_can_show_custodian_users(): void
    {
        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'GET',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_cannot_show_custodian_users(): void
    {
        $latestCustodianUser = CustodianUser::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodianUser ? $latestCustodianUser->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_create_custodian_users(): void
    {
        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
        ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                    'password' => Str::random(12),
                    'provider' => fake()->word(),
                    'keycloak_id' => ''
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_can_update_custodian_users(): void
    {
        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                    'password' => Str::random(12),
                    'provider' => fake()->word(),
                    'keycloak_id' => ''
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson();
        $this->assertGreaterThan(0, $content['data']);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $content['data'],
                [
                    'first_name' => 'Updated',
                    'last_name' => 'Name',
                    'email' => fake()->email(),
                ]
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];

        $this->assertEquals($content['first_name'], 'Updated');
        $this->assertEquals($content['last_name'], 'Name');
    }

    public function test_the_application_cannot_update_custodian_users(): void
    {
        $latestCustodianUser = CustodianUser::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodianUser ? $latestCustodianUser->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$custodianIdTest}",
                [
                    'first_name' => 'Updated',
                    'last_name' => 'Name',
                    'email' => fake()->email(),
                ]
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_delete_custodian_users(): void
    {
        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                    'password' => Str::random(12),
                    'provider' => fake()->word(),
                    'keycloak_id' => ''
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson()['data'];
        $this->assertGreaterThan(0, $content);

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'DELETE',
                self::TEST_URL . '/' . $content
            );

        $response->assertStatus(200);
    }

    public function test_the_application_cannot_delete_custodian_users(): void
    {
        $latestCustodianUser = CustodianUser::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodianUser ? $latestCustodianUser->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'DELETE',
                self::TEST_URL . "/{$custodianIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_invite_a_user_for_custodian(): void
    {
        Queue::assertNothingPushed();

        //CustodianUser::truncate();

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                    'password' => Str::random(12),
                    'provider' => fake()->word(),
                    'keycloak_id' => ''
                ]
            );

        $response->assertStatus(201);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL . '/invite/1'
            );

        Queue::assertPushed(SendEmailJob::class);

        $invites = PendingInvite::all();

        $this->assertTrue(count($invites) === 1);
    }

    public function test_the_application_cannot_invite_a_user_for_custodian(): void
    {
        $latestCustodianUser = CustodianUser::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodianUser ? $latestCustodianUser->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)

            ->json(
                'POST',
                self::TEST_URL . "/invite/{$custodianIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_assign_permissions_to_custodian_users(): void
    {
        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                    'password' => Str::random(12),
                    'provider' => fake()->word(),
                    'keycloak_id' => '',
                    'permissions' => [
                        1,
                        3,
                        5,
                    ],
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson()['data'];

        $perms = CustodianUserHasPermission::where('custodian_user_id', $content)->get()->pluck('custodian_user_id');
        $this->assertTrue(count($perms) > 0);
    }

    private function grantCustodianAdmin(CustodianUser $custodianUser): void
    {
        $permission = Permission::where('name', 'CUSTODIAN_ADMIN')->firstOrFail();

        CustodianUserHasPermission::firstOrCreate([
            'custodian_user_id' => $custodianUser->id,
            'permission_id' => $permission->id,
        ]);
    }

    public function test_creating_a_custodian_user_requires_the_custodian_admin_permission(): void
    {
        $this->enableMiddleware();

        // custodian_admin fixture from BaseDemoSeeder already carries CUSTODIAN_ADMIN,
        // so use a custodian user with no permissions at all to test the negative case.
        $nonAdminCustodianUser = CustodianUser::factory()->create();
        $nonAdmin = User::factory()->create([
            'user_group' => User::GROUP_CUSTODIANS,
            'email' => $nonAdminCustodianUser->email,
            'keycloak_id' => (string) Str::uuid(),
            'custodian_user_id' => $nonAdminCustodianUser->id,
            'unclaimed' => 0,
        ]);

        $response = $this->actingAsKeycloakUser($nonAdmin, $this->getMockedKeycloakPayload())
            ->actingAs($nonAdmin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                ]
            );

        $response->assertStatus(403);
    }

    public function test_a_custodian_admin_can_create_custodian_users(): void
    {
        $this->enableMiddleware();
        $this->grantCustodianAdmin(CustodianUser::find($this->custodian_admin->custodian_user_id));

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'first_name' => fake()->firstname(),
                    'last_name' => fake()->lastname(),
                    'email' => fake()->email(),
                ]
            );

        $response->assertStatus(201);
    }

    public function test_a_custodian_admin_cannot_update_a_custodian_user_belonging_to_another_custodian(): void
    {
        $this->enableMiddleware();
        $this->grantCustodianAdmin(CustodianUser::find($this->custodian_admin->custodian_user_id));

        $otherCustodian = Custodian::factory()->create();
        $otherCustodianUser = CustodianUser::factory()->create(['custodian_id' => $otherCustodian->id]);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $otherCustodianUser->id,
                [
                    'first_name' => 'Should Not',
                    'last_name' => 'Apply',
                ]
            );

        $response->assertStatus(403);
        $this->assertEquals(
            $otherCustodianUser->first_name,
            CustodianUser::find($otherCustodianUser->id)->first_name
        );
    }

    public function test_a_custodian_admin_cannot_delete_a_custodian_user_belonging_to_another_custodian(): void
    {
        $this->enableMiddleware();
        $this->grantCustodianAdmin(CustodianUser::find($this->custodian_admin->custodian_user_id));

        $otherCustodian = Custodian::factory()->create();
        $otherCustodianUser = CustodianUser::factory()->create(['custodian_id' => $otherCustodian->id]);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'DELETE',
                self::TEST_URL . '/' . $otherCustodianUser->id
            );

        $response->assertStatus(403);
        $this->assertNotNull(CustodianUser::find($otherCustodianUser->id));
    }

    public function test_a_custodian_admin_can_update_a_custodian_user_belonging_to_their_own_custodian(): void
    {
        $this->enableMiddleware();
        $ownCustodianUser = CustodianUser::find($this->custodian_admin->custodian_user_id);
        $this->grantCustodianAdmin($ownCustodianUser);

        $teammate = CustodianUser::factory()->create(['custodian_id' => $ownCustodianUser->custodian_id]);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $teammate->id,
                [
                    'first_name' => 'Updated',
                    'last_name' => 'Name',
                ]
            );

        $response->assertStatus(200);
    }
}
