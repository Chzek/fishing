<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fishes = FishBreed::with(['family'])
            ->withCount('records')
            ->orderBy('fish_families_id', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('fish.index', [
            'fishes' => $fishes,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fish = FishBreed::with(['family'])->findOrFail($id);

        $longest = Record::where('fish_breeds_id', $fish->id)->max('length');
        $fattest = Record::where('fish_breeds_id', $fish->id)->max('weight');
        $count = Record::where('fish_breeds_id', $fish->id)->count();

        $lakes = $fish->records()
            ->select(
                'lakes_id',
                DB::raw('count(*) as count'),
                DB::raw('count(distinct caught) as visits'),
                DB::raw('min(length) as min_length'),
                DB::raw('max(length) as max_length'),
                DB::raw('round(avg(length), 2) as avg_length')
            )
            ->groupBy('lakes_id')
            ->with('lake')
            ->orderBy('count', 'desc')
            ->get();

        return view('fish.show', [
            'fish' => $fish,
            'longest' => $longest,
            'fattest' => $fattest,
            'count' => $count,
            'lakes' => $lakes,
        ]);
    }
}
