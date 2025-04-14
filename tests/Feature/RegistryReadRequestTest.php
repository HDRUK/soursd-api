<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Custodian;
use App\Models\Registry;
use App\Models\RegistryReadRequest;
use KeycloakGuard\ActingAsKeycloakUser;
use Tests\TestCase;
use Tests\Traits\Authorisation;

class RegistryReadRequestTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;

    public const TEST_URL = '/api/v1/request_access';

    private $user = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('user_group', 'USERS')->first();
    }

    public function test_the_application_can_issue_registry_read_requests(): void
    {
        // $user = User::whereNotNull('registry_id')->where('user_group', User::GROUP_USERS)
        //     ->first();

        // $custodian = Custodian::inRandomOrder()->first();

        // $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
        //     ->json('POST', self::TEST_URL, [
        //         'custodian_identifier' => $custodian->calculated_hash,
        //         'digital_identifier' => Registry::where('id', $user->registry_id)->first()['digi_ident'],
        //     ]);

        // $response->assertStatus(201);
        // $this->assertArrayHasKey('data', $response);

        // $this->assertDatabaseHas('registry_read_requests', [
        //     'custodian_id' => $custodian->id,
        //     'registry_id' => $user->registry_id,
        //     'status' => RegistryReadRequest::READ_REQUEST_STATUS_OPEN,
        //     'approved_at' => null,
        //     'rejected_at' => null,
        // ]);
    }

    public function test_the_application_can_accept_registry_read_requests(): void
    {
        // $user = User::whereNotNull('registry_id')->where('user_group', User::GROUP_USERS)
        //     ->first();
        // $custodian = Custodian::inRandomOrder()->first();

        // $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
        //     ->json('POST', self::TEST_URL, [
        //         'custodian_identifier' => $custodian->calculated_hash,
        //         'digital_identifier' => Registry::where('id', $user->registry_id)->first()['digi_ident'],
        //     ]);

        // $req = RegistryReadRequest::where('status', RegistryReadRequest::READ_REQUEST_STATUS_OPEN)->first();
        // // Note the swap to an actual researcher for this test (this->user won't work in this instance)
        // $response = $this->actingAsKeycloakUser($user, $this->getMockedKeycloakPayload())
        //     ->json('PATCH', self::TEST_URL . '/' . $req->id, [
        //         'user_id' => $user->id,
        //         'status' => RegistryReadRequest::READ_REQUEST_STATUS_APPROVED,
        //     ]);

        // $response->assertStatus(200);
        // $this->assertDatabaseHas('registry_read_requests', [
        //     'id' => $req->id,
        //     'custodian_id' => $custodian->id,
        //     'registry_id' => $req->registry_id,
        //     'status' => RegistryReadRequest::READ_REQUEST_STATUS_APPROVED,
        // ]);
    }

    public function test_the_application_can_reject_registry_read_requests(): void
    {
        // $user = User::whereNotNull('registry_id')->where('user_group', User::GROUP_USERS)
        //     ->first();
        // $custodian = Custodian::inRandomOrder()->first();

        // $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
        //     ->json('POST', self::TEST_URL, [
        //         'custodian_identifier' => $custodian->calculated_hash,
        //         'digital_identifier' => Registry::where('id', $user->registry_id)->first()['digi_ident'],
        //     ]);

        // $req = RegistryReadRequest::where('status', RegistryReadRequest::READ_REQUEST_STATUS_OPEN)->first();
        // // Note the swap to an actual researcher for this test (this->user won't work in this instance)
        // $response = $this->actingAsKeycloakUser($user, $this->getMockedKeycloakPayload())
        //     ->json('PATCH', self::TEST_URL . '/' . $req->id, [
        //         'user_id' => $user->id,
        //         'status' => RegistryReadRequest::READ_REQUEST_STATUS_REJECTED,
        //     ]);

        // $response->assertStatus(200);
        // $this->assertDatabaseHas('registry_read_requests', [
        //     'id' => $req->id,
        //     'custodian_id' => $custodian->id,
        //     'registry_id' => $req->registry_id,
        //     'status' => RegistryReadRequest::READ_REQUEST_STATUS_REJECTED,
        // ]);
    }

    /* Will fix on my return
    public function test_the_application_rejects_unknown_custodians_requesting_access(): void
    {
        $user = User::whereNotNull('registry_id')->where('user_group', User::GROUP_USERS)
            ->first();

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json('POST', self::TEST_URL, [
                'custodian_identifier' => 'thisH4shdoesntexist123',
                'digital_identifier' => Registry::where('id', $user->registry_id)->first()['digi_ident'],
            ]);

        $response->assertStatus(404);
        $content = $response->decodeResponseJson();
        $this->assertEquals($content['message'], 'custodian_identifier not known');
        $this->assertNull($content['data']);
    }

    public function test_the_application_can_reject_anyone_accepting_requests_on_behalf_of_the_user(): void
    {
        $user = User::whereNotNull('registry_id')->where('user_group', User::GROUP_USERS)
            ->first();
        $custodian = Custodian::inRandomOrder()->first();

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json('POST', self::TEST_URL, [
                'custodian_identifier' => $custodian->calculated_hash,
                'digital_identifier' => Registry::where('id', $user->registry_id)->first()['digi_ident'],
            ]);

        $req = RegistryReadRequest::where('status', RegistryReadRequest::READ_REQUEST_STATUS_OPEN)->first();

        // Now change the user to another that doesn't own the read request
        $user2 = User::whereNotNull('registry_id')->where('user_group', User::GROUP_USERS)
            ->where('id', '!=', $user->id)
            ->first();

        // Note the swap to an actual researcher for this test (this->user won't work in this instance)
        $response = $this->actingAsKeycloakUser($user2, $this->getMockedKeycloakPayload())
            ->json('PATCH', self::TEST_URL . '/' . $req->id, [
                'user_id' => $user2->id,
                'status' => RegistryReadRequest::READ_REQUEST_STATUS_REJECTED,
            ]);

        $response->assertStatus(403);
        $content = $response->decodeResponseJson();
        $this->assertEquals($content['message'], 'you don\'t have access to this record');
        $this->assertNull($content['data']);
    }*/
}
