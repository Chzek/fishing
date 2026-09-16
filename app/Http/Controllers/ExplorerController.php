<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;

class ExplorerController extends Controller
{
    /**
     * Display the Interactive Lake Explorer Map & Catch Analytics page.
     */
    public function index()
    {
        $fishBreeds = FishBreed::orderBy('name', 'asc')->get();
        $anglers = Angler::orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
        $lures = Lure::orderBy('name', 'asc')->get();
        
        $years = Record::whereNotNull('caught')
            ->selectRaw('DISTINCT YEAR(caught) as yr')
            ->whereRaw('YEAR(caught) > 1900')
            ->orderByDesc('yr')
            ->pluck('yr')
            ->values();

        return view('map.explorer', [
            'fishBreeds' => $fishBreeds,
            'anglers' => $anglers,
            'lures' => $lures,
            'years' => $years,
        ]);
    }
}
