<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $selectedFamilyId = $request->query('family');
        $search = $request->query('search');

        $families = FishFamily::withCount('breeds')
            ->orderBy('name', 'asc')
            ->get();

        $query = FishBreed::with(['family'])
            ->withCount('records')
            ->withMax('records as longest_record', 'length')
            ->withMax('records as heaviest_record', 'weight');

        if (!empty($selectedFamilyId)) {
            $query->where('fish_families_id', $selectedFamilyId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('family', function ($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalBreedsCount = FishBreed::count();
        $totalFamiliesCount = FishFamily::count();
        $totalCatchesCount = Record::count();
        $topSpecies = FishBreed::withCount('records')->orderBy('records_count', 'desc')->first();

        $fishes = $query->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('fish.index', [
            'fishes' => $fishes,
            'families' => $families,
            'selectedFamilyId' => $selectedFamilyId,
            'search' => $search,
            'totalBreedsCount' => $totalBreedsCount,
            'totalFamiliesCount' => $totalFamiliesCount,
            'totalCatchesCount' => $totalCatchesCount,
            'topSpecies' => $topSpecies,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fish = FishBreed::with(['family'])->findOrFail($id);

        $longest = Record::where('fish_breeds_id', $fish->id)->max('length');
        $fattest = Record::where('fish_breeds_id', $fish->id)->max('weight');
        $count = Record::where('fish_breeds_id', $fish->id)->count();

        $lakes = $fish->records()
            ->select(
                'lakes_id',
                DB::raw('count(*) as count'),
                DB::raw('count(distinct caught) as visits'),
                DB::raw('min(length) as min_length'),
                DB::raw('max(length) as max_length'),
                DB::raw('round(avg(length), 2) as avg_length')
            )
            ->groupBy('lakes_id')
            ->with('lake')
            ->orderBy('count', 'desc')
            ->get();

        return view('fish.show', [
            'fish' => $fish,
            'longest' => $longest,
            'fattest' => $fattest,
            'count' => $count,
            'lakes' => $lakes,
        ]);
    }
}
