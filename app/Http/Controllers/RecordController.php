<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Record;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    use Notifiable;

    // Validation rules
    protected $rules = [
        'anglers_id' => 'integer|required',
        'lakes_id' => 'integer|required',
        'fish_breeds_id' => 'integer|required',
        #'lures_id' => 'integer',
        #'weight' => 'numeric',
        'length' => 'numeric|required',
        #'temperature' => 'numeric',
        'released' => 'boolean|required',
        'caught' => 'date|required',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $records = \Fishinglog\Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->orderBy('caught', 'desc')
            ->orderBy('anglers_id', 'asc')
            ->paginate(10);

        return view('record.index', [
            'records' => $records
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $temp = \Fishinglog\Angler::orderBy('lastName', 'asc')
            ->orderBy('firstName', 'asc')
            ->orderBy('middleName', 'asc')
            ->get();

        $anglers[null] = "Select an Angler";
        foreach($temp as $angler)
        {
            $anglers[$angler->id] = $angler->fullName;
        }

        $temp = \Fishinglog\Lake::orderBy('name', 'asc')
            ->get();

        $lakes[null] = "Select a Lake";
        foreach($temp as $lake)
        {
            $lakes[$lake->id] = $lake->name;
        }

        $temp = \Fishinglog\FishBreed::orderBy('name', 'asc')
            ->get();

        $fishes[null] = "Select a Fish";
        foreach($temp as $fish)
        {
            $fishes[$fish->id] = $fish->name;
        }

        $temp = \Fishinglog\Lure::orderBy('name', 'asc')
            ->orderBy('color', 'asc')
            ->orderBy('size', 'desc')
            ->get();

        $lures[null] = "Select a Lure";
        foreach($temp as $lure)
        {
            $lures[$lure->id] = $lure->displayName;
        }

        $record = \Fishinglog\Record::find($request->record);

        if($record == null)
        {
            $record = new Record;
        }

        return view('record.create', [
            'anglers' => $anglers,
            'lakes' => $lakes,
            'fishes' => $fishes,
            'lures' => $lures,
            'record' => $record,
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

        $record = new Record;
        $record->anglers_id = $request->anglers_id;
        $record->lakes_id = $request->lakes_id;
        $record->fish_breeds_id = $request->fish_breeds_id;
        $record->lures_id = $request->lures_id;
        $record->weight = $request->weight;
        $record->length = $request->length;
        $record->temperature = $request->temperature;
        $record->released = $request->released;
        $record->caught = $request->caught;

        $record->save();
        
        return redirect()->action(
            'RecordController@create',
            [ 'record' => $record ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Record $record)
    {
        return view('record.show', [
            'record' => $record,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function edit(Record $record)
    {
        $temp = \Fishinglog\Angler::orderBy('lastName', 'asc')
            ->orderBy('firstName', 'asc')
            ->orderBy('middleName', 'asc')
            ->get();

        $anglers[null] = "Select an Angler";
        foreach($temp as $angler)
        {
            $anglers[$angler->id] = $angler->fullName;
        }

        $temp = \Fishinglog\Lake::orderBy('name', 'asc')
            ->get();

        $lakes[null] = "Select a Lake";
        foreach($temp as $lake)
        {
            $lakes[$lake->id] = $lake->name;
        }

        $temp = \Fishinglog\FishBreed::orderBy('name', 'asc')
            ->get();

        $fishes[null] = "Select a Fish";
        foreach($temp as $fish)
        {
            $fishes[$fish->id] = $fish->name;
        }

        $temp = \Fishinglog\Lure::orderBy('name', 'asc')
            ->orderBy('color', 'asc')
            ->orderBy('size', 'desc')
            ->get();

        $lures[null] = "Select a Lure";
        foreach($temp as $lure)
        {
            $lures[$lure->id] = $lure->displayName;
        }

        return view('record.edit', [
            'anglers' => $anglers,
            'lakes' => $lakes,
            'fishes' => $fishes,
            'lures' => $lures,
            'record' => $record,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Record $record)
    {
        //
        $request->validate($this->rules);
        $record = \Fishinglog\Record::find($request->id);

        $record->anglers_id = $request->anglers_id;
        $record->lakes_id = $request->lakes_id;
        $record->fish_breeds_id = $request->fish_breeds_id;
        $record->lures_id = $request->lures_id;
        $record->weight = $request->weight;
        $record->length = $request->length;
        $record->temperature = $request->temperature;
        $record->released = $request->released;
        $record->caught = $request->caught;

        $record->save();

        return redirect('/record/'.$request->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function destroy(Record $record)
    {
        //
    }
}
