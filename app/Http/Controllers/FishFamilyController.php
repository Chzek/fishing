<?php

namespace App\Http\Controllers;

use App\FishFamily;
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
        //
        $families = \App\FishFamily::all();
        $family = new FishFamily;

        return view('fish.family.create', [
            'families' => $families,
            'family' => $family,
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
        $request->validate($this->rules);

        $family = new FishFamily;
        $family->name = $request->name;

        $family->save();

        return redirect('/fish');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FishFamily  $fishFamily
     * @return \Illuminate\Http\Response
     */
    public function show(FishFamily $fishFamily)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FishFamily  $fishFamily
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
     * @param  \App\FishFamily  $fishFamily
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FishFamily $fishFamily)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FishFamily  $fishFamily
     * @return \Illuminate\Http\Response
     */
    public function destroy(FishFamily $fishFamily)
    {
        //
    }

    protected $rules = [
        'name' => [
            'required',
            'unique:fish_families,name',
            'max:255',
        ]
    ];
}
