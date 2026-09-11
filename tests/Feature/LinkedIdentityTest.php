<?php

namespace Tests\Feature;

use Http;
use Keycloak;
use Tests\TestCase;
use Laravel\Pennant\Feature;
use App\Models\UserIdentity;
use Tests\Traits\Authorisation;

class LinkedIdentityTest extends TestCase
{
    use Authorisation;

    public const TEST_URL = '/api/v1/linked_identities';

    public function setUp(): void
    {
        parent::setUp();
        $this->withUsers();

        $this->user->update([
            'keycloak_id' => 'keycloak-user-123',
        ]);

        Http::fake([
            '*' => Http::response([
                'access_token' => 'fake-access-token',
                'token_type' => 'Bearer',
                'expires_in' => 300,
            ], 200),
        ]);
    }

    public function test_the_application_can_list_linked_identities_and_available_providers(): void
    {
        UserIdentity::factory()->create([
            'user_id' => $this->user->id,
            'provider' => 'orcid',
            'provider_user_id' => '0000-0002-7193-5482',
        ]);

        $response = $this->actingAs($this->user)
            ->json('GET', self::TEST_URL);

        $response->assertStatus(200);
        $data = $response->decodeResponseJson()['data'];

        $this->assertCount(1, $data['linked']);
        $this->assertEquals('orcid', $data['linked'][0]['provider']);

        $providerKeys = collect($data['providers'])->pluck('key')->all();
        $this->assertContains('orcid', $providerKeys);
        $this->assertContains('github', $providerKeys);

        $orcidProvider = collect($data['providers'])->firstWhere('key', 'orcid');
        $this->assertTrue($orcidProvider['linked']);

        $githubProvider = collect($data['providers'])->firstWhere('key', 'github');
        $this->assertFalse($githubProvider['linked']);
    }

    public function test_the_application_can_sync_a_provider_that_keycloak_has_linked(): void
    {
        Keycloak::shouldReceive('getFederatedIdentities')
            ->once()
            ->with('fake-access-token', 'keycloak-user-123')
            ->andReturn([
                [
                    'identityProvider' => 'github',
                    'userId' => '12345',
                    'userName' => 'octocat',
                ],
            ]);

        $response = $this->actingAs($this->user)
            ->json('POST', self::TEST_URL . '/github');

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_identities', [
            'user_id' => $this->user->id,
            'provider' => 'github',
            'provider_user_id' => '12345',
            'provider_username' => 'octocat',
        ]);
    }

    public function test_syncing_a_provider_keycloak_has_not_linked_returns_not_found(): void
    {
        Keycloak::shouldReceive('getFederatedIdentities')
            ->once()
            ->with('fake-access-token', 'keycloak-user-123')
            ->andReturn([]);

        $response = $this->actingAs($this->user)
            ->json('POST', self::TEST_URL . '/github');

        $response->assertStatus(404);
    }

    public function test_syncing_an_unsupported_provider_returns_unprocessable(): void
    {
        $response = $this->actingAs($this->user)
            ->json('POST', self::TEST_URL . '/google');

        $response->assertStatus(422);
    }

    public function test_the_application_can_unlink_a_provider(): void
    {
        UserIdentity::factory()->create([
            'user_id' => $this->user->id,
            'provider' => 'orcid',
            'provider_user_id' => '0000-0002-7193-5482',
        ]);

        Keycloak::shouldReceive('removeFederatedIdentity')
            ->once()
            ->with('fake-access-token', 'keycloak-user-123', 'orcid')
            ->andReturn(true);

        $response = $this->actingAs($this->user)
            ->json('DELETE', self::TEST_URL . '/orcid');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_identities', [
            'user_id' => $this->user->id,
            'provider' => 'orcid',
        ]);
    }

    public function test_unlinking_a_provider_that_is_not_linked_returns_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->json('DELETE', self::TEST_URL . '/orcid');

        $response->assertStatus(404);
    }

    public function test_non_researcher_users_cannot_list_linked_identities(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json('GET', self::TEST_URL);

        $response->assertStatus(403);
    }

    public function test_non_researcher_users_cannot_sync_a_provider(): void
    {
        $response = $this->actingAs($this->organisation_admin)
            ->json('POST', self::TEST_URL . '/github');

        $response->assertStatus(403);
    }

    public function test_non_researcher_users_cannot_unlink_a_provider(): void
    {
        $response = $this->actingAs($this->custodian_admin)
            ->json('DELETE', self::TEST_URL . '/orcid');

        $response->assertStatus(403);
    }

    public function test_endpoints_are_not_found_when_the_feature_flag_is_disabled(): void
    {
        Feature::deactivate('LinkedIdentitiesEnabled');

        $this->actingAs($this->user)
            ->json('GET', self::TEST_URL)
            ->assertStatus(404);

        $this->actingAs($this->user)
            ->json('POST', self::TEST_URL . '/github')
            ->assertStatus(404);

        $this->actingAs($this->user)
            ->json('DELETE', self::TEST_URL . '/orcid')
            ->assertStatus(404);

        Feature::activate('LinkedIdentitiesEnabled');
    }
}
