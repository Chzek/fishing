<?php

namespace Tests\Feature;

use Fishinglog\Models\Photo;
use Fishinglog\Models\User;
use Fishinglog\Services\MediaSyncManager;
use Fishinglog\Services\NasSyncService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NasMediaChunkSyncTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_verifies_remote_media_hashes_and_identifies_missing_files()
    {
        Storage::fake('public');

        $user = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $this->actingAs($user, 'api');

        // Prepare one existing file and one missing file
        $existingPath = 'photos/catch_1.jpg';
        $existingContent = 'sample-image-bytes-12345';
        Storage::disk('public')->put($existingPath, $existingContent);
        $existingHash = hash('sha256', $existingContent);

        $missingPath = 'photos/catch_2.jpg';
        $missingHash = hash('sha256', 'other-content');

        $response = $this->postJson('/api/v1/sync/media/verify', [
            'media' => [
                ['path' => $existingPath, 'hash' => $existingHash],
                ['path' => $missingPath, 'hash' => $missingHash],
                ['path' => 'photos/corrupted.jpg', 'hash' => 'wrong-hash-checksum'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'missing_paths' => [
                $missingPath,
                'photos/corrupted.jpg',
            ],
        ]);
    }

    #[Test]
    public function it_receives_and_assembles_multi_chunk_binary_upload_with_sha256_verification()
    {
        Storage::fake('public');

        $user = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $this->actingAs($user, 'api');

        $targetPath = 'photos/expeditions/french_river_big_muskie.jpg';
        $fullContent = str_repeat('ABCDEF1234567890', 100); // 1600 bytes
        $fileHash = hash('sha256', $fullContent);
        $fileSize = strlen($fullContent);

        // Split into 3 chunks
        $chunkSize = 600;
        $chunks = str_split($fullContent, $chunkSize);
        $totalChunks = count($chunks);
        $uploadId = (string) Str::uuid();

        foreach ($chunks as $index => $chunkData) {
            $chunkHash = hash('sha256', $chunkData);
            $file = UploadedFile::fake()->createWithContent('chunk.bin', $chunkData);

            $response = $this->postJson('/api/v1/sync/media/chunk', [
                'upload_id' => $uploadId,
                'path' => $targetPath,
                'chunk_index' => $index,
                'total_chunks' => $totalChunks,
                'file_hash' => $fileHash,
                'chunk_hash' => $chunkHash,
                'file_size' => $fileSize,
                'chunk_data' => $file,
            ]);

            $response->assertStatus(200);

            if ($index + 1 === $totalChunks) {
                $response->assertJson([
                    'status' => 'success',
                    'completed' => true,
                ]);
            } else {
                $response->assertJson([
                    'status' => 'success',
                    'completed' => false,
                ]);
            }
        }

        // Verify assembled file exists in storage and matches exact content
        Storage::disk('public')->assertExists($targetPath);
        $this->assertEquals($fullContent, Storage::disk('public')->get($targetPath));
    }

    #[Test]
    public function it_rejects_chunk_upload_with_tampered_checksum()
    {
        Storage::fake('public');

        $user = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $this->actingAs($user, 'api');

        $file = UploadedFile::fake()->createWithContent('chunk.bin', 'actual-chunk-content');

        $response = $this->postJson('/api/v1/sync/media/chunk', [
            'upload_id' => (string) Str::uuid(),
            'path' => 'photos/test.jpg',
            'chunk_index' => 0,
            'total_chunks' => 1,
            'file_hash' => 'hash1',
            'chunk_hash' => 'tampered-or-invalid-hash',
            'file_size' => 100,
            'chunk_data' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Chunk hash mismatch.']);
    }

    #[Test]
    public function it_downloads_binary_media_file_with_authorization()
    {
        Storage::fake('public');

        $user = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $this->actingAs($user, 'api');

        $path = 'avatars/bob_angler.jpg';
        $content = 'binary-avatar-image-content-xyz';
        Storage::disk('public')->put($path, $content);

        $response = $this->get('/api/v1/sync/media/download?path=' . urlencode($path));
        $response->assertStatus(200);
        $this->assertEquals($content, $response->streamedContent());
    }

    #[Test]
    public function it_synchronizes_photos_two_ways_via_chunked_streaming()
    {
        Storage::fake('public');

        $localPath = 'photos/catches/giant_pike.jpg';
        $localContent = 'binary-giant-pike-catch-photo';
        Storage::disk('public')->put($localPath, $localContent);

        $localPhoto = Photo::create([
            'photoable_type' => \Fishinglog\Models\Record::class,
            'photoable_id' => (string) Str::uuid(),
            'path' => $localPath,
            'original_name' => 'giant_pike.jpg',
            'caption' => 'Master Angler Trophy Pike',
        ]);

        $remotePhotoUuid = (string) Str::uuid();
        $remotePath = 'photos/catches/walleye_gold.jpg';
        $remoteContent = 'binary-walleye-gold-photo';

        Http::fake([
            'https://nas.example.com/api/v1/sync/push' => function (\Illuminate\Http\Client\Request $request) use ($localPhoto) {
                return Http::response([
                    'status' => 'success',
                    'synced_uuids' => [$localPhoto->id],
                    'processed_count' => 1,
                ], 200);
            },
            'https://nas.example.com/api/v1/sync/media/verify' => Http::response([
                'status' => 'success',
                'missing_paths' => [$localPath],
            ], 200),
            'https://nas.example.com/api/v1/sync/media/chunk' => Http::response([
                'status' => 'success',
                'completed' => true,
            ], 200),
            'https://nas.example.com/api/v1/sync/pull*' => Http::response([
                'photos' => [
                    [
                        'id' => $remotePhotoUuid,
                        'photoable_type' => \Fishinglog\Models\Record::class,
                        'photoable_id' => (string) Str::uuid(),
                        'path' => $remotePath,
                        'original_name' => 'walleye_gold.jpg',
                        'caption' => 'Sunset Walleye',
                        'updated_at' => now()->toIso8601String(),
                    ],
                ],
                'server_timestamp' => now()->toIso8601String(),
            ], 200),
            'https://nas.example.com/api/v1/sync/media/download*' => Http::response($remoteContent, 200),
        ]);

        $service = new NasSyncService('https://nas.example.com', 'test-token');
        $result = $service->sync();

        $this->assertEquals(1, $result['pushed']);
        $this->assertEquals(1, $result['pulled']);

        // Verify local photo marked synced
        $this->assertEquals('synced', $localPhoto->fresh()->sync_status);

        // Verify remote photo record created and binary downloaded to disk
        $this->assertDatabaseHas('photos', [
            'id' => $remotePhotoUuid,
            'path' => $remotePath,
            'caption' => 'Sunset Walleye',
            'sync_status' => 'synced',
        ]);

        Storage::disk('public')->assertExists($remotePath);
        $this->assertEquals($remoteContent, Storage::disk('public')->get($remotePath));
    }
}
