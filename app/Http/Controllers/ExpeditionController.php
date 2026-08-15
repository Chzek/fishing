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
        $expedition->load('photos');

        $records = Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->orderBy('caught', 'desc')
            ->paginate(15);

        $totalRecords = Record::where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->count();

        $releasedCount = Record::where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->where('released', 1)
            ->count();

        $releaseRate = $totalRecords > 0 ? round(($releasedCount / $totalRecords) * 100) : 0;

        // Trip Accolades & Brags
        $lunker = Record::where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->with(['angler', 'lake', 'fishBreed', 'lure'])
            ->orderBy('length', 'desc')
            ->first();

        $heavyweight = Record::where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->whereNotNull('weight')
            ->with(['angler', 'lake', 'fishBreed'])
            ->orderBy('weight', 'desc')
            ->first();

        $topRod = Record::select('anglers_id', DB::raw('count(*) as catch_count'), DB::raw('sum(length) as total_length'))
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->groupBy('anglers_id')
            ->orderBy('catch_count', 'desc')
            ->with('angler')
            ->first();

        $hotLure = Record::select('lures_id', DB::raw('count(*) as catch_count'))
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->whereNotNull('lures_id')
            ->groupBy('lures_id')
            ->orderBy('catch_count', 'desc')
            ->with('lure')
            ->first();

        // Analytics & Visualizations
        $dailyCadence = Record::select('caught', DB::raw('count(*) as count'))
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->groupBy('caught')
            ->orderBy('caught', 'asc')
            ->get();

        $speciesDistribution = Record::select('fish_breeds_id', DB::raw('count(*) as count'))
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->groupBy('fish_breeds_id')
            ->orderBy('count', 'desc')
            ->with('fishBreed')
            ->get();

        $crewLeaderboard = Record::select('anglers_id', DB::raw('count(*) as total_catches'), DB::raw('round(sum(length), 2) as total_length'), DB::raw('max(length) as longest_fish'))
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->groupBy('anglers_id')
            ->orderBy('total_catches', 'desc')
            ->with('angler')
            ->get();

        $recordsWithGps = Record::with(['angler', 'fishBreed'])
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $visitedLakes = \Fishinglog\Models\Lake::whereIn('id', function($query) use ($expedition) {
            $query->select('lakes_id')
                ->from('records')
                ->where('caught', '>=', $expedition->start)
                ->where('caught', '<=',  $expedition->finish)
                ->whereNotNull('lakes_id');
        })
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

        $daysFishedCount = count($dailyCadence);
        $startDate = strtotime($expedition->start);
        $finishDate = strtotime($expedition->finish);
        $totalTripDays = ($startDate && $finishDate && $finishDate >= $startDate) ? max(1, round(($finishDate - $startDate) / 86400) + 1) : 1;
        $dailyAvgCatches = $daysFishedCount > 0 ? round($totalRecords / $daysFishedCount, 1) : 0;

        return view('expedition.show', [
            'totalRecords' => $totalRecords,
            'releasedCount' => $releasedCount,
            'releaseRate' => $releaseRate,
            'daysFishedCount' => $daysFishedCount,
            'totalTripDays' => $totalTripDays,
            'dailyAvgCatches' => $dailyAvgCatches,
            'records' => $records,
            'expedition' => $expedition,
            'lunker' => $lunker,
            'heavyweight' => $heavyweight,
            'topRod' => $topRod,
            'hotLure' => $hotLure,
            'dailyCadence' => $dailyCadence,
            'speciesDistribution' => $speciesDistribution,
            'crewLeaderboard' => $crewLeaderboard,
            'recordsWithGps' => $recordsWithGps,
            'visitedLakes' => $visitedLakes,
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
