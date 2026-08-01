<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreCrewRequest;
use Fishinglog\Http\Requests\UpdateCrewRequest;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Expedition;
use Illuminate\Http\Request;

class CrewController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $crew = new Crew;
        $expedition = Expedition::find($request->expeditions_id);

        return view('expedition.crew.create', [
            'expedition' => $expedition,
            'crew' => $crew,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreCrewRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCrewRequest $request)
    {
        $crew = new Crew;
        $crew->expeditions_id = $request->expeditions_id;
        $crew->anglers_id = $request->anglers_id;
        $crew->joined = $request->joined;

        $crew->save();

        return redirect('/expedition/' . $request->expeditions_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Models\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function show(Crew $crew)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function edit(Crew $crew)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\UpdateCrewRequest  $request
     * @param  \Fishinglog\Models\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCrewRequest $request, Crew $crew)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function destroy(Crew $crew)
    {
        //
    }
}
