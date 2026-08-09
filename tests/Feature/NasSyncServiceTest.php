<?php

namespace Tests\Feature;

use Fishinglog\Models\Lake;
use Fishinglog\Services\NasSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NasSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_orchestrates_two_way_sync_with_mocked_nas()
    {
        $lake = Lake::create([
            'name' => 'Laptop Field Lake',
            'latitude' => 45.0,
            'longitude' => -78.0,
        ]);

        $remoteUuid = '99999999-8888-7777-6666-555555555555';

        Http::fake([
            'https://nas.example.com/api/v1/sync/push' => Http::response([
                'status' => 'success',
                'synced_uuids' => [$lake->id],
                'processed_count' => 1,
            ], 200),
            'https://nas.example.com/api/v1/sync/pull*' => Http::response([
                'lakes' => [
                    [
                        'id' => $remoteUuid,
                        'name' => 'NAS Home Lake',
                        'latitude' => 46.1,
                        'longitude' => -79.1,
                        'updated_at' => '2026-08-09T10:00:00Z',
                    ]
                ],
                'server_timestamp' => '2026-08-09T15:00:00Z',
            ], 200),
        ]);

        $service = new NasSyncService('https://nas.example.com', 'test-admin-token');
        $result = $service->sync();

        $this->assertEquals(1, $result['pushed']);
        $this->assertEquals(1, $result['pulled']);

        // Verify local pending lake marked synced
        $this->assertEquals('synced', $lake->fresh()->sync_status);

        // Verify remote NAS lake pulled into local DB
        $this->assertDatabaseHas('lakes', [
            'id' => $remoteUuid,
            'name' => 'NAS Home Lake',
            'sync_status' => 'synced',
        ]);
    }
}
