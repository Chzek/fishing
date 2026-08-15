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
}
