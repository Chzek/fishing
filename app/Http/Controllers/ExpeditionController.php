<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreExpeditionRequest;
use Fishinglog\Http\Requests\UpdateExpeditionRequest;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpeditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expeditions = Expedition::withCount('posts', 'crews')
            ->addSelect(['records_count' => Record::selectRaw('count(*)')
                ->whereColumn('caught', '>=', 'expeditions.start')
                ->whereColumn('caught', '<=', 'expeditions.finish')
            ])
            ->orderBy('start', 'desc')
            ->get();

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
        $expedition = new Expedition;
        return view('expedition.create', [
            'expedition' => $expedition,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreExpeditionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExpeditionRequest $request)
    {
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
     * @param  \Fishinglog\Models\Expedition  $expedition
     * @return \Illuminate\Http\Response
     */
    public function show(Expedition $expedition)
    {
        $records = Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->orderBy('caught', 'desc')
            ->paginate(10);

        $caught = Record::where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->where('released', '=', 0)
            ->count();

        return view('expedition.show', [
            'caught' => $caught,
            'recordTotal' => $records->count(),
            'records' => $records,
            'expedition' => $expedition,
            'stats' => $this->stats($expedition),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\Expedition  $expedition
     * @return \Illuminate\Http\Response
     */
    public function edit(Expedition $expedition)
    {
        return view('expedition.edit', [
            'expedition' => $expedition,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\UpdateExpeditionRequest  $request
     * @param  \Fishinglog\Models\Expedition  $expedition
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpeditionRequest $request, Expedition $expedition)
    {
        $targetExpedition = Expedition::find($request->id) ?? $expedition;

        $targetExpedition->description = $request->description;
        $targetExpedition->start = $request->start;
        $targetExpedition->finish = $request->finish;

        $targetExpedition->save();

        return redirect('/expedition/' . $targetExpedition->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Expedition  $expedition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expedition $expedition)
    {
        $expedition->delete();

        return redirect('/expedition')->with('status', 'Expedition removed successfully.');
    }

    public function stats(Expedition $expedition, $quantity = null)
    {
        $query = Record::select(
            'fish_breeds_id',
            DB::raw('count(*) as cnt'),
            DB::raw('round(avg(length), 2) as avg_length'),
            DB::raw('min(length) as min_length'),
            DB::raw('max(length) as max_length'),
            DB::raw('round(avg(weight), 2) as avg_weight'),
            DB::raw('min(weight) as min_weight'),
            DB::raw('max(weight) as max_weight'),
            DB::raw('sum(if(weight IS NOT NULL, 1, 0)) as weighed_count')
        )
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->with('fishBreed')
            ->groupBy('fish_breeds_id')
            ->orderBy('cnt', 'desc');

        if (!is_null($quantity)) {
            $query->limit($quantity);
        }

        return $query->get();
    }
}
