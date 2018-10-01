<?php

namespace App\Http\Controllers;

use App\Crew;
use Illuminate\Http\Request;

class CrewController extends Controller
{
    // Validation rules
    protected $rules = [
        'expeditions_id' => 'integer',
        'anglers_id' => 'integer',
        'joined' => 'date',
    ];

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
    public function create(Request $request)
    {
        //
        $crew = new Crew;
        $temp = \App\Angler::orderBy('lastName', 'asc')
            ->orderBy('firstName', 'asc')
            ->orderBy('middleName', 'asc')
            ->get();

        $anglers[null] = "Select an Angler";
        foreach($temp as $angler)
        {
            $anglers[$angler->id] = $angler->fullName;
        }

        $temp = \App\Expedition::all();
        foreach($temp as $expedition)
        {
            $expeditions[$expedition->id] = $expedition->description;
        }

        $expedition = \App\Expedition::find($request->expeditions_id);

        return view('expedition.crew.create', [
            'anglers' => $anglers,
            'expeditions' => $expeditions,
            'expedition' => $expedition,
            'crew' => $crew,
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

        $crew = new Crew;
        $crew->expeditions_id = $request->expeditions_id;
        $crew->anglers_id = $request->anglers_id;
        $crew->joined = $request->joined;

        $crew->save();

        return redirect('/expedition/'.$request->expeditions_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function show(Crew $crew)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function edit(Crew $crew)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Crew $crew)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Crew  $crew
     * @return \Illuminate\Http\Response
     */
    public function destroy(Crew $crew)
    {
        //
    }
}
