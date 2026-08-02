<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Http\Resources\LakeResource;
use Fishinglog\Models\Lake;
use Illuminate\Http\Request;

class LakeApiController extends Controller
{
    public function index()
    {
        $lakes = Lake::orderBy('name', 'asc')->paginate(15);

        return LakeResource::collection($lakes);
    }

    public function show(Lake $lake)
    {
        return new LakeResource($lake);
    }

    public function nearby(Request $request)
    {
        $lat = (float) $request->query('lat', $request->query('latitude'));
        $lng = (float) $request->query('lng', $request->query('longitude'));
        $radius = (float) $request->query('radius', 2.0);
        $excludeId = $request->query('exclude_id');

        $nearbyLakes = Lake::nearby($lat, $lng, $radius, $excludeId);

        return response()->json([
            'data' => $nearbyLakes,
            'count' => $nearbyLakes->count(),
            'radius_miles' => $radius,
        ]);
    }
}
