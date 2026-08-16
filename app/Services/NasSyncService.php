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

    public function __construct(?string $nasUrl = null, ?string $apiToken = null)
    {
        $this->nasUrl = rtrim($nasUrl ?? config('services.nas.url', env('NAS_URL', '')), '/');
        $this->apiToken = $apiToken ?? config('services.nas.token', env('NAS_API_TOKEN', ''));
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

        // 1. Execute Push model by model in chunks to prevent request payload size and timeout limits on NAS server
        foreach ($this->modelMap as $key => $modelClass) {
            $pending = $modelClass::pendingUpstream()->get();
            if ($pending->isEmpty()) {
                continue;
            }

            foreach ($pending->chunk(50) as $chunk) {
                $itemsArray = $chunk->map(function ($item) use ($key) {
                    $data = method_exists($item, 'makeVisible')
                        ? $item->makeVisible(['password', 'remember_token'])->toArray()
                        : $item->toArray();

                    if ($key === 'photos' && !empty($item->path) && Storage::disk('public')->exists($item->path)) {
                        $data['file_base64'] = base64_encode(Storage::disk('public')->get($item->path));
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

                // If remote photo includes binary file payload, write to local storage
                if ($key === 'photos' && !empty($remoteItem['file_base64']) && !empty($remoteItem['path'])) {
                    Storage::disk('public')->put($remoteItem['path'], base64_decode($remoteItem['file_base64']));
                }

                $existing = $modelClass::find($id);

                $attributes = $remoteItem;
                $attributes['id'] = $id;
                unset($attributes['uuid']);
                unset($attributes['file_base64']);
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
