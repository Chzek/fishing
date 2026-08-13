<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NasSyncApiTest extends TestCase
{
    use RefreshDatabase;

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
    public function admin_can_pull_updated_models_since_timestamp()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $lake = Lake::create([
            'name' => 'Pulled Lake',
            'latitude' => 45.1,
            'longitude' => -78.2,
        ]);
        $lake->touch(); // ensure updated_at is recent

        $response = $this->actingAs($admin)->getJson('/api/v1/sync/pull?since=2026-01-01T00:00:00Z');

        $response->assertStatus(200);
        $response->assertJsonStructure(['lakes', 'records', 'anglers', 'server_timestamp']);
        $this->assertNotEmpty($response->json('lakes'));
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
}
