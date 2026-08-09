<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\FishingZone;

class FishingZoneController extends Controller
{
    public function index()
    {
        $fishingZones = FishingZone::withCount('lakes')
            ->orderBy('code', 'asc')
            ->get();

        return view('fishing-zone.index', [
            'fishingZones' => $fishingZones,
        ]);
    }

    public function show(FishingZone $fishingZone)
    {
        $fishingZone->load(['rules', 'lakes' => function ($q) {
            $q->withCount('records');
        }]);

        return view('fishing-zone.show', [
            'fishingZone' => $fishingZone,
        ]);
    }
}
