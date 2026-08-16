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

    #[Test]
    public function it_syncs_photos_and_binary_files_two_ways()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $localPhotoPath = 'photos/expeditions/sunset_boat.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put($localPhotoPath, 'fake-binary-image-data-from-laptop');

        $localPhoto = \Fishinglog\Models\Photo::create([
            'photoable_type' => \Fishinglog\Models\Expedition::class,
            'photoable_id' => 'expedition-uuid-1',
            'path' => $localPhotoPath,
            'original_name' => 'sunset_boat.jpg',
            'caption' => 'Sunset from laptop',
        ]);

        $remotePhotoUuid = 'photo-uuid-from-nas-999';
        $remotePhotoPath = 'photos/records/lunker_pike.jpg';
        $remoteImageContent = 'fake-binary-image-from-nas';

        Http::fake([
            'https://nas.example.com/api/v1/sync/push' => function (\Illuminate\Http\Client\Request $request) use ($localPhoto) {
                $data = $request->data();
                $photos = $data['photos'] ?? [];
                if (count($photos) > 0 && isset($photos[0]['file_base64'])) {
                    return Http::response([
                        'status' => 'success',
                        'synced_uuids' => [$localPhoto->id],
                        'processed_count' => 1,
                    ], 200);
                }
                return Http::response(['status' => 'error'], 400);
            },
            'https://nas.example.com/api/v1/sync/pull*' => Http::response([
                'photos' => [
                    [
                        'id' => $remotePhotoUuid,
                        'photoable_type' => \Fishinglog\Models\Record::class,
                        'photoable_id' => 'record-uuid-2',
                        'path' => $remotePhotoPath,
                        'original_name' => 'lunker_pike.jpg',
                        'caption' => 'Giant northern pike',
                        'file_base64' => base64_encode($remoteImageContent),
                        'updated_at' => '2026-08-15T12:00:00Z',
                    ]
                ],
                'server_timestamp' => '2026-08-15T15:00:00Z',
            ], 200),
        ]);

        $service = new NasSyncService('https://nas.example.com', 'test-admin-token');
        $result = $service->sync();

        $this->assertEquals(1, $result['pushed']);
        $this->assertEquals(1, $result['pulled']);

        // Verify local photo marked synced
        $this->assertEquals('synced', $localPhoto->fresh()->sync_status);

        // Verify pulled photo record exists in database
        $this->assertDatabaseHas('photos', [
            'id' => $remotePhotoUuid,
            'caption' => 'Giant northern pike',
        ]);

        // Verify pulled physical file was written to local storage
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($remotePhotoPath);
        $this->assertEquals($remoteImageContent, \Illuminate\Support\Facades\Storage::disk('public')->get($remotePhotoPath));
    }

    #[Test]
    public function it_returns_accurate_pending_breakdown_per_model()
    {
        Lake::create(['name' => 'Pending Lake 1', 'latitude' => 45.0, 'longitude' => -78.0]);
        Lake::create(['name' => 'Pending Lake 2', 'latitude' => 45.1, 'longitude' => -78.1]);

        \Fishinglog\Models\Angler::create(['firstName' => 'Bob', 'middleName' => 'J', 'lastName' => 'Fisherman']);

        $service = new NasSyncService('https://nas.example.com', 'test-token');

        $this->assertEquals(3, $service->getPendingCount());

        $breakdown = $service->getPendingBreakdown();

        $this->assertArrayHasKey('lakes', $breakdown);
        $this->assertEquals(2, $breakdown['lakes']['count']);
        $this->assertEquals('Lakes & Waters', $breakdown['lakes']['label']);

        $this->assertArrayHasKey('anglers', $breakdown);
        $this->assertEquals(1, $breakdown['anglers']['count']);
        $this->assertEquals('Anglers', $breakdown['anglers']['label']);

        // Models with 0 pending items are not in breakdown
        $this->assertArrayNotHasKey('records', $breakdown);
    }

    #[Test]
    public function it_includes_per_model_breakdown_in_sync_result()
    {
        $lake = Lake::create(['name' => 'Lake Push Test', 'latitude' => 45.0, 'longitude' => -78.0]);
        $remoteLakeUuid = '88888888-4444-4444-4444-888888888888';

        Http::fake([
            'https://nas.example.com/api/v1/sync/push' => Http::response([
                'status' => 'success',
                'synced_uuids' => [$lake->id],
                'processed_count' => 1,
            ], 200),
            'https://nas.example.com/api/v1/sync/pull*' => Http::response([
                'lakes' => [
                    [
                        'id' => $remoteLakeUuid,
                        'name' => 'Remote Pulled Lake',
                        'latitude' => 46.0,
                        'longitude' => -79.0,
                        'updated_at' => '2026-08-16T10:00:00Z',
                    ]
                ],
                'server_timestamp' => '2026-08-16T15:00:00Z',
            ], 200),
        ]);

        $service = new NasSyncService('https://nas.example.com', 'test-token');
        $result = $service->sync();

        $this->assertEquals(1, $result['pushed']);
        $this->assertEquals(1, $result['pulled']);
        $this->assertEquals(['Lakes & Waters' => 1], $result['pushed_breakdown']);
        $this->assertEquals(['Lakes & Waters' => 1], $result['pulled_breakdown']);
    }

    #[Test]
    public function it_performs_full_baseline_pull_ignoring_last_synced_at()
    {
        // Set a cached last_synced_at in the future
        \Illuminate\Support\Facades\Cache::forever('nas_last_synced_at', '2026-08-16T12:00:00Z');

        $remoteLakeUuid = '77777777-7777-7777-7777-777777777777';

        Http::fake([
            'https://nas.example.com/api/v1/sync/pull*' => function (\Illuminate\Http\Client\Request $request) use ($remoteLakeUuid) {
                // In baseline mode, 'since' query parameter should not be present
                $url = $request->url();
                $hasSince = str_contains($url, 'since=');

                if (!$hasSince) {
                    return Http::response([
                        'lakes' => [
                            [
                                'id' => $remoteLakeUuid,
                                'name' => 'Baseline Pulled Lake',
                                'latitude' => 45.5,
                                'longitude' => -78.5,
                                'updated_at' => '2026-01-01T10:00:00Z', // older than cache
                            ]
                        ],
                        'server_timestamp' => '2026-08-16T15:00:00Z',
                    ], 200);
                }

                return Http::response(['lakes' => []], 200);
            },
        ]);

        $service = new NasSyncService('https://nas.example.com', 'test-token');
        $result = $service->sync(forceBaseline: true);

        $this->assertEquals(1, $result['pulled']);
        $this->assertTrue($result['is_baseline']);
        $this->assertDatabaseHas('lakes', [
            'id' => $remoteLakeUuid,
            'name' => 'Baseline Pulled Lake',
        ]);
    }

    #[Test]
    public function it_marks_all_pending_models_as_synced()
    {
        $lake1 = Lake::create(['name' => 'Lake 1', 'latitude' => 45.0, 'longitude' => -78.0]);
        $lake2 = Lake::create(['name' => 'Lake 2', 'latitude' => 45.1, 'longitude' => -78.1]);
        $angler = \Fishinglog\Models\Angler::create(['firstName' => 'John', 'middleName' => '', 'lastName' => 'Doe']);

        $this->assertEquals('pending_upstream', $lake1->fresh()->sync_status);
        $this->assertEquals('pending_upstream', $lake2->fresh()->sync_status);
        $this->assertEquals('pending_upstream', $angler->fresh()->sync_status);

        $service = new NasSyncService('https://nas.example.com', 'test-token');
        $count = $service->markAllSynced();

        $this->assertEquals(3, $count);
        $this->assertEquals('synced', $lake1->fresh()->sync_status);
        $this->assertEquals('synced', $lake2->fresh()->sync_status);
        $this->assertEquals('synced', $angler->fresh()->sync_status);
        $this->assertEquals(0, $service->getPendingCount());
    }

    #[Test]
    public function it_preserves_synced_status_and_timestamps_when_pulling_existing_records()
    {
        $lake = Lake::create(['name' => 'Existing Lake', 'latitude' => 45.0, 'longitude' => -78.0]);
        $lake->timestamps = false;
        $lake->updated_at = \Illuminate\Support\Carbon::parse('2026-08-16T08:00:00Z');
        $lake->markSynced();
        $this->assertEquals('synced', $lake->fresh()->sync_status);

        $remoteUpdatedAt = '2026-08-16T10:00:00Z';

        Http::fake([
            'https://nas.example.com/api/v1/sync/push' => Http::response(['status' => 'success', 'synced_uuids' => []], 200),
            'https://nas.example.com/api/v1/sync/pull*' => Http::response([
                'lakes' => [
                    [
                        'id' => $lake->id,
                        'name' => 'Existing Lake (Updated Name from NAS)',
                        'latitude' => 45.0,
                        'longitude' => -78.0,
                        'updated_at' => $remoteUpdatedAt,
                    ]
                ],
                'server_timestamp' => '2026-08-16T15:00:00Z',
            ], 200),
        ]);

        $service = new NasSyncService('https://nas.example.com', 'test-token');
        $result = $service->sync();

        $this->assertEquals(1, $result['pulled']);
        
        $freshLake = $lake->fresh();
        $this->assertEquals('Existing Lake (Updated Name from NAS)', $freshLake->name);
        $this->assertEquals('synced', $freshLake->sync_status, 'Sync status must remain synced and not be flipped to pending_upstream by model event');
        $this->assertEquals(0, $service->getPendingCount());
    }
}
