<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Expedition;
use Illuminate\Http\Request;

class ExpeditionController extends Controller
{
    // Validation rules
    protected $rules = [
        'description' => 'required|string',
        'start' => 'required|date',
        'finish' => 'required|date',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $expeditions = \Fishinglog\Expedition::withCount('posts', 'crews')
            ->orderBy('start', 'desc')
            ->get();

        foreach($expeditions as $expedition)
        {
            $expedition['records_count'] = \Fishinglog\Record::where('caught', '>=', $expedition->start)
                ->where('caught', '<=',  $expedition->finish)
                ->get()
                ->count(); 
        }

        return view('expedition.index', [
            'expeditions' => $expeditions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $expedition = new Expedition;
        return view('expedition.create', [
            'expedition' => $expedition,
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

        $expedition = new Expedition;
        $expedition->description = $request->description;
        $expedition->start = $request->start;
        $expedition->finish = $request->finish;

        $expedition->save();

        return redirect('/expedition');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Expedition  $expedition
     * @return \Illuminate\Http\Response
     */
    public function show(Expedition $expedition, $id)
    {
        //
        $expedition = \Fishinglog\Expedition::with('crews')
            ->find($id);

        $records = \Fishinglog\Record::where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->orderBy('caught', 'desc')
            ->get();

        $caught = \Fishinglog\Record::where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->where('released', '=', 0)
            ->get()
            ->count();        

        return view('expedition.show', [
            'caught' => $caught,
            'recordTotal' => $records->count(),
            'records' => $records,
            'expedition' => $expedition,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Expedition  $expedition
     * @return \Illuminate\Http\Response
     */
    public function edit(Expedition $expedition, $id)
    {
        //
        $expedition = \Fishinglog\Expedition::find($id);
        return view('expedition.edit', [
            'expedition' => $expedition,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\Expedition  $expedition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expedition $expedition)
    {
        //
        $request->validate($this->rules);
        $expedition = \Fishinglog\Expedition::find($request->id);

        $expedition->description = $request->description;
        $expedition->start = $request->start;
        $expedition->finish = $request->finish;

        $expedition->save();

        return redirect('/expedition/'.$request->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Expedition  $expedition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expedition $expedition)
    {
        //
    }
}
