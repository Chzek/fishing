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
            ->orderBy('name', 'asc')
            ->get();

        return view('fish.index', [
            'fishes' => $fishes
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

            return view('fish.show', [
            'fish' => $fish
        ]);
    }
}

// dnr.cornell.edu for Fish Family reference and images
