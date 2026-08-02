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
        $anglers = Angler::orderBy('firstname', 'asc')->orderBy('lastname', 'asc')->get();
        $lures = Lure::orderBy('name', 'asc')->get();
        
        $years = Record::whereNotNull('caught')
            ->get()
            ->map(function ($r) {
                return is_a($r->caught, \DateTimeInterface::class) 
                    ? (int) $r->caught->format('Y') 
                    : (int) substr((string) $r->caught, 0, 4);
            })
            ->filter(fn($y) => $y > 1900)
            ->unique()
            ->sortDesc()
            ->values();

        return view('map.explorer', [
            'fishBreeds' => $fishBreeds,
            'anglers' => $anglers,
            'lures' => $lures,
            'years' => $years,
        ]);
    }
}
