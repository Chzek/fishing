<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SyncApiController extends Controller
{
    /**
     * Entity key to model class mapping.
     */
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

    /**
     * Check if request is authorized via NAS_API_TOKEN or admin user.
     */
    protected function isAuthorized(Request $request): bool
    {
        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN') ?? $request->input('api_token');
        $configuredToken = config('services.nas.token', env('NAS_API_TOKEN'));

        if (!empty($configuredToken) && !empty($token) && hash_equals($configuredToken, $token)) {
            return true;
        }

        $user = $request->user() ?? auth('api')->user();
        return (bool) ($user && $user->isAdmin());
    }

    /**
     * Receive outbox push payload from client.
     */
    public function push(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(['message' => 'Only admin users are authorized to perform database sync.'], 403);
        }

        $syncedUuids = [];
        $processedCount = 0;

        foreach ($this->modelMap as $key => $modelClass) {
            $items = $request->input($key, []);
            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $itemData) {
                $id = $itemData['id'] ?? $itemData['uuid'] ?? null;
                if (empty($id)) {
                    continue;
                }

                $existing = $modelClass::find($id);

                $attributes = $itemData;
                $attributes['id'] = $id;
                unset($attributes['uuid']);
                $attributes['sync_status'] = 'synced';
                $attributes['synced_at'] = now();

                $entity = $existing ?? new $modelClass();
                $columns = \Illuminate\Support\Facades\Schema::getColumnListing($entity->getTable());
                $filtered = array_intersect_key($attributes, array_flip($columns));

                if (!$existing) {
                    $entity->forceFill($filtered)->save();
                    $syncedUuids[] = $id;
                    $processedCount++;
                } else {
                    $incomingUpdated = isset($itemData['updated_at']) ? Carbon::parse($itemData['updated_at']) : null;
                    $localUpdated = $existing->updated_at ? Carbon::parse($existing->updated_at) : null;

                    if (!$localUpdated || ($incomingUpdated && $incomingUpdated->greaterThanOrEqualTo($localUpdated))) {
                        $existing->forceFill($filtered)->save();
                    } else {
                        $existing->forceFill([
                            'sync_status' => 'synced',
                            'synced_at' => now(),
                        ])->save();
                    }

                    $syncedUuids[] = $id;
                    $processedCount++;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'synced_uuids' => $syncedUuids,
            'processed_count' => $processedCount,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Return downstream updates modified since 'since' timestamp.
     */
    public function pull(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(['message' => 'Only admin users are authorized to perform database sync.'], 403);
        }

        $sinceStr = $request->query('since');
        $since = $sinceStr ? Carbon::parse($sinceStr) : Carbon::createFromTimestamp(0);

        $payload = [];

        foreach ($this->modelMap as $key => $modelClass) {
            $query = $modelClass::where('updated_at', '>', $since);

            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass))) {
                $query->withTrashed();
            }

            $payload[$key] = $query->get();
        }

        $payload['server_timestamp'] = now()->toIso8601String();

        return response()->json($payload);
    }
}
