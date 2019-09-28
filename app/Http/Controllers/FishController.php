<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\FishBreed;
use Fishinglog\FishFamily;
use Illuminate\Http\Request;

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

        return view('fish.show', [
            'fish' => $fish,
            'longest' => $longest,
            'fattest' => $fattest,
            'count' => $count,
        ]);
    }
}

// dnr.cornell.edu for Fish Family reference and images
