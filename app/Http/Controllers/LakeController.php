<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Actions\Lakes\CreateLakeAction;
use Fishinglog\Actions\Lakes\UpdateLakeAction;
use Fishinglog\Http\Requests\StoreLakeRequest;
use Fishinglog\Http\Requests\UpdateLakeRequest;
use Fishinglog\Models\FishingZone;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Services\TrophyScoringService;
use Illuminate\Support\Facades\DB;

class LakeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('lake.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $lake = new Lake();
        $fishingZones = FishingZone::orderBy('code', 'asc')->get();

        return view('lake.create', [
            'lake' => $lake,
            'fishingZones' => $fishingZones,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreLakeRequest  $request
     * @param  CreateLakeAction  $createLakeAction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreLakeRequest $request, CreateLakeAction $createLakeAction)
    {
        $createLakeAction->execute($request->validated());

        return redirect('/lake');
    }

    /**
     * Display the specified resource.
     *
     * @param  Lake  $lake
     * @param  TrophyScoringService  $trophyScoringService
     * @return \Illuminate\View\View
     */
    public function show(Lake $lake, TrophyScoringService $trophyScoringService)
    {
        $lake->load('fishingZone.rules');

        // Consolidated single-pass aggregate query (total catches, unique visits, unique anglers)
        $aggregates = Record::where('lakes_id', $lake->id)
            ->selectRaw('
                COUNT(*) as total_count,
                COUNT(DISTINCT caught) as unique_visits,
                COUNT(DISTINCT anglers_id) as unique_anglers
            ')
            ->first();

        $count = (int) ($aggregates->total_count ?? 0);
        $visits = (int) ($aggregates->unique_visits ?? 0);
        $anglers = (int) ($aggregates->unique_anglers ?? 0);

        $longest = Record::where('lakes_id', $lake->id)
            ->whereNotNull('length')
            ->with(['fishBreed', 'angler'])
            ->orderBy('length', 'desc')
            ->first();

        $fattest = Record::where('lakes_id', $lake->id)
            ->whereNotNull('weight')
            ->with(['fishBreed', 'angler'])
            ->orderBy('weight', 'desc')
            ->first();

        $topCatches = $trophyScoringService->getTopNormalizedCatches(
            Record::where('lakes_id', $lake->id),
            5
        );

        return view('lake.show', [
            'lake' => $lake,
            'count' => $count,
            'longest' => $longest,
            'fattest' => $fattest,
            'visits' => $visits,
            'anglers' => $anglers,
            'topCatches' => $topCatches,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Lake  $lake
     * @return \Illuminate\View\View
     */
    public function edit(Lake $lake)
    {
        $lake->load('fishingZone');
        $nearbyLakes = Lake::nearby($lake->latitude, $lake->longitude, 2.0, $lake->id);
        $fishingZones = FishingZone::orderBy('code', 'asc')->get();

        return view('lake.edit', [
            'lake' => $lake,
            'nearbyLakes' => $nearbyLakes,
            'fishingZones' => $fishingZones,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateLakeRequest  $request
     * @param  Lake  $lake
     * @param  UpdateLakeAction  $updateLakeAction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateLakeRequest $request, Lake $lake, UpdateLakeAction $updateLakeAction)
    {
        $targetLake = Lake::find($request->id) ?? $lake;
        $updatedLake = $updateLakeAction->execute($targetLake, $request->validated());

        return redirect('/lake/' . $updatedLake->id);
    }

    /**
     * Compute per-species summary statistics for a lake.
     *
     * @param  Lake  $lake
     * @param  int|null  $quantity
     * @return \Illuminate\Database\Eloquent\Collection<int, Record>
     */
    public function stats(Lake $lake, ?int $quantity = null)
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
            ->where('lakes_id', $lake->id)
            ->with('fishBreed')
            ->groupBy('fish_breeds_id')
            ->orderBy('cnt', 'desc');

        if (!is_null($quantity)) {
            $query->limit($quantity);
        }

        return $query->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Lake  $lake
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Lake $lake)
    {
        $lake->delete();

        return redirect('/lake')->with('status', 'Lake removed successfully.');
    }
}
