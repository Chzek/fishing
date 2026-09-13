<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\FishingRule;
use Fishinglog\Models\FishingZone;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Photo;
use Fishinglog\Models\Post;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NasSyncService
{
    protected string $nasUrl;
    protected string $apiToken;
    protected MediaSyncManager $mediaSyncManager;

    protected array $modelMap = [
        'users' => User::class,
        'anglers' => Angler::class,
        'lakes' => Lake::class,
        'fish_families' => FishFamily::class,
        'fish_breeds' => FishBreed::class,
        'lures' => Lure::class,
        'records' => Record::class,
        'expeditions' => Expedition::class,
        'crews' => Crew::class,
        'posts' => Post::class,
        'fishing_zones' => FishingZone::class,
        'fishing_rules' => FishingRule::class,
        'photos' => Photo::class,
    ];

    protected array $modelLabels = [
        'records' => 'Catches',
        'photos' => 'Photos',
        'lakes' => 'Lakes & Waters',
        'expeditions' => 'Expeditions',
        'anglers' => 'Anglers',
        'lures' => 'Lures & Tackle',
        'fish_breeds' => 'Fish Species',
        'fish_families' => 'Fish Families',
        'posts' => 'Log Posts',
        'crews' => 'Crews',
        'fishing_zones' => 'Zones',
        'fishing_rules' => 'Rules',
        'users' => 'Users',
    ];

    public function __construct(?string $nasUrl = null, ?string $apiToken = null, ?MediaSyncManager $mediaSyncManager = null)
    {
        $this->nasUrl = rtrim($nasUrl ?? (string) config('services.nas.url', ''), '/');
        $this->apiToken = $apiToken ?? (string) config('services.nas.token', '');
        $this->mediaSyncManager = $mediaSyncManager ?? app(MediaSyncManager::class);
    }

    /**
     * Get count of local records pending upstream sync.
     */
    public function getPendingCount(): int
    {
        $total = 0;
        foreach ($this->modelMap as $modelClass) {
            $total += $modelClass::pendingUpstream()->count();
        }
        return $total;
    }

    /**
     * Get associative list of models and their pending counts.
     */
    public function getPendingBreakdown(): array
    {
        $breakdown = [];
        foreach ($this->modelMap as $key => $modelClass) {
            $count = $modelClass::pendingUpstream()->count();
            if ($count > 0) {
                $breakdown[$key] = [
                    'key' => $key,
                    'label' => $this->modelLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                    'count' => $count,
                ];
            }
        }
        return $breakdown;
    }

    /**
     * Get last successful sync timestamp string.
     */
    public function getLastSyncedAt(): ?string
    {
        return Cache::get('nas_last_synced_at');
    }

    /**
     * Test live connectivity, latency, SSL certificate status, and token authorization against the remote NAS.
     */
    public function checkConnectivity(bool $forceFresh = false): array
    {
        if (empty($this->nasUrl)) {
            return [
                'online' => false,
                'latency_ms' => null,
                'http_status' => null,
                'ssl_enabled' => false,
                'ssl_valid' => false,
                'ssl_issuer' => null,
                'ssl_expires_at' => null,
                'auth_valid' => false,
                'target_url' => 'Not Configured (Set NAS_URL in environment)',
                'instance_name' => $this->getInstanceName(),
                'target_name' => $this->getTargetName(),
                'error_message' => 'NAS_URL is empty in application configuration.',
                'checked_at' => now()->toIso8601String(),
            ];
        }

        $sslEnabled = str_starts_with(strtolower($this->nasUrl), 'https://');
        $sslValid = false;
        $sslIssuer = null;
        $sslExpiresAt = null;

        $startTime = microtime(true);
        $isOnline = false;
        $httpStatus = null;
        $authValid = false;
        $errorMessage = null;

        try {
            $response = Http::timeout(5)
                ->withToken($this->apiToken)
                ->acceptJson()
                ->get("{$this->nasUrl}/api/v1/sync/pull", [
                    'limit' => 1,
                ]);

            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);
            $httpStatus = $response->status();

            if ($response->successful()) {
                $isOnline = true;
                $authValid = true;
                if ($sslEnabled) {
                    $sslValid = true;
                    $sslIssuer = 'Verified TLS Session';
                }
            } elseif ($httpStatus === 401 || $httpStatus === 403) {
                $isOnline = true;
                $authValid = false;
                $errorMessage = "Authentication Rejected ({$httpStatus}). Check NAS_API_TOKEN configuration.";
            } else {
                $isOnline = true;
                $errorMessage = "Server responded with HTTP status {$httpStatus}.";
            }
        } catch (\Throwable $e) {
            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);
            $isOnline = false;
            $errorMessage = $e->getMessage();
        }

        // Additional peer SSL certificate inspection for HTTPS endpoints
        if ($sslEnabled && $isOnline && function_exists('openssl_x509_parse')) {
            try {
                $parsed = parse_url($this->nasUrl);
                $host = $parsed['host'] ?? null;
                $port = $parsed['port'] ?? 443;
                if ($host) {
                    $streamContext = stream_context_create([
                        'ssl' => [
                            'capture_peer_cert' => true,
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]);
                    $client = @stream_socket_client("ssl://{$host}:{$port}", $errno, $errstr, 2, STREAM_CLIENT_CONNECT, $streamContext);
                    if ($client) {
                        $params = stream_context_get_params($client);
                        $cert = $params['options']['ssl']['peer_certificate'] ?? null;
                        if ($cert) {
                            $certData = openssl_x509_parse($cert);
                            if ($certData) {
                                $sslIssuer = $certData['issuer']['O'] ?? $certData['issuer']['CN'] ?? 'Let\'s Encrypt / Synology CA';
                                if (!empty($certData['validTo_time_t'])) {
                                    $sslExpiresAt = Carbon::createFromTimestamp($certData['validTo_time_t'])->format('Y-m-d H:i:s');
                                    $sslValid = $certData['validTo_time_t'] > time();
                                }
                            }
                        }
                        fclose($client);
                    }
                }
            } catch (\Throwable) {
                // Keep default SSL heuristics if raw socket probe fails
            }
        }

        return [
            'online' => $isOnline,
            'latency_ms' => $isOnline ? $latencyMs : null,
            'http_status' => $httpStatus,
            'ssl_enabled' => $sslEnabled,
            'ssl_valid' => $sslValid,
            'ssl_issuer' => $sslIssuer ?: ($sslEnabled ? 'TLS / SSL' : 'Disabled (HTTP)'),
            'ssl_expires_at' => $sslExpiresAt,
            'auth_valid' => $authValid,
            'target_url' => $this->nasUrl,
            'instance_name' => $this->getInstanceName(),
            'target_name' => $this->getTargetName(),
            'error_message' => $errorMessage,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get detailed synchronization matrix and outbox status across all 13 models.
     */
    public function getDetailedModelMatrix(): array
    {
        $matrix = [];

        foreach ($this->modelMap as $key => $modelClass) {
            $total = $modelClass::count();
            $synced = $modelClass::where('sync_status', 'synced')->count();
            $pending = $modelClass::pendingUpstream()->count();
            $percent = $total > 0 ? (int) round(($synced / $total) * 100) : 100;

            $latestLocal = $modelClass::max('updated_at');
            $latestSynced = $modelClass::where('sync_status', 'synced')->max('synced_at');

            $matrix[$key] = [
                'key' => $key,
                'label' => $this->modelLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                'total' => $total,
                'synced' => $synced,
                'pending' => $pending,
                'percent' => $percent,
                'latest_local' => $latestLocal ? Carbon::parse($latestLocal)->diffForHumans() : '—',
                'latest_synced' => $latestSynced ? Carbon::parse($latestSynced)->diffForHumans() : 'Never',
            ];
        }

        return $matrix;
    }

    /**
     * Get media assets synchronization diagnostics.
     */
    public function getMediaDiagnosticStatus(): array
    {
        $photosCount = Photo::count();
        $photosWithFile = Photo::whereNotNull('path')->where('path', '!=', '')->count();
        $anglersWithAvatar = Angler::whereNotNull('avatar')->where('avatar', '!=', '')->count();
        $speciesWithArtwork = FishBreed::where(function ($q) {
            $q->whereNotNull('avatar')->orWhereNotNull('image');
        })->count();

        $totalMediaAssets = $photosWithFile + $anglersWithAvatar + $speciesWithArtwork;

        return [
            'total_media_records' => $totalMediaAssets,
            'photos_count' => $photosCount,
            'photos_with_file' => $photosWithFile,
            'anglers_avatars_count' => $anglersWithAvatar,
            'species_artwork_count' => $speciesWithArtwork,
            'chunk_size_bytes' => MediaSyncManager::DEFAULT_CHUNK_SIZE,
            'chunk_size_mb' => round(MediaSyncManager::DEFAULT_CHUNK_SIZE / 1048576, 1),
            'hashing_algorithm' => 'SHA-256',
            'storage_disk' => 'public',
        ];
    }

    /**
     * Execute full two-way sync with NAS server.
     */
    public function sync(bool $forceBaseline = false): array
    {
        if (empty($this->nasUrl)) {
            throw new \RuntimeException('NAS URL is not configured. Set NAS_URL in environment.');
        }

        $pushedCount = 0;
        $pulledCount = 0;
        $pushedBreakdown = [];
        $pulledBreakdown = [];
        $mediaToVerify = [];

        // 1. Execute Push model by model in chunks to prevent request payload size and timeout limits on NAS server
        foreach ($this->modelMap as $key => $modelClass) {
            $pending = $modelClass::pendingUpstream()->get();
            if ($pending->isEmpty()) {
                continue;
            }

            // Standard models chunked at 50; media models chunked at 25 since binary is streamed separately
            $chunkSize = 50;
            foreach ($pending->chunk($chunkSize) as $chunk) {

                $itemsArray = $chunk->map(function ($item) use ($key, &$mediaToVerify) {
                    $data = method_exists($item, 'makeVisible')
                        ? $item->makeVisible(['password', 'remember_token'])->toArray()
                        : $item->toArray();

                    // Strip array location object so receiver calculates Spatial Point cleanly from latitude and longitude
                    if (isset($data['location']) && is_array($data['location'])) {
                        unset($data['location']);
                    }

                    // Track media assets for SHA-256 chunked streaming
                    if ($key === 'photos' && !empty($item->path) && Storage::disk('public')->exists($item->path)) {
                        $hash = $this->mediaSyncManager->computeHash($item->path);
                        if ($hash) {
                            $mediaToVerify[] = ['path' => $item->path, 'hash' => $hash];
                        }
                    }
                    if ($key === 'anglers' && !empty($item->avatar) && Storage::disk('public')->exists('avatars/' . $item->avatar)) {
                        $avatarPath = 'avatars/' . $item->avatar;
                        $hash = $this->mediaSyncManager->computeHash($avatarPath);
                        if ($hash) {
                            $mediaToVerify[] = ['path' => $avatarPath, 'hash' => $hash];
                        }
                    }
                    if ($key === 'fish_breeds') {
                        if (!empty($item->avatar) && Storage::disk('public')->exists('fish/avatars/' . $item->avatar)) {
                            $avatarPath = 'fish/avatars/' . $item->avatar;
                            $hash = $this->mediaSyncManager->computeHash($avatarPath);
                            if ($hash) {
                                $mediaToVerify[] = ['path' => $avatarPath, 'hash' => $hash];
                            }
                        }
                        if (!empty($item->image) && Storage::disk('public')->exists('fish/' . $item->image)) {
                            $imagePath = 'fish/' . $item->image;
                            $hash = $this->mediaSyncManager->computeHash($imagePath);
                            if ($hash) {
                                $mediaToVerify[] = ['path' => $imagePath, 'hash' => $hash];
                            }
                        }
                    }

                    return $data;
                })->all();

                $pushPayload = [$key => $itemsArray];
                $localPendingByUuid = [];
                foreach ($chunk as $item) {
                    $localPendingByUuid[$item->id ?? $item->uuid] = $item;
                }

                $pushResponse = Http::withToken($this->apiToken)
                    ->acceptJson()
                    ->post("{$this->nasUrl}/api/v1/sync/push", $pushPayload);

                if (!$pushResponse->successful()) {
                    Log::error('NAS Sync Push Failed', ['status' => $pushResponse->status(), 'body' => $pushResponse->body()]);
                    throw new \RuntimeException("NAS Sync Push Failed for {$key} with status {$pushResponse->status()}");
                }

                $responseData = $pushResponse->json();
                $syncedUuids = $responseData['synced_uuids'] ?? [];

                foreach ($syncedUuids as $uuid) {
                    if (isset($localPendingByUuid[$uuid])) {
                        $localPendingByUuid[$uuid]->markSynced();
                        $pushedCount++;
                        $label = $this->modelLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                        $pushedBreakdown[$label] = ($pushedBreakdown[$label] ?? 0) + 1;
                    }
                }
            }
        }

        // 2. Stream pending media binary files in multi-chunk slices with SHA-256 deduplication
        if (!empty($mediaToVerify)) {
            $missingPaths = $this->mediaSyncManager->verifyRemoteMedia($this->nasUrl, $this->apiToken, $mediaToVerify);
            foreach ($missingPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    $this->mediaSyncManager->uploadFileInChunks($this->nasUrl, $this->apiToken, $path, $path);
                }
            }
        }

        // 3. Execute Pull for downstream updates
        $lastSyncedAt = $forceBaseline ? null : $this->getLastSyncedAt();
        $pullQueryParams = $lastSyncedAt ? ['since' => $lastSyncedAt, 'mark_synced' => 1] : ['mark_synced' => 1];

        $pullResponse = Http::withToken($this->apiToken)
            ->acceptJson()
            ->get("{$this->nasUrl}/api/v1/sync/pull", $pullQueryParams);

        if (!$pullResponse->successful()) {
            Log::error('NAS Sync Pull Failed', ['status' => $pullResponse->status(), 'body' => $pullResponse->body()]);
            throw new \RuntimeException("NAS Sync Pull Failed with status {$pullResponse->status()}");
        }

        $pullData = $pullResponse->json();

        foreach ($this->modelMap as $key => $modelClass) {
            $remoteItems = $pullData[$key] ?? [];
            foreach ($remoteItems as $remoteItem) {
                $id = $remoteItem['id'] ?? $remoteItem['uuid'] ?? null;
                if (empty($id)) {
                    continue;
                }

                // If remote photo or avatar includes legacy binary file payload, write to local storage
                if ($key === 'photos' && !empty($remoteItem['file_base64']) && !empty($remoteItem['path'])) {
                    Storage::disk('public')->put($remoteItem['path'], base64_decode($remoteItem['file_base64']));
                } elseif ($key === 'photos' && !empty($remoteItem['path']) && !Storage::disk('public')->exists($remoteItem['path'])) {
                    // Stream download missing photo from NAS server
                    $this->mediaSyncManager->downloadFile($this->nasUrl, $this->apiToken, $remoteItem['path'], $remoteItem['path']);
                }

                if ($key === 'anglers' && !empty($remoteItem['avatar_base64']) && !empty($remoteItem['avatar'])) {
                    Storage::disk('public')->put('avatars/' . $remoteItem['avatar'], base64_decode($remoteItem['avatar_base64']));
                } elseif ($key === 'anglers' && !empty($remoteItem['avatar']) && !in_array($remoteItem['avatar'], ['user.jpg', 'default.jpg', 'avatar.jpg', 'default-avatar.png']) && !Storage::disk('public')->exists('avatars/' . $remoteItem['avatar'])) {
                    $this->mediaSyncManager->downloadFile($this->nasUrl, $this->apiToken, 'avatars/' . $remoteItem['avatar'], 'avatars/' . $remoteItem['avatar']);
                }

                if ($key === 'fish_breeds') {
                    if (!empty($remoteItem['avatar_base64']) && !empty($remoteItem['avatar'])) {
                        Storage::disk('public')->put('fish/avatars/' . $remoteItem['avatar'], base64_decode($remoteItem['avatar_base64']));
                    } elseif (!empty($remoteItem['avatar']) && !Storage::disk('public')->exists('fish/avatars/' . $remoteItem['avatar'])) {
                        $this->mediaSyncManager->downloadFile($this->nasUrl, $this->apiToken, 'fish/avatars/' . $remoteItem['avatar'], 'fish/avatars/' . $remoteItem['avatar']);
                    }

                    if (!empty($remoteItem['image_base64']) && !empty($remoteItem['image'])) {
                        Storage::disk('public')->put('fish/' . $remoteItem['image'], base64_decode($remoteItem['image_base64']));
                    } elseif (!empty($remoteItem['image']) && !Storage::disk('public')->exists('fish/' . $remoteItem['image'])) {
                        $this->mediaSyncManager->downloadFile($this->nasUrl, $this->apiToken, 'fish/' . $remoteItem['image'], 'fish/' . $remoteItem['image']);
                    }
                }

                $existing = $modelClass::find($id);

                $attributes = $remoteItem;
                $attributes['id'] = $id;
                unset($attributes['uuid']);
                unset($attributes['file_base64']);
                unset($attributes['avatar_base64']);
                unset($attributes['image_base64']);

                $attributes['sync_status'] = 'synced';
                $attributes['synced_at'] = now();

                if (!empty($attributes['created_at'])) {
                    $attributes['created_at'] = Carbon::parse($attributes['created_at']);
                }
                if (!empty($attributes['updated_at'])) {
                    $attributes['updated_at'] = Carbon::parse($attributes['updated_at']);
                }
                if (!empty($attributes['deleted_at'])) {
                    $attributes['deleted_at'] = Carbon::parse($attributes['deleted_at']);
                }
                if (!empty($attributes['email_verified_at'])) {
                    $attributes['email_verified_at'] = Carbon::parse($attributes['email_verified_at']);
                }
                if (!empty($attributes['caught'])) {
                    $attributes['caught'] = Carbon::parse($attributes['caught']);
                }

                $entity = $existing ?? new $modelClass();
                $columns = \Illuminate\Support\Facades\Schema::getColumnListing($entity->getTable());
                $filtered = array_intersect_key($attributes, array_flip($columns));

                if ($key === 'users') {
                    if (!$existing && empty($filtered['password'])) {
                        $filtered['password'] = \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32));
                    } elseif ($existing && empty($filtered['password'])) {
                        unset($filtered['password']);
                    }
                }

                if (!$existing) {
                    $entity->timestamps = false;
                    $entity->forceFill($filtered);
                    $entity->saveQuietly();
                    $entity->timestamps = true;
                    $pulledCount++;
                    $label = $this->modelLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                    $pulledBreakdown[$label] = ($pulledBreakdown[$label] ?? 0) + 1;
                } else {
                    $incomingUpdated = isset($remoteItem['updated_at']) ? Carbon::parse($remoteItem['updated_at']) : null;
                    $localUpdated = $existing->updated_at ? Carbon::parse($existing->updated_at) : null;

                    if ($forceBaseline || !$localUpdated || ($incomingUpdated && $incomingUpdated->greaterThanOrEqualTo($localUpdated))) {
                        $existing->timestamps = false;
                        $existing->forceFill($filtered);
                        $existing->saveQuietly();
                        $existing->timestamps = true;
                        $pulledCount++;
                        $label = $this->modelLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                        $pulledBreakdown[$label] = ($pulledBreakdown[$label] ?? 0) + 1;
                    } else {
                        $existing->timestamps = false;
                        $existing->forceFill([
                            'sync_status' => 'synced',
                            'synced_at' => now(),
                        ]);
                        $existing->saveQuietly();
                        $existing->timestamps = true;
                    }
                }
            }
        }

        $nowTimestamp = now()->toIso8601String();
        Cache::forever('nas_last_synced_at', $nowTimestamp);

        return [
            'pushed' => $pushedCount,
            'pulled' => $pulledCount,
            'pushed_breakdown' => $pushedBreakdown,
            'pulled_breakdown' => $pulledBreakdown,
            'last_synced_at' => $nowTimestamp,
            'is_baseline' => $forceBaseline,
        ];
    }

    /**
     * Mark all local records across all syncable models as synced.
     */
    public function markAllSynced(): int
    {
        $total = 0;
        foreach ($this->modelMap as $modelClass) {
            $total += $modelClass::where('sync_status', '!=', 'synced')
                ->update([
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                ]);
        }
        return $total;
    }

    /**
     * Get the configured name of the remote synchronization target (e.g., 'Laptop' or 'NAS').
     */
    public function getTargetName(): string
    {
        return config('services.nas.target_name', app()->isProduction() ? 'Laptop' : 'NAS');
    }

    /**
     * Get the configured name of the local application instance (e.g., 'Field Laptop' or 'Synology NAS').
     */
    public function getInstanceName(): string
    {
        return config('services.nas.instance_name', app()->isProduction() ? 'Synology NAS' : 'Field Laptop');
    }
}
