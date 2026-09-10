<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Registry;
use App\Models\Affiliation;
use App\Models\Organisation;
use App\Traits\CommonFunctions;
use Tests\Traits\Authorisation;
use KeycloakGuard\ActingAsKeycloakUser;
use Illuminate\Support\Facades\Gate;

class AffiliationTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;
    use CommonFunctions;

    public const TEST_URL = '/api/v1/affiliations';

    public function setUp(): void
    {
        parent::setUp();
        $this->withUsers();
    }

    public function test_the_application_can_show_affiliations_by_registry_id(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_cannot_show_affiliations_by_registry_id(): void
    {
        $latestRegistry = Registry::query()->orderBy('id', 'desc')->first();
        $registryIdTest = $latestRegistry ? $latestRegistry->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'GET',
                self::TEST_URL . "/{$registryIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_show_organisation_affiliation(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . '/1/organisation/1'
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_cannot_show_organisation_affiliation(): void
    {
        $latestRegistry = Registry::query()->orderBy('id', 'desc')->first();
        $registryIdTest = $latestRegistry ? $latestRegistry->id + 1 : 1;

        $latestOrganisation = Organisation::query()->orderBy('id', 'desc')->first();
        $organisationIdTest = $latestOrganisation ? $latestOrganisation->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'GET',
                self::TEST_URL . "/{$registryIdTest}/organisation/{$organisationIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_create_an_affiliation_by_registry_id(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                self::TEST_URL . '/1',
                [
                    'organisation_id' => 1,
                    'member_id' => fake()->uuid(),
                    'relationship' => 'employee',
                    'from' => Carbon::now()->subYears(5)->toDateString(),
                    'to' => '',
                    'department' => 'Research',
                    'role' => 'Researcher',
                    'email' => fake()->email(),
                    'ror' => generateRorID(),
                    'registry_id' => 1,
                    'current_employer' => false,
                ]
            );

        $response->assertStatus(201);

        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_cannot_create_an_affiliation_by_registry_id(): void
    {
        $latestRegistry = Registry::query()->orderBy('id', 'desc')->first();
        $registryIdTest = $latestRegistry ? $latestRegistry->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'POST',
                self::TEST_URL . "/{$registryIdTest}",
                [
                    'organisation_id' => 1,
                    'member_id' => fake()->uuid(),
                    'relationship' => 'employee',
                    'from' => Carbon::now()->subYears(5)->toDateString(),
                    'to' => '',
                    'department' => 'Research',
                    'role' => 'Researcher',
                    'email' => fake()->email(),
                    'ror' => generateRorID(),
                    'registry_id' => 1,
                    'current_employer' => false,
                ]
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_resend_verification_email_when_user_not_authorised(): void
    {
        Gate::shouldReceive('allows')
            ->once()
            ->andReturn(false);

        $affiliation = Affiliation::factory()->create([
            'current_employer' => true,
            'is_verified' => false,
        ]);

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'GET',
                self::TEST_URL . "/{$affiliation->id}/resend/verification"
            );

        $response->assertStatus(403);

        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('forbidden', $message);
    }

    public function test_the_application_can_update_an_affiliation(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'PUT',
                self::TEST_URL . '/1',
                [
                    'member_id' => 'A1234567',
                    'organisation_id' => 1,
                    'current_employer' => 1,
                    'relationship' => 'employee'
                ]
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_cannot_update_an_affiliation(): void
    {
        $latestAffiliation = Affiliation::query()->orderBy('id', 'desc')->first();
        $affiliationIdTest = $latestAffiliation ? $latestAffiliation->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                self::TEST_URL . "/{$affiliationIdTest}",
                [
                    'member_id' => 'A1234567',
                    'organisation_id' => 1,
                    'current_employer' => 1,
                    'relationship' => 'employee'
                ]
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_update_an_affiliation_they_dont_own(): void
    {
        $otherAffiliation = Affiliation::where('registry_id', '!=', $this->user->registry_id)->first();

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                self::TEST_URL . "/{$otherAffiliation->id}",
                [
                    'member_id' => 'A1234567',
                    'organisation_id' => $otherAffiliation->organisation_id,
                    'current_employer' => 1,
                    'relationship' => 'employee'
                ]
            );

        $response->assertStatus(403);
    }

    public function test_the_application_can_delete_an_affiliation(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'DELETE',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(200);
    }

    public function test_the_application_cannot_delete_an_affiliation(): void
    {
        $latestAffiliation = Affiliation::query()->orderBy('id', 'desc')->first();
        $affiliationIdTest = $latestAffiliation ? $latestAffiliation->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'DELETE',
                self::TEST_URL . "/{$affiliationIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_delete_an_affiliation_they_dont_own(): void
    {
        $otherAffiliation = Affiliation::where('registry_id', '!=', $this->user->registry_id)->first();

        $response = $this->actingAs($this->user)
            ->json(
                'DELETE',
                self::TEST_URL . "/{$otherAffiliation->id}"
            );

        $response->assertStatus(403);
    }

    public function test_the_application_cannot_update_registry_affiliations_when_not_the_employing_organisation(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'PUT',
                self::TEST_URL . '/1/affiliation/1?status=approved'
            );

        $response->assertStatus(403);
    }

    public function test_the_application_can_update_registry_affiliations(): void
    {
        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                self::TEST_URL . '/1/affiliation/1'
            );
        $response->assertStatus(500);

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/1/affiliation/1?status=approved'
            );
        $response->assertStatus(200);

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/1/affiliation/1?status=rejected'
            );
        $response->assertStatus(200);

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/1/affiliation/1?status=rejected'
            );
        $response->assertStatus(config('workflow.transitions.enforced') ? 500 : 200);

        $response = $this->actingAs($this->organisation_admin)
            ->json(
                'PUT',
                self::TEST_URL . '/1/affiliation/999?status=rejected'
            );
        $response->assertStatus(400);
    }

    public function test_the_application_cannot_update_registry_affiliations(): void
    {
        $latestAffiliation = Affiliation::query()->orderBy('id', 'desc')->first();
        $affiliationIdTest = $latestAffiliation ? $latestAffiliation->id + 1 : 1;

        $latestRegistry = Registry::query()->orderBy('id', 'desc')->first();
        $registryIdTest = $latestRegistry ? $latestRegistry->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                self::TEST_URL . "/{$registryIdTest}/affiliation/{$latestAffiliation}"
            );
        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                self::TEST_URL . "/{$registryIdTest}/affiliation/{$latestAffiliation}"
            );
        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                self::TEST_URL . "/{$registryIdTest}/affiliation/{$latestAffiliation}"
            );
        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                self::TEST_URL . "/{$registryIdTest}/affiliation/{$latestAffiliation}"
            );
        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);


        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'PUT',
                self::TEST_URL . "/{$registryIdTest}/affiliation/{$latestAffiliation}"
            );
        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_send_verification_email(): void
    {
        $latestAffiliation = Affiliation::query()->orderBy('id', 'desc')->first();
        $affiliationIdTest = $latestAffiliation ? $latestAffiliation->id + 1 : 1;

        $response = $this->actingAsKeycloakUser($this->user, $this->getMockedKeycloakPayload())
            ->json(
                'GET',
                self::TEST_URL . "/{$affiliationIdTest}/resend/verification"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }
}
