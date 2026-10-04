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
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('expedition.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
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
     * @return \Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\View\View
     */
    public function show(Expedition $expedition, \Fishinglog\Services\ExpeditionAnalyticsService $analyticsService)
    {
        $expedition->load('photos');

        $records = Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=',  $expedition->finish)
            ->orderBy('caught', 'desc')
            ->paginate(15);

        $analytics = $analyticsService->getAnalytics($expedition);
        $totalRecords = (int) ($analytics['totalRecords'] ?? 0);
        $releasedCount = (int) ($analytics['releasedCount'] ?? 0);
        $releaseRate = (int) ($analytics['releaseRate'] ?? 0);
        $daysFishedCount = (int) ($analytics['daysFishedCount'] ?? 0);
        $totalTripDays = (int) ($analytics['totalTripDays'] ?? 1);
        $dailyAvgCatches = (float) ($analytics['dailyAvgCatches'] ?? 0);
        $lunker = $analytics['lunker'] ?? null;
        $heavyweight = $analytics['heavyweight'] ?? null;
        $topRod = $analytics['topRod'] ?? null;
        $hotLure = $analytics['hotLure'] ?? null;
        $dailyCadence = $analytics['dailyCadence'] ?? [];
        $speciesDistribution = $analytics['speciesDistribution'] ?? collect();
        $totalAnglersCount = (int) ($analytics['totalAnglersCount'] ?? $analytics['totalUniqueAnglersCount'] ?? 0);
        $crewLeaderboard = $analytics['crewLeaderboard'] ?? collect();

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

        $topCatches = app(\Fishinglog\Services\TrophyScoringService::class)->getTopNormalizedCatches(
            Record::where('caught', '>=', $expedition->start)
                ->where('caught', '<=', $expedition->finish),
            5
        );

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
            'totalAnglersCount' => $totalAnglersCount,
            'crewLeaderboard' => $crewLeaderboard,
            'recordsWithGps' => $recordsWithGps,
            'visitedLakes' => $visitedLakes,
            'topCatches' => $topCatches,
            'stats' => $this->stats($expedition),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\Expedition  $expedition
     * @return \Illuminate\View\View
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
     * @return \Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\Http\RedirectResponse
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
