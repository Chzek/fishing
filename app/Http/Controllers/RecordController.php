<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreRecordRequest;
use Fishinglog\Http\Requests\UpdateRecordRequest;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Pipes\Filters\FilterByAngler;
use Fishinglog\Pipes\Filters\FilterByLength;
use Fishinglog\Pipes\Filters\FilterByName;
use Fishinglog\Pipes\Filters\FilterByRecordsCount;
use Fishinglog\Pipes\Filters\SortBy;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    use Notifiable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Pipeline $pipeline, Request $request)
    {
        $records = Record::with(['angler', 'lake', 'fishBreed', 'lure', 'dailyWeather'])
            ->orderBy('caught', 'desc')
            ->orderBy('lakes_id', 'asc')
            ->orderBy('anglers_id', 'asc');

        $records = $pipeline->send($records)
            ->through([
                SortBy::class,
                FilterByLength::class,
                FilterByName::class,
            ])
            ->thenReturn();

        $records = $records->paginate(10);

        return view('record.index', [
            'records' => $records,
        ]);
    }

    /**
     * Show touch-optimized quick catch form for boat logging.
     *
     * @return \Illuminate\Http\Response
     */
    public function quick()
    {
        return view('record.quick', [
            'anglers' => Angler::orderBy('lastName', 'asc')->get(),
            'lakes' => Lake::orderBy('name', 'asc')->get(),
            'fishBreeds' => FishBreed::orderBy('name', 'asc')->get(),
            'lures' => Lure::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $record = Record::find($request->record);

        if ($record == null) {
            $record = new Record;
        }

        return view('record.create', [
            'record' => $record,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreRecordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRecordRequest $request)
    {
        $record = new Record;
        $record->client_id = $request->client_id;
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
            [self::class, 'create'],
            ['record' => $record->id]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Models\Record  $record
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
     * @param  \Fishinglog\Models\Record  $record
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
     * @param  \Fishinglog\Http\Requests\UpdateRecordRequest  $request
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRecordRequest $request, Record $record)
    {
        $targetRecord = Record::find($request->id) ?? $record;

        $targetRecord->anglers_id = $request->anglers_id;
        $targetRecord->lakes_id = $request->lakes_id;
        $targetRecord->fish_breeds_id = $request->fish_breeds_id;
        $targetRecord->lures_id = $request->lures_id;
        $targetRecord->weight = $request->weight;
        $targetRecord->length = $request->length;
        $targetRecord->temperature = $request->temperature;
        $targetRecord->released = $request->released;
        $targetRecord->caught = $request->caught;

        $targetRecord->save();

        return redirect('/record/' . $targetRecord->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function destroy(Record $record)
    {
        //
    }
}
