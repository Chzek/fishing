<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\FishBreed;
use Fishinglog\FishFamily;
use Illuminate\Http\Request;
use Fishinglog\Http\Requests\StoreFishBreedRequest;
use Fishinglog\Http\Requests\UpdateFishBreedRequest;

class FishBreedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breed = new FishBreed;

        return view('fish.breed.create', [
            'breed' => $breed,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFishBreedRequest $request)
    {
        //

        $imageName = 'avatar_'.time().'.'.$request->image->getClientOriginalExtension();
        $request->image->storeAs('fish', $imageName);

        $breed = new FishBreed;
        $breed->name = $request->name;
        $breed->fish_families_id = $request->fish_families_id;
        $breed->image = $imageName;

        $breed->save();

        return redirect('/fish');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\FishBreed  $fishBreed
     * @return \Illuminate\Http\Response
     */
    public function show(FishBreed $fishBreed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\FishBreed  $fishBreed
     * @return \Illuminate\Http\Response
     */
    public function edit(FishBreed $fishBreed)
    {
        return view('fish.breed.edit', [
            'breed' => $fishBreed,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\FishBreed  $fishBreed
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFishBreedRequest $request, FishBreed $fishBreed)
    {
        //
        $breed = \Fishinglog\FishBreed::find($request->id);

        $breed->fish_families_id = $request->fish_families_id;
        $breed->name = $request->name;

        if ($request->hasFile('image')) {
            $imageName = 'avatar_'.time().'.'.$request->image->getClientOriginalExtension();
            $request->image->storeAs('fish', $imageName);
            $breed->image = $imageName;
        }

        $breed->save();

        return redirect('/fish/'.$breed->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\FishBreed  $fishBreed
     * @return \Illuminate\Http\Response
     */
    public function destroy(FishBreed $fishBreed)
    {
        //
    }


}
