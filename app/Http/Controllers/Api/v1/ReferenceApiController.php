<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Illuminate\Http\Request;

class ReferenceApiController extends Controller
{
    public function index()
    {
        return response()->json([
            'anglers' => Angler::orderBy('lastName', 'asc')->get(['id', 'firstName', 'middleName', 'lastName']),
            'lakes' => Lake::orderBy('name', 'asc')->get(['id', 'name', 'latitude', 'longitude']),
            'fish_breeds' => FishBreed::orderBy('name', 'asc')->get(['id', 'name', 'fish_families_id']),
            'lures' => Lure::orderBy('name', 'asc')->get(['id', 'name', 'color', 'size']),
            'expeditions' => Expedition::orderBy('start', 'desc')->get(['id', 'description', 'start', 'finish']),
        ]);
    }
}
