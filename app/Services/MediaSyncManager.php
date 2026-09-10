<?php

namespace Fishinglog\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaSyncManager
{
    public const DEFAULT_CHUNK_SIZE = 1048576; // 1 MB (1024 * 1024 bytes)

    /**
     * Compute SHA-256 hash of a file on a storage disk.
     */
    public function computeHash(string $diskPath, string $disk = 'public'): ?string
    {
        if (!Storage::disk($disk)->exists($diskPath)) {
            return null;
        }

        $fullPath = Storage::disk($disk)->path($diskPath);
        if (file_exists($fullPath)) {
            return hash_file('sha256', $fullPath);
        }

        $contents = Storage::disk($disk)->get($diskPath);
        return hash('sha256', $contents);
    }

    /**
     * Verify list of media files against remote NAS server to determine which need uploading.
     *
     * @param array<int, array{path: string, hash: string, size?: int}> $mediaList
     * @return array<string> List of relative paths that need to be uploaded
     */
    public function verifyRemoteMedia(string $nasUrl, string $token, array $mediaList): array
    {
        if (empty($mediaList)) {
            return [];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->post("{$nasUrl}/api/v1/sync/media/verify", [
                    'media' => $mediaList,
                ]);

            if ($response->successful()) {
                /** @var array<string> $missing */
                $missing = $response->json('missing_paths') ?? [];
                return $missing;
            }

            Log::warning('Media verification failed on NAS server', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception during remote media verification: ' . $e->getMessage());
        }

        // If verification fails or is unsupported, fallback to sending all paths
        return array_column($mediaList, 'path');
    }

    /**
     * Upload a file in binary chunks (default 1MB per chunk) to the remote NAS server.
     */
    public function uploadFileInChunks(
        string $nasUrl,
        string $token,
        string $diskPath,
        string $targetPath,
        int $chunkSize = self::DEFAULT_CHUNK_SIZE,
        string $disk = 'public'
    ): bool {
        if (!Storage::disk($disk)->exists($diskPath)) {
            return false;
        }

        $fileHash = $this->computeHash($diskPath, $disk);
        $fileSize = Storage::disk($disk)->size($diskPath);

        if ($fileSize === 0 || empty($fileHash)) {
            return false;
        }

        $totalChunks = (int) ceil($fileSize / $chunkSize);
        $uploadId = (string) Str::uuid();

        $stream = Storage::disk($disk)->readStream($diskPath);
        if (!$stream) {
            return false;
        }

        $chunkIndex = 0;
        try {
            while (!feof($stream) && $chunkIndex < $totalChunks) {
                $chunkData = fread($stream, $chunkSize);
                if ($chunkData === false || strlen($chunkData) === 0) {
                    break;
                }

                $chunkHash = hash('sha256', $chunkData);

                $response = Http::withToken($token)
                    ->attach('chunk_data', $chunkData, 'chunk.bin')
                    ->post("{$nasUrl}/api/v1/sync/media/chunk", [
                        'upload_id' => $uploadId,
                        'path' => $targetPath,
                        'chunk_index' => $chunkIndex,
                        'total_chunks' => $totalChunks,
                        'file_hash' => $fileHash,
                        'chunk_hash' => $chunkHash,
                        'file_size' => $fileSize,
                    ]);

                if (!$response->successful()) {
                    Log::error("Failed to upload chunk {$chunkIndex}/{$totalChunks} for {$targetPath}", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return false;
                }

                $chunkIndex++;
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return $chunkIndex === $totalChunks;
    }

    /**
     * Download a remote media file from the NAS server and write to local disk.
     */
    public function downloadFile(
        string $nasUrl,
        string $token,
        string $remotePath,
        string $localPath,
        ?string $expectedHash = null,
        string $disk = 'public'
    ): bool {
        try {
            $response = Http::withToken($token)
                ->get("{$nasUrl}/api/v1/sync/media/download", [
                    'path' => $remotePath,
                ]);

            if (!$response->successful()) {
                Log::warning("Failed to download remote media from NAS: {$remotePath}", [
                    'status' => $response->status(),
                ]);
                return false;
            }

            $contents = $response->body();

            if (!empty($expectedHash)) {
                $actualHash = hash('sha256', $contents);
                if (!hash_equals($expectedHash, $actualHash)) {
                    Log::error("Downloaded file hash mismatch for {$remotePath}. Expected: {$expectedHash}, got: {$actualHash}");
                    return false;
                }
            }

            // Ensure directory exists before writing
            $directory = dirname($localPath);
            if (!empty($directory) && $directory !== '.' && !Storage::disk($disk)->exists($directory)) {
                Storage::disk($disk)->makeDirectory($directory);
            }

            Storage::disk($disk)->put($localPath, $contents);
            return true;
        } catch (\Throwable $e) {
            Log::error("Exception downloading media {$remotePath}: " . $e->getMessage());
            return false;
        }
    }
}
