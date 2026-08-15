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
     * Get last successful sync timestamp string.
     */
    public function getLastSyncedAt(): ?string
    {
        return Cache::get('nas_last_synced_at');
    }

    /**
     * Execute full two-way sync with NAS server.
     */
    public function sync(): array
    {
        if (empty($this->nasUrl)) {
            throw new \RuntimeException('NAS URL is not configured. Set NAS_URL in environment.');
        }

        $pushedCount = 0;
        $pulledCount = 0;

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
                    }
                }
            }
        }

        // 3. Execute Pull for downstream updates
        $lastSyncedAt = $this->getLastSyncedAt();
        $pullQueryParams = $lastSyncedAt ? ['since' => $lastSyncedAt] : [];

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

                $entity = $existing ?? new $modelClass();
                $columns = \Illuminate\Support\Facades\Schema::getColumnListing($entity->getTable());
                $filtered = array_intersect_key($attributes, array_flip($columns));

                if (!$existing) {
                    $entity->forceFill($filtered)->save();
                    $pulledCount++;
                } else {
                    $incomingUpdated = isset($remoteItem['updated_at']) ? Carbon::parse($remoteItem['updated_at']) : null;
                    $localUpdated = $existing->updated_at ? Carbon::parse($existing->updated_at) : null;

                    if (!$localUpdated || ($incomingUpdated && $incomingUpdated->greaterThanOrEqualTo($localUpdated))) {
                        $existing->forceFill($filtered)->save();
                        $pulledCount++;
                    } else {
                        $existing->forceFill([
                            'sync_status' => 'synced',
                            'synced_at' => now(),
                        ])->save();
                    }
                }
            }
        }

        $nowTimestamp = now()->toIso8601String();
        Cache::forever('nas_last_synced_at', $nowTimestamp);

        return [
            'pushed' => $pushedCount,
            'pulled' => $pulledCount,
            'last_synced_at' => $nowTimestamp,
        ];
    }
}
