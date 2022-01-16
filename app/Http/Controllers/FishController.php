<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\FishBreed;
use Fishinglog\FishFamily;
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
        //
        $fishes = \Fishinglog\FishBreed::with(['family'])
            ->withCount('records')
            ->orderBy('fish_families_id', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(10);

       // $fishes = $fishes->sortBy('family.name');

        return view('fish.index', [
            'fishes' => $fishes,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  fish_breed.id  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $fish = \Fishinglog\FishBreed::with(['family'])->find($id);

        $longest = \Fishinglog\Record::where('fish_breeds_id', $fish->id)->max('length');
        $fattest = \Fishinglog\Record::where('fish_breeds_id', $fish->id)->max('weight');
        $count = \Fishinglog\Record::where('fish_breeds_id', $fish->id)->count();

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

// dnr.cornell.edu for Fish Family reference and images
