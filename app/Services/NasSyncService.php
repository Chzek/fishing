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
use Fishinglog\Models\Post;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // 1. Prepare Push Outbox Payload
        $pushPayload = [];
        $localPendingByUuid = [];

        foreach ($this->modelMap as $key => $modelClass) {
            $pending = $modelClass::pendingUpstream()->get();
            if ($pending->isNotEmpty()) {
                $pushPayload[$key] = $pending->toArray();
                foreach ($pending as $item) {
                    $localPendingByUuid[$item->uuid] = $item;
                }
            }
        }

        // 2. Execute Push if outbox items exist
        if (!empty($pushPayload)) {
            $pushResponse = Http::withToken($this->apiToken)
                ->acceptJson()
                ->post("{$this->nasUrl}/api/v1/sync/push", $pushPayload);

            if (!$pushResponse->successful()) {
                Log::error('NAS Sync Push Failed', ['status' => $pushResponse->status(), 'body' => $pushResponse->body()]);
                throw new \RuntimeException("NAS Sync Push Failed with status {$pushResponse->status()}");
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
                if (empty($remoteItem['uuid'])) {
                    continue;
                }

                $existing = $modelClass::where('uuid', $remoteItem['uuid'])->first();

                $attributes = $remoteItem;
                unset($attributes['id']);
                $attributes['sync_status'] = 'synced';
                $attributes['synced_at'] = now();

                if (!$existing) {
                    $modelClass::create($attributes);
                    $pulledCount++;
                } else {
                    $incomingUpdated = isset($remoteItem['updated_at']) ? Carbon::parse($remoteItem['updated_at']) : null;
                    $localUpdated = $existing->updated_at ? Carbon::parse($existing->updated_at) : null;

                    if (!$localUpdated || ($incomingUpdated && $incomingUpdated->greaterThanOrEqualTo($localUpdated))) {
                        $existing->update($attributes);
                        $pulledCount++;
                    } else {
                        $existing->update([
                            'sync_status' => 'synced',
                            'synced_at' => now(),
                        ]);
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
