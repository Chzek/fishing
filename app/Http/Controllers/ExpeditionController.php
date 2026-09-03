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

        $registeredCrewAnglerIds = $expedition->crews()->pluck('anglers_id')->filter()->unique();

        $catchingAnglerIds = Record::where('caught', '>=', $expedition->start)
            ->where('caught', '<=', $expedition->finish)
            ->whereNotNull('anglers_id')
            ->pluck('anglers_id')
            ->unique();

        $allLeaderboardAnglerIds = $registeredCrewAnglerIds->concat($catchingAnglerIds)->unique();

        $tripRecordMetrics = Record::select(
                'anglers_id',
                DB::raw('count(*) as total_catches'),
                DB::raw('round(sum(length), 2) as total_length'),
                DB::raw('max(length) as longest_fish')
            )
            ->where('caught', '>=', $expedition->start)
            ->where('caught', '<=', $expedition->finish)
            ->whereIn('anglers_id', $allLeaderboardAnglerIds)
            ->groupBy('anglers_id')
            ->get()
            ->keyBy('anglers_id');

        $anglers = \Fishinglog\Models\Angler::whereIn('id', $allLeaderboardAnglerIds)->get()->keyBy('id');

        $crewLeaderboard = $allLeaderboardAnglerIds->map(function ($anglerId) use ($anglers, $tripRecordMetrics) {
            $angler = $anglers->get($anglerId);
            if (!$angler) {
                return null;
            }

            $metrics = $tripRecordMetrics->get($anglerId);

            $obj = new \stdClass();
            $obj->anglers_id = $anglerId;
            $obj->angler = $angler;
            $obj->total_catches = $metrics ? (int) $metrics->total_catches : 0;
            $obj->total_length = $metrics ? (float) $metrics->total_length : 0.0;
            $obj->longest_fish = $metrics ? (float) $metrics->longest_fish : 0.0;

            return $obj;
        })->filter()->sort(function ($a, $b) {
            if ($a->total_catches !== $b->total_catches) {
                return $b->total_catches <=> $a->total_catches;
            }
            if ($a->total_length !== $b->total_length) {
                return $b->total_length <=> $a->total_length;
            }
            return strcmp($a->angler->fullName, $b->angler->fullName);
        })->values();

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
        $startDate = strtotime((string) $expedition->start);
        $finishDate = strtotime((string) $expedition->finish);
        $totalTripDays = ($startDate && $finishDate && $finishDate >= $startDate) ? (int) max(1, round(($finishDate - $startDate) / 86400) + 1) : 1;
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
