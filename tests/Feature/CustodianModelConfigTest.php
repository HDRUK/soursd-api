<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Custodian;
use App\Models\CustodianUser;
use App\Models\CustodianModelConfig;
use App\Models\CustodianUserHasPermission;
use App\Models\Permission;
use App\Models\User;
use App\Traits\CommonFunctions;
use Illuminate\Support\Str;
use Tests\Traits\Authorisation;
use KeycloakGuard\ActingAsKeycloakUser;

class CustodianModelConfigTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;
    use CommonFunctions;

    public const TEST_URL = '/api/v1/custodian_config';

    public function setUp(): void
    {
        parent::setUp();
        $this->withUsers(true);
        $this->grantCustodianAdmin(CustodianUser::find($this->custodian_admin->custodian_user_id));
    }

    private function grantCustodianAdmin(CustodianUser $custodianUser): void
    {
        $permission = Permission::where('name', 'CUSTODIAN_ADMIN')->firstOrFail();

        CustodianUserHasPermission::firstOrCreate([
            'custodian_user_id' => $custodianUser->id,
            'permission_id' => $permission->id,
        ]);
    }

    /**
     * Create a User + linked CustodianUser in the given custodian, holding the given permission.
     *
     * @return array{0: User, 1: CustodianUser}
     */
    private function makeCustodianUserActor(int $custodianId, string $permissionName): array
    {
        $custodianUser = CustodianUser::factory()->create(['custodian_id' => $custodianId]);
        $user = User::factory()->create([
            'user_group' => User::GROUP_CUSTODIANS,
            'email' => $custodianUser->email,
            'keycloak_id' => (string) Str::uuid(),
            'custodian_user_id' => $custodianUser->id,
            'unclaimed' => 0,
        ]);

        $permission = Permission::where('name', $permissionName)->firstOrFail();
        CustodianUserHasPermission::create([
            'custodian_user_id' => $custodianUser->id,
            'permission_id' => $permission->id,
        ]);

        return [$user, $custodianUser];
    }

    public function test_the_application_can_show_custodian_config_by_custodian_id(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '/' . $custodianId
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
        $this->assertNotNull($response->decodeResponseJson()['data']);
    }

    public function test_a_custodian_approver_can_show_custodian_config_by_custodian_id(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        [$approver] = $this->makeCustodianUserActor($custodianId, 'CUSTODIAN_APPROVER');

        $response = $this->actingAsKeycloakUser($approver, $this->getMockedKeycloakPayload())
            ->actingAs($approver)
            ->json(
                'GET',
                self::TEST_URL . '/' . $custodianId
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_a_user_without_custodian_permissions_cannot_show_custodian_config(): void
    {
        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(403);
    }

    public function test_a_custodian_admin_cannot_show_another_custodians_config(): void
    {
        $otherCustodian = Custodian::factory()->create();

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . '/' . $otherCustodian->id
            );

        $response->assertStatus(403);
    }

    public function test_the_application_cannot_show_custodian_config_by_custodian_id(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian ? $latestCustodian->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'GET',
                self::TEST_URL . "/{$custodianIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_create_custodian_config(): void
    {
        $this->enableObservers();
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'decision_model_id' => 1,
                    'active' => 1,
                    'custodian_id' => $custodianId,
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);
        $this->assertNotNull($response->decodeResponseJson()['data']);
    }

    public function test_a_custodian_approver_cannot_create_custodian_config(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        [$approver] = $this->makeCustodianUserActor($custodianId, 'CUSTODIAN_APPROVER');

        $response = $this->actingAsKeycloakUser($approver, $this->getMockedKeycloakPayload())
            ->actingAs($approver)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'decision_model_id' => 1,
                    'active' => 1,
                    'custodian_id' => $custodianId,
                ]
            );

        $response->assertStatus(403);
    }

    public function test_a_custodian_admin_cannot_create_config_for_another_custodian(): void
    {
        $otherCustodian = Custodian::factory()->create();

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'decision_model_id' => 1,
                    'active' => 1,
                    'custodian_id' => $otherCustodian->id,
                ]
            );

        $response->assertStatus(403);
    }

    public function test_the_application_can_update_custodian_config(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'decision_model_id' => 1,
                    'active' => 1,
                    'custodian_id' => $custodianId,
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);
        $content = $response->decodeResponseJson();

        $this->assertNotNull($content['data']);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $content['data'],
                [
                    'active' => 0,
                ]
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
        $content = $response->decodeResponseJson();
        $this->assertEquals($content['data']['active'], 0);
    }

    public function test_a_custodian_approver_cannot_update_custodian_config(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        $config = CustodianModelConfig::where('custodian_id', $custodianId)->first();
        [$approver] = $this->makeCustodianUserActor($custodianId, 'CUSTODIAN_APPROVER');

        $response = $this->actingAsKeycloakUser($approver, $this->getMockedKeycloakPayload())
            ->actingAs($approver)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $config->id,
                [
                    'active' => 0,
                ]
            );

        $response->assertStatus(403);
    }

    public function test_a_custodian_admin_cannot_update_another_custodians_config(): void
    {
        $otherCustodian = Custodian::factory()->create();
        $otherConfig = CustodianModelConfig::factory()->create([
            'custodian_id' => $otherCustodian->id,
            'decision_model_id' => 1,
            'active' => 1,
        ]);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $otherConfig->id,
                [
                    'active' => 0,
                ]
            );

        $response->assertStatus(403);
    }

    public function test_the_application_cannot_update_custodian_config(): void
    {
        $latestCustodianModelConfig = CustodianModelConfig::query()->orderBy('id', 'desc')->first();
        $custodianModelConfigIdTest = $latestCustodianModelConfig ? $latestCustodianModelConfig->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$custodianModelConfigIdTest}",
                [
                    'active' => 0,
                ]
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_delete_a_custodian_config(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'decision_model_id' => 1,
                    'active' => 1,
                    'custodian_id' => $custodianId,
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response);
        $content = $response->decodeResponseJson();
        $this->assertNotNull($content['data']);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'DELETE',
                self::TEST_URL . '/' . $content['data']
            );

        $response->assertStatus(200);
        // $this->assertArrayHasKey('data', $response);
        $content = $response->decodeResponseJson();

        $this->assertNull($content['data']);
        $this->assertEquals($content['message'], 'success');
    }

    public function test_a_custodian_approver_cannot_delete_a_custodian_config(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        $config = CustodianModelConfig::where('custodian_id', $custodianId)->first();
        [$approver] = $this->makeCustodianUserActor($custodianId, 'CUSTODIAN_APPROVER');

        $response = $this->actingAsKeycloakUser($approver, $this->getMockedKeycloakPayload())
            ->actingAs($approver)
            ->json(
                'DELETE',
                self::TEST_URL . '/' . $config->id
            );

        $response->assertStatus(403);
    }

    public function test_the_application_cannot_delete_a_custodian_config(): void
    {
        $latestCustodianModelConfig = CustodianModelConfig::query()->orderBy('id', 'desc')->first();
        $custodianModelConfigIdTest = $latestCustodianModelConfig ? $latestCustodianModelConfig->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'DELETE',
                self::TEST_URL . "/{$custodianModelConfigIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_get_decision_models_for_specific_custodian(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'GET',
                "/api/v1/custodian_config/{$custodianId}/decision_models?decision_model_type=decision_models"
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
        $content = $response->decodeResponseJson();
        $this->assertNotNull($content['data']);
        $this->assertIsArray($content['data']);

        if (count($content['data']) > 0) {
            $this->assertArrayHasKey('id', $content['data'][0]);
            $this->assertArrayHasKey('name', $content['data'][0]);
            $this->assertArrayHasKey('description', $content['data'][0]);
            $this->assertArrayHasKey('active', $content['data'][0]);
        }
    }

    public function test_a_user_without_custodian_permissions_cannot_get_decision_models(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->actingAs($this->user)
            ->json(
                'GET',
                "/api/v1/custodian_config/{$custodianId}/decision_models?decision_model_type=decision_models"
            );

        $response->assertStatus(403);
    }

    public function test_the_application_cannot_get_decision_models_for_specific_custodian(): void
    {
        $latestCustodian = Custodian::query()->orderBy('id', 'desc')->first();
        $custodianIdTest = $latestCustodian ? $latestCustodian->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'GET',
                "/api/v1/custodian_config/{$custodianIdTest}/decision_models?decision_model_type=decision_models"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_returns_error_for_invalid_decision_model_type(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'GET',
                "/api/v1/custodian_config/{$custodianId}/decision_models?decision_model_type=invalid_type"
            );

        $response->assertStatus(400);
        $content = $response->decodeResponseJson();
        $this->assertEquals('Invalid argument(s)', $content['message']);
        $this->assertEquals('The selected decision model type is invalid.', $content['errors'][0]['message']);
    }

    public function test_the_application_requires_decision_model_type_parameter(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'GET',
                "/api/v1/custodian_config/{$custodianId}/decision_models"
            );

        $response->assertStatus(400);
        $content = $response->decodeResponseJson();
        $this->assertEquals('Invalid argument(s)', $content['message']);
        $this->assertEquals('The decision model type field is required.', $content['errors'][0]['message']);
    }

    public function test_the_application_returns_not_found_for_invalid_custodian_id(): void
    {
        $invalidCustodianId = 9999;

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'GET',
                "/api/v1/custodian_config/{$invalidCustodianId}/decision_models?decision_model_type=decision_models"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_update_decision_models_for_a_custodian(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        $config = CustodianModelConfig::where('custodian_id', $custodianId)->first();

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$custodianId}/decision_models",
                [
                    'configs' => [
                        [
                            'decision_model_id' => $config->decision_model_id,
                            'active' => false,
                        ],
                    ],
                ]
            );

        $response->assertStatus(200);
    }

    public function test_a_custodian_approver_cannot_update_decision_models_for_a_custodian(): void
    {
        $custodianId = CustodianUser::find($this->custodian_admin->custodian_user_id)->custodian_id;
        $config = CustodianModelConfig::where('custodian_id', $custodianId)->first();
        [$approver] = $this->makeCustodianUserActor($custodianId, 'CUSTODIAN_APPROVER');

        $response = $this->actingAsKeycloakUser($approver, $this->getMockedKeycloakPayload())
            ->actingAs($approver)
            ->json(
                'PUT',
                self::TEST_URL . "/{$custodianId}/decision_models",
                [
                    'configs' => [
                        [
                            'decision_model_id' => $config->decision_model_id,
                            'active' => false,
                        ],
                    ],
                ]
            );

        $response->assertStatus(403);
    }

    public function test_a_custodian_admin_cannot_update_decision_models_for_another_custodian(): void
    {
        $otherCustodian = Custodian::factory()->create();
        $otherConfig = CustodianModelConfig::factory()->create([
            'custodian_id' => $otherCustodian->id,
            'decision_model_id' => 1,
            'active' => 1,
        ]);

        $response = $this->actingAsKeycloakUser($this->custodian_admin, $this->getMockedKeycloakPayload())
            ->actingAs($this->custodian_admin)
            ->json(
                'PUT',
                self::TEST_URL . "/{$otherCustodian->id}/decision_models",
                [
                    'configs' => [
                        [
                            'decision_model_id' => $otherConfig->decision_model_id,
                            'active' => false,
                        ],
                    ],
                ]
            );

        $response->assertStatus(403);
    }
}
