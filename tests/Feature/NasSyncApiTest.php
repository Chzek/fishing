<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NasSyncApiTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function non_admin_cannot_access_sync_api()
    {
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE]);

        $pushResponse = $this->actingAs($user)->postJson('/api/v1/sync/push', []);
        $pushResponse->assertStatus(403);

        $pullResponse = $this->actingAs($user)->getJson('/api/v1/sync/pull');
        $pullResponse->assertStatus(403);
    }

    #[Test]
    public function admin_can_push_outbox_data_and_resolve_via_last_write_wins()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $lakeUuid = '11111111-2222-3333-4444-555555555555';

        $payload = [
            'lakes' => [
                [
                    'uuid' => $lakeUuid,
                    'name' => 'Remote NAS Lake',
                    'latitude' => 46.5,
                    'longitude' => -79.2,
                    'updated_at' => '2026-08-09T12:00:00Z',
                ]
            ]
        ];

        $response = $this->actingAs($admin)->postJson('/api/v1/sync/push', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('processed_count', 1);

        $this->assertDatabaseHas('lakes', [
            'id' => $lakeUuid,
            'name' => 'Remote NAS Lake',
            'sync_status' => 'synced',
        ]);
    }

    #[Test]
    public function admin_can_pull_updated_models_since_timestamp_and_mark_served_as_synced()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $lake = Lake::create([
            'name' => 'Pulled Lake',
            'latitude' => 45.1,
            'longitude' => -78.2,
        ]);
        $lake->touch(); // ensure updated_at is recent
        $this->assertEquals('pending_upstream', $lake->fresh()->sync_status);

        $response = $this->actingAs($admin)->getJson('/api/v1/sync/pull?since=2026-01-01T00:00:00Z&mark_synced=1');

        $response->assertStatus(200);
        $response->assertJsonStructure(['lakes', 'records', 'anglers', 'server_timestamp']);
        $this->assertNotEmpty($response->json('lakes'));

        // Verify server marked served record as synced
        $this->assertEquals('synced', $lake->fresh()->sync_status);
    }

    #[Test]
    public function bearer_token_can_access_sync_api()
    {
        User::factory()->create();
        config(['services.nas.token' => 'valid-secret-token']);

        $response = $this->withToken('valid-secret-token')
            ->getJson('/api/v1/sync/pull');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_push_and_pull_photos_with_binary_files()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $photoUuid = 'photo-uuid-push-test-123';
        $photoPath = 'photos/expeditions/boat_sunset.jpg';
        $imageContent = 'real-binary-image-stream-content';

        $payload = [
            'photos' => [
                [
                    'id' => $photoUuid,
                    'photoable_type' => \Fishinglog\Models\Expedition::class,
                    'photoable_id' => 'expedition-uuid-1',
                    'path' => $photoPath,
                    'original_name' => 'boat_sunset.jpg',
                    'caption' => 'Boat Sunset',
                    'file_base64' => base64_encode($imageContent),
                    'updated_at' => '2026-08-15T12:00:00Z',
                ]
            ]
        ];

        // 1. Test Push
        $pushResponse = $this->actingAs($admin)->postJson('/api/v1/sync/push', $payload);
        $pushResponse->assertStatus(200);
        $pushResponse->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('photos', [
            'id' => $photoUuid,
            'caption' => 'Boat Sunset',
        ]);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($photoPath);
        $this->assertEquals($imageContent, \Illuminate\Support\Facades\Storage::disk('public')->get($photoPath));

        // 2. Test Pull
        $pullResponse = $this->actingAs($admin)->getJson('/api/v1/sync/pull?since=2026-01-01T00:00:00Z');
        $pullResponse->assertStatus(200);
        $pulledPhotos = $pullResponse->json('photos');
        $this->assertNotEmpty($pulledPhotos);
        $this->assertEquals(base64_encode($imageContent), $pulledPhotos[0]['file_base64']);
    }

    #[Test]
    public function it_preserves_synced_status_and_timestamps_on_server_during_push()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $lake = Lake::create([
            'name' => 'Original Server Lake',
            'latitude' => 45.0,
            'longitude' => -78.0,
        ]);
        $lake->timestamps = false;
        $lake->updated_at = \Illuminate\Support\Carbon::parse('2026-08-16T08:00:00Z');
        $lake->markSynced();
        $this->assertEquals('synced', $lake->fresh()->sync_status);

        $payload = [
            'lakes' => [
                [
                    'id' => $lake->id,
                    'name' => 'Original Server Lake (Updated from Laptop)',
                    'latitude' => 45.0,
                    'longitude' => -78.0,
                    'updated_at' => '2026-08-16T12:00:00Z',
                ]
            ]
        ];

        $response = $this->actingAs($admin)->postJson('/api/v1/sync/push', $payload);
        $response->assertStatus(200);

        $freshLake = $lake->fresh();
        $this->assertEquals('Original Server Lake (Updated from Laptop)', $freshLake->name);
        $this->assertEquals('synced', $freshLake->sync_status, 'Server must preserve sync_status as synced during push ingestion');
    }

    #[Test]
    public function it_pulls_users_with_password_visible_and_pushes_users_safely()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $user = User::factory()->create([
            'name' => 'Stanley Mroczek',
            'email' => 'spmroczek3@gmail.com',
            'type' => User::DEFAULT_TYPE,
        ]);
        $user->touch();

        // 1. Test Pull includes password in payload
        $pullResponse = $this->actingAs($admin)->getJson('/api/v1/sync/pull?since=2026-01-01T00:00:00Z');
        $pullResponse->assertStatus(200);

        $pulledUsers = $pullResponse->json('users');
        $this->assertNotEmpty($pulledUsers);
        $pulledStanley = collect($pulledUsers)->firstWhere('email', 'spmroczek3@gmail.com');
        $this->assertNotNull($pulledStanley);
        $this->assertNotEmpty($pulledStanley['password'], 'Password must be made visible in pull payload');

        // 2. Test Push without password uses fallback and does not error with 1364
        $newUserUuid = '22222222-3333-4444-5555-666666666666';
        $pushPayload = [
            'users' => [
                [
                    'id' => $newUserUuid,
                    'name' => 'New Push User',
                    'email' => 'pushuser@example.com',
                    'type' => 'default',
                    'updated_at' => '2026-08-16T12:00:00Z',
                ]
            ]
        ];

        $pushResponse = $this->actingAs($admin)->postJson('/api/v1/sync/push', $pushPayload);
        $pushResponse->assertStatus(200);

        $pushedUser = User::find($newUserUuid);
        $this->assertNotNull($pushedUser);
        $this->assertEquals('pushuser@example.com', $pushedUser->email);
        $this->assertNotEmpty($pushedUser->password);
    }
}
