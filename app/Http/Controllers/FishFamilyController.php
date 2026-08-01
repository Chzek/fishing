<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreFishFamilyRequest;
use Fishinglog\Models\FishFamily;
use Illuminate\Http\Request;

class FishFamilyController extends Controller
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
        $families = FishFamily::all();
        $family = new FishFamily;

        return view('fish.family.create', [
            'families' => $families,
            'family' => $family,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreFishFamilyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFishFamilyRequest $request)
    {
        $family = new FishFamily;
        $family->name = $request->name;

        $family->save();

        return redirect('/fish');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Models\FishFamily  $fishFamily
     * @return \Illuminate\Http\Response
     */
    public function show(FishFamily $fishFamily)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\FishFamily  $fishFamily
     * @return \Illuminate\Http\Response
     */
    public function edit(FishFamily $fishFamily)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\Models\FishFamily  $fishFamily
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FishFamily $fishFamily)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\FishFamily  $fishFamily
     * @return \Illuminate\Http\Response
     */
    public function destroy(FishFamily $fishFamily)
    {
        //
    }
}
