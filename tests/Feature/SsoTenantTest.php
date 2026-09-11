<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SsoTenant;
use App\Models\SsoTenantDomain;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Tests\Traits\Authorisation;
use Illuminate\Support\Facades\Http;
use KeycloakGuard\ActingAsKeycloakUser;

class SsoTenantTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;

    public const TEST_URL = '/api/v1/sso_tenants';

    private User $ssoAdmin;
    private User $ssoCustodian;
    private User $ssoOrgAdmin;
    private User $ssoOrgDelegate;
    private User $ssoNonAdmin;

    public function setUp(): void
    {
        parent::setUp();
        $this->withMiddleware();

        Feature::activate('EnterpriseSAMLSSOEnabled');

        $this->ssoAdmin = User::factory()->create([
            'user_group' => User::GROUP_ADMINS,
            'keycloak_id' => (string) Str::uuid(),
        ]);

        $this->ssoCustodian = User::factory()->create([
            'user_group' => User::GROUP_CUSTODIANS,
            'keycloak_id' => (string) Str::uuid(),
        ]);

        $this->ssoOrgAdmin = User::factory()->create([
            'user_group' => User::GROUP_ORGANISATIONS,
            'is_delegate' => 0,
            'keycloak_id' => (string) Str::uuid(),
        ]);

        $this->ssoOrgDelegate = User::factory()->create([
            'user_group' => User::GROUP_ORGANISATIONS,
            'is_delegate' => 1,
            'keycloak_id' => (string) Str::uuid(),
        ]);

        $this->ssoNonAdmin = User::factory()->create([
            'user_group' => User::GROUP_USERS,
            'keycloak_id' => (string) Str::uuid(),
        ]);

        // Closures, not literal Response instances - Http::fake() reuses the
        // same object for every match on a literal, and these endpoints are
        // hit multiple times per approval (once per Keycloak admin call
        // needing a fresh service token); our code always calls
        // $response->close(), which detaches a shared object's stream for
        // its second match.
        Http::fake([
            '*/identity-provider/import-config' => fn () => Http::response([
                'entityId' => 'https://sso.acme.example/adfs/services/trust',
            ], 200),
            '*/identity-provider/instances/*/mappers' => fn () => Http::response([], 201),
            '*/identity-provider/instances/*' => fn () => Http::response([], 204),
            '*/identity-provider/instances' => fn () => Http::response([], 201),
            '*/protocol/openid-connect/token' => fn () => Http::response(['access_token' => 'fake-service-token'], 200),
        ]);
    }

    public function test_regular_user_cannot_submit_a_request(): void
    {
        $response = $this->actingAs($this->ssoNonAdmin)->json('POST', self::TEST_URL, [
            'name' => 'Acme Corp',
            'metadata_url' => 'https://sso.acme.example/federationmetadata.xml',
            'domains' => ['acme.com'],
        ]);

        $response->assertStatus(403);
    }

    public function test_org_delegate_cannot_submit_a_request(): void
    {
        $response = $this->actingAs($this->ssoOrgDelegate)->json('POST', self::TEST_URL, [
            'name' => 'Acme Corp',
            'metadata_url' => 'https://sso.acme.example/federationmetadata.xml',
            'domains' => ['acme.com'],
        ]);

        $response->assertStatus(403);
    }

    public function test_custodian_can_submit_a_pending_request_without_touching_keycloak(): void
    {
        $response = $this->actingAs($this->ssoCustodian)->json('POST', self::TEST_URL, [
            'name' => 'Acme Corp',
            'metadata_url' => 'https://sso.acme.example/federationmetadata.xml',
            'domains' => ['acme.com'],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.enabled', false)
            ->assertJsonPath('data.idp_alias', null);

        Http::assertNothingSent();
    }

    public function test_org_admin_can_submit_a_pending_request(): void
    {
        $response = $this->actingAs($this->ssoOrgAdmin)->json('POST', self::TEST_URL, [
            'name' => 'Acme Corp',
            'metadata_url' => 'https://sso.acme.example/federationmetadata.xml',
            'domains' => ['acme.com'],
        ]);

        $response->assertStatus(201)->assertJsonPath('data.status', 'pending');
    }

    public function test_submitter_only_sees_their_own_requests(): void
    {
        $mine = SsoTenant::factory()->create(['submitted_by_user_id' => $this->ssoCustodian->id]);
        SsoTenant::factory()->create(['submitted_by_user_id' => $this->ssoOrgAdmin->id]);

        $response = $this->actingAs($this->ssoCustodian)->json('GET', self::TEST_URL);

        $response->assertStatus(200);
        $ids = collect($response->json('data.data'))->pluck('id')->all();

        $this->assertEquals([$mine->id], $ids);
    }

    public function test_admin_sees_all_requests(): void
    {
        SsoTenant::factory()->create(['submitted_by_user_id' => $this->ssoCustodian->id]);
        SsoTenant::factory()->create(['submitted_by_user_id' => $this->ssoOrgAdmin->id]);

        $response = $this->actingAs($this->ssoAdmin)->json('GET', self::TEST_URL);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
    }

    public function test_custodian_cannot_approve_their_own_request(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['submitted_by_user_id' => $this->ssoCustodian->id]);

        $response = $this->actingAs($this->ssoCustodian)
            ->json('POST', self::TEST_URL . "/{$ssoTenant->id}/approve");

        $response->assertStatus(403);
    }

    public function test_admin_can_approve_a_pending_request(): void
    {
        $ssoTenant = SsoTenant::factory()->create([
            'submitted_by_user_id' => $this->ssoCustodian->id,
            'metadata_url' => 'https://sso.acme.example/federationmetadata.xml',
            'status' => SsoTenant::STATUS_PENDING,
            'idp_alias' => null,
            'enabled' => false,
        ]);
        SsoTenantDomain::create(['sso_tenant_id' => $ssoTenant->id, 'domain' => 'acme.com']);

        $response = $this->actingAs($this->ssoAdmin)
            ->json('POST', self::TEST_URL . "/{$ssoTenant->id}/approve");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.enabled', true)
            ->assertJsonPath('data.entity_id', 'https://sso.acme.example/adfs/services/trust');

        $refreshed = $ssoTenant->fresh();
        $this->assertNotNull($refreshed->idp_alias);
        $this->assertSame(
            config('speedi.system.keycloak_base_url') . '/realms/' . config('speedi.system.keycloak_realm'),
            $refreshed->sp_entity_id
        );
        $this->assertSame($refreshed->sp_entity_id . '/broker/' . $refreshed->idp_alias . '/endpoint', $refreshed->sp_acs_url);
        $this->assertSame($refreshed->sp_acs_url . '/descriptor', $refreshed->sp_metadata_url);
    }

    public function test_sp_urls_are_null_until_approved(): void
    {
        $ssoTenant = SsoTenant::factory()->create([
            'status' => SsoTenant::STATUS_PENDING,
            'idp_alias' => null,
        ]);

        $this->assertNull($ssoTenant->sp_entity_id);
        $this->assertNull($ssoTenant->sp_acs_url);
        $this->assertNull($ssoTenant->sp_metadata_url);
    }

    public function test_admin_can_reject_a_pending_request_without_touching_keycloak(): void
    {
        $ssoTenant = SsoTenant::factory()->create([
            'submitted_by_user_id' => $this->ssoCustodian->id,
            'status' => SsoTenant::STATUS_PENDING,
            'idp_alias' => null,
            'enabled' => false,
        ]);

        $response = $this->actingAs($this->ssoAdmin)
            ->json('POST', self::TEST_URL . "/{$ssoTenant->id}/reject", ['reason' => 'Not a real domain']);

        $response->assertStatus(200)->assertJsonPath('data.status', 'rejected');
        Http::assertNothingSent();
    }

    public function test_cannot_approve_an_already_approved_request(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['status' => SsoTenant::STATUS_APPROVED]);

        $response = $this->actingAs($this->ssoAdmin)
            ->json('POST', self::TEST_URL . "/{$ssoTenant->id}/approve");

        $response->assertStatus(409);
    }

    public function test_pending_tenant_domain_does_not_match_in_lookup(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['status' => SsoTenant::STATUS_PENDING, 'enabled' => false]);
        SsoTenantDomain::create(['sso_tenant_id' => $ssoTenant->id, 'domain' => 'acme.com']);

        $response = $this->json('POST', '/api/v1/sso/lookup', ['email' => 'jane@acme.com']);

        $response->assertStatus(200)->assertJson(['data' => ['matched' => false]]);
    }

    public function test_approved_tenant_domain_matches_in_lookup(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['status' => SsoTenant::STATUS_APPROVED, 'enabled' => true]);
        SsoTenantDomain::create(['sso_tenant_id' => $ssoTenant->id, 'domain' => 'acme.com']);

        $response = $this->json('POST', '/api/v1/sso/lookup', ['email' => 'jane@acme.com']);

        $response->assertStatus(200)
            ->assertJson(['data' => ['matched' => true, 'idp_alias' => $ssoTenant->idp_alias]]);
    }

    public function test_a_second_tenant_cannot_claim_an_already_routed_domain(): void
    {
        $ssoTenant = SsoTenant::factory()->create();
        SsoTenantDomain::create(['sso_tenant_id' => $ssoTenant->id, 'domain' => 'acme.com']);

        $response = $this->actingAs($this->ssoCustodian)->json('POST', self::TEST_URL, [
            'name' => 'Definitely Not Acme',
            'metadata_url' => 'https://sso.notacme.example/federationmetadata.xml',
            'domains' => ['acme.com'],
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('sso_tenants', ['name' => 'Definitely Not Acme']);
    }

    public function test_admin_can_disable_and_re_enable_an_approved_tenant(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['status' => SsoTenant::STATUS_APPROVED, 'enabled' => true]);

        $disable = $this->actingAs($this->ssoAdmin)->json('DELETE', self::TEST_URL . "/{$ssoTenant->id}");
        $disable->assertStatus(200)->assertJsonPath('data.enabled', false);

        $enable = $this->actingAs($this->ssoAdmin)
            ->json('POST', self::TEST_URL . "/{$ssoTenant->id}/enable");
        $enable->assertStatus(200)->assertJsonPath('data.enabled', true);
    }

    public function test_cannot_disable_a_pending_tenant(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['status' => SsoTenant::STATUS_PENDING, 'enabled' => false]);

        $response = $this->actingAs($this->ssoAdmin)->json('DELETE', self::TEST_URL . "/{$ssoTenant->id}");

        $response->assertStatus(409);
    }

    public function test_admin_can_purge_an_approved_tenant_including_its_domains(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['status' => SsoTenant::STATUS_APPROVED, 'idp_alias' => 'acme-corp']);
        SsoTenantDomain::create(['sso_tenant_id' => $ssoTenant->id, 'domain' => 'acme.com']);

        $response = $this->actingAs($this->ssoAdmin)
            ->json('DELETE', self::TEST_URL . "/{$ssoTenant->id}/purge");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sso_tenants', ['id' => $ssoTenant->id]);
        $this->assertDatabaseMissing('sso_tenant_domains', ['sso_tenant_id' => $ssoTenant->id]);
    }

    public function test_admin_can_purge_a_pending_tenant_without_touching_keycloak(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['status' => SsoTenant::STATUS_PENDING, 'idp_alias' => null]);

        $response = $this->actingAs($this->ssoAdmin)
            ->json('DELETE', self::TEST_URL . "/{$ssoTenant->id}/purge");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sso_tenants', ['id' => $ssoTenant->id]);
    }

    public function test_non_admin_cannot_purge_a_tenant(): void
    {
        $ssoTenant = SsoTenant::factory()->create(['submitted_by_user_id' => $this->ssoCustodian->id]);

        $response = $this->actingAs($this->ssoCustodian)
            ->json('DELETE', self::TEST_URL . "/{$ssoTenant->id}/purge");

        $response->assertStatus(403);
        $this->assertDatabaseHas('sso_tenants', ['id' => $ssoTenant->id]);
    }

    public function test_purging_a_domain_frees_it_for_a_new_submission(): void
    {
        $ssoTenant = SsoTenant::factory()->create();
        SsoTenantDomain::create(['sso_tenant_id' => $ssoTenant->id, 'domain' => 'acme.com']);

        $this->actingAs($this->ssoAdmin)->json('DELETE', self::TEST_URL . "/{$ssoTenant->id}/purge");

        $response = $this->actingAs($this->ssoCustodian)->json('POST', self::TEST_URL, [
            'name' => 'Acme Corp Take Two',
            'metadata_url' => 'https://sso.acme.example/federationmetadata.xml',
            'domains' => ['acme.com'],
        ]);

        $response->assertStatus(201);
    }

    public function test_endpoints_are_blocked_when_the_feature_flag_is_disabled(): void
    {
        Feature::deactivate('EnterpriseSAMLSSOEnabled');

        $this->actingAs($this->ssoAdmin)->json('GET', self::TEST_URL)->assertStatus(400);

        $this->json('POST', '/api/v1/sso/lookup', ['email' => 'jane@acme.com'])
            ->assertStatus(400);
    }
}
