<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Pipes\Filters\FilterByAngler;
use Fishinglog\Pipes\Filters\FilterByLength;
use Fishinglog\Record;
use Fishinglog\Pipes\Filters\FilterByName;
use Fishinglog\Pipes\Filters\FilterByRecordsCount;
use Fishinglog\Pipes\Filters\SortBy;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Fishinglog\Http\Requests\StoreRecordRequest;
use Fishinglog\Http\Requests\UpdateRecordRequest;

class RecordController extends Controller
{
    use Notifiable;

    // Validation rules


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Pipeline $pipeline, Request $request)
    {
        //
        $records = \Fishinglog\Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->orderBy('caught', 'desc')
            ->orderBy('lakes_id', 'asc')
            ->orderBy('anglers_id', 'asc');
            // ->paginate(10);

        $records = $pipeline->send($records)
            ->through([
                SortBy::class,
                // FilterByAngler::class,
                FilterByLength::class,
                FilterByName::class,
            ])
            ->thenReturn();

        $records = $records->paginate(10);

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
        $record = \Fishinglog\Record::find($request->record);

        if($record == null)
        {
            $record = new Record;
        }

        return view('record.create', [
            'record' => $record,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRecordRequest $request)
    {
        //

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
        return view('record.edit', [
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
    public function update(UpdateRecordRequest $request, Record $record)
    {
        //
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
