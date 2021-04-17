<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\FishBreed;
use Fishinglog\FishFamily;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        //
        $tempFamilies = \Fishinglog\FishFamily::all();
        $breeds = \Fishinglog\FishBreed::all();
        $breed = new FishBreed;

        $families = [];
        foreach($tempFamilies as $family)
        {
            $families[$family->id] = $family->name;
        }

        return view('fish.breed.create', [
            'families' => $families,
            'breed' => $breed,
            'breeds' => $breeds,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate($this->rules());

        $breed = new FishBreed;
        $breed->name = $request->name;
        $breed->fish_families_id = $request->fish_families_id;

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
        //
        $tempFamilies = \Fishinglog\FishFamily::all();
        foreach($tempFamilies as $family)
        {
            $families[$family->id] = $family->name;
        }
        $breeds = \Fishinglog\FishBreed::all();

        return view('fish.breed.edit', [
            'families' => $families,
            'breeds' => $breeds,
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
    public function update(Request $request, FishBreed $fishBreed)
    {
        //
        $rules = $this->rules();

        // Modify rule to allow for updating the FishBreed
        $rules['name'] = [
            'required',
            Rule::unique('fish_breeds')->ignore($request->id),
        ];

        $request->validate($rules);
        $breed = \Fishinglog\FishBreed::find($request->id);

        $breed->fish_families_id = $request->fish_families_id;
        $breed->name = $request->name;

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

    private function rules(){
        return [
            'fish_families_id' => 'required|exists:fish_families,id',
            'name' => 'required|unique:fish_breeds,name|max:255',
        ];
    }
}
