<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreFishFamilyRequest;
use Fishinglog\Models\FishFamily;
use Illuminate\Http\Request;

class FishFamilyController extends Controller
{
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
}
