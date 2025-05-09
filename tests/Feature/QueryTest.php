<?php

namespace Tests\Feature;

use KeycloakGuard\ActingAsKeycloakUser;
use App\Models\Registry;
use App\Models\Custodian;
use Tests\TestCase;
use Tests\Traits\Authorisation;
use App\Http\Traits\HmacSigning;
use App\Models\RegistryReadRequest;

class QueryTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;
    use HmacSigning;

    public const TEST_URL = '/api/v1/query';

    public function setUp(): void
    {
        parent::setUp();
        $this->withUsers();
    }

    public function test_the_application_can_query_the_system(): void
    {
        $custodian = Custodian::where('id', 1)->first();
        $rrr = RegistryReadRequest::create([
            'custodian_id' => $custodian->id,
            'registry_id' => $this->user->registry_id,
            'status' => RegistryReadRequest::READ_REQUEST_STATUS_APPROVED,
        ]);

        $registry = Registry::where('id', $this->user->registry_id)->first();

        $payload = [
            'ident' => $registry->digi_ident,
        ];

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'POST',
                self::TEST_URL,
                $payload,
                [
                    'x-client-id' => $custodian->client_id,
                    'x-signature' => $this->generateSignature(json_encode($payload), $custodian->unique_identifier),
                ]
            );

        $response->assertStatus(200);
        $content = $response->decodeResponseJson()['data'];

        $this->assertNotNull($content['user']);
        $this->assertNotNull($content['user']['identity']);
        $this->assertEquals(
            $content['user']['identity']['registry_id'],
            $content['user']['registry_id']
        );
        $this->assertNotNull($content['registry']);
        $this->assertNotNull($content['registry']['training']);
        // LS - Haven't added history to demo data - removed for now
        //$this->assertNotNull($content['registry']['history']);
    }

    public function test_the_application_cant_query_the_system_with_no_read_request(): void
    {
        $custodian = Custodian::where('id', 1)->first();
        $registry = Registry::where('id', $this->user->registry_id)->first();

        $payload = [
            'ident' => $registry->digi_ident,
        ];

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'POST',
                self::TEST_URL,
                $payload,
                [
                    'x-client-id' => $custodian->client_id,
                    'x-signature' => $this->generateSignature(json_encode($payload), $custodian->unique_identifier),
                ]
            );

        $response->assertStatus(401);
        $this->assertEquals(
            'no user approved read request found',
            $response->decodeResponseJson()['message']
        );
    }

    public function test_the_application_cant_query_the_systm_with_a_rejected_read_request(): void
    {
        $custodian = Custodian::where('id', 1)->first();
        $rrr = RegistryReadRequest::create([
            'custodian_id' => $custodian->id,
            'registry_id' => $this->user->registry_id,
            'status' => RegistryReadRequest::READ_REQUEST_STATUS_REJECTED,
        ]);

        $registry = Registry::where('id', $this->user->registry_id)->first();

        $payload = [
            'ident' => $registry->digi_ident,
        ];

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'POST',
                self::TEST_URL,
                $payload,
                [
                    'x-client-id' => $custodian->client_id,
                    'x-signature' => $this->generateSignature(json_encode($payload), $custodian->unique_identifier),
                ]
            );

        $response->assertStatus(401);
        $this->assertEquals(
            'no user approved read request found',
            $response->decodeResponseJson()['message']
        );
    }

    public function test_the_application_cant_query_the_system_with_an_open_read_request(): void
    {
        $custodian = Custodian::where('id', 1)->first();
        $rrr = RegistryReadRequest::create([
            'custodian_id' => $custodian->id,
            'registry_id' => $this->user->registry_id,
            'status' => RegistryReadRequest::READ_REQUEST_STATUS_OPEN,
        ]);

        $registry = Registry::where('id', $this->user->registry_id)->first();

        $payload = [
            'ident' => $registry->digi_ident,
        ];

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'POST',
                self::TEST_URL,
                $payload,
                [
                    'x-client-id' => $custodian->client_id,
                    'x-signature' => $this->generateSignature(json_encode($payload), $custodian->unique_identifier),
                ]
            );

        $response->assertStatus(401);
        $this->assertEquals(
            'no user approved read request found',
            $response->decodeResponseJson()['message']
        );
    }
}
