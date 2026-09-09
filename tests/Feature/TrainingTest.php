<?php

namespace Tests\Feature;

use App\Models\File;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Registry;
use App\Models\Training;
use Tests\Traits\Authorisation;
use KeycloakGuard\ActingAsKeycloakUser;

class TrainingTest extends TestCase
{
    use Authorisation;
    use ActingAsKeycloakUser;

    public const TEST_URL = '/api/v1/training';

    public function setUp(): void
    {
        parent::setUp();
        $this->withUsers();
    }

    public function test_the_application_can_list_training(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_the_application_can_list_training_by_registry_id(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . '/registry/' . $this->user->registry_id
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
        $this->assertNotNull($response->decodeResponseJson()['data']);
    }

    public function test_the_application_cannot_list_training_by_registry_id(): void
    {
        $latestRegistry = Registry::query()->orderBy('id', 'desc')->first();
        $registryIdTest = $latestRegistry ? $latestRegistry->id + 1 : 1;

        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . "/registry/{$registryIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_cannot_list_training_by_registry_id_they_dont_own(): void
    {
        $otherRegistry = Registry::where('id', '!=', $this->user->registry_id)->first();

        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . "/registry/{$otherRegistry->id}"
            );

        $response->assertStatus(403);
    }

    public function test_the_application_cannot_list_training_linked_to_file(): void
    {
        $latestTraining = Training::query()->orderBy('id', 'desc')->first();
        $trainingIdTest = $latestTraining ? $latestTraining->id + 1 : 1;

        $latestFile = File::query()->orderBy('id', 'desc')->first();
        $fileIdTest = $latestFile ? $latestFile->id + 1 : 1;

        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                self::TEST_URL . "/{$trainingIdTest}/link_file/{$fileIdTest}"
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_show_training(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . '/1'
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
    }


    public function test_the_application_cannot_show_training(): void
    {
        $latestTraining = Training::query()->orderBy('id', 'desc')->first();
        $trainingIdTest = $latestTraining ? $latestTraining->id + 1 : 1;

        $response = $this->actingAs($this->user)
            ->json(
                'GET',
                self::TEST_URL . '/' . $trainingIdTest
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_create_training(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'provider' => 'Fake Training Provider',
                    'awarded_at' => Carbon::now(),
                    'expires_at' => Carbon::now()->addYears(5),
                    'expires_in_years' => 5,
                    'training_name' => 'Completely made up Researcher Training',
                    'certification_id' => null,
                    'pro_registration' => fake()->randomElement([0, 1]),
                    'registry_id' => $this->user->registry_id,
                ]
            );

        $response->assertStatus(201);
        $content = $response->decodeResponseJson()['data'];

        $this->assertArrayHasKey('data', $response);
        $this->assertGreaterThan(0, $content);

        $this->assertDatabaseHas('registry_has_trainings', [
            'training_id' => $content,
            'registry_id' => $this->user->registry_id,
        ]);
    }

    public function test_the_application_can_update_training(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'provider' => 'Fake Training Provider',
                    'awarded_at' => Carbon::now(),
                    'expires_at' => Carbon::now()->addYears(5),
                    'expires_in_years' => 5,
                    'training_name' => 'Completely made up Researcher Training',
                    'certification_id' => 1,
                    'pro_registration' => 0,
                    'registry_id' => $this->user->registry_id,
                ]
            );

        $response->assertStatus(201);
        $content = $response->decodeResponseJson()['data'];

        $this->assertArrayHasKey('data', $response);
        $this->assertGreaterThan(0, $content);

        $response = $this->actingAs($this->user)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $content,
                [
                    'provider' => 'Fake Training Provider 2',
                    'awarded_at' => Carbon::now(),
                    'expires_at' => Carbon::now()->addYears(8),
                    'expires_in_years' => 8,
                    'training_name' => 'Completely made up Researcher Training v2',
                    'certification_id' => 1,
                    'pro_registration' => 1,
                ]
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);

        $content = $response->decodeResponseJson()['data'];

        $this->assertEquals($content['provider'], 'Fake Training Provider 2');
        $this->assertEquals($content['expires_in_years'], 8);
        $this->assertEquals($content['training_name'], 'Completely made up Researcher Training v2');
        $this->assertEquals($content['pro_registration'], 1);
    }

    public function test_the_application_cannot_update_training(): void
    {
        $latestTraining = Training::query()->orderBy('id', 'desc')->first();
        $trainingIdTest = $latestTraining ? $latestTraining->id + 1 : 1;

        $response = $this->actingAs($this->user)
            ->json(
                'PUT',
                self::TEST_URL . '/' . $trainingIdTest,
                [
                    'provider' => 'Fake Training Provider 2',
                    'awarded_at' => Carbon::now(),
                    'expires_at' => Carbon::now()->addYears(8),
                    'expires_in_years' => 8,
                    'training_name' => 'Completely made up Researcher Training v2',
                    'certification_id' => 1,
                    'pro_registration' => 1,
                ]
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }

    public function test_the_application_can_delete_training(): void
    {
        $response = $this->actingAs($this->user)
            ->json(
                'POST',
                self::TEST_URL,
                [
                    'provider' => 'Fake Training Provider',
                    'awarded_at' => Carbon::now(),
                    'expires_at' => Carbon::now()->addYears(5),
                    'expires_in_years' => 5,
                    'training_name' => 'Pointless Training that will be Deleted',
                    'certification_id' => null,
                    'pro_registration' => 0,
                    'registry_id' => $this->user->registry_id,
                ]
            );

        $response->assertStatus(201);
        $content = $response->decodeResponseJson()['data'];

        $this->assertArrayHasKey('data', $response);
        $this->assertGreaterThan(0, $content);

        $response = $this->actingAs($this->user)
            ->json(
                'DELETE',
                self::TEST_URL . '/' . $content,
            );

        $response->assertStatus(200);
    }

    public function test_the_application_cannot_delete_training(): void
    {
        $latestTraining = Training::query()->orderBy('id', 'desc')->first();
        $trainingIdTest = $latestTraining ? $latestTraining->id + 1 : 1;

        $response = $this->actingAs($this->user)
            ->json(
                'DELETE',
                self::TEST_URL . '/' . $trainingIdTest,
            );

        $response->assertStatus(400);
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals('Invalid argument(s)', $message);
    }
}
