<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExplorerApiController extends Controller
{
    /**
     * Get visited lakes within the bounding box matching active filters.
     */
    public function lakes(Request $request)
    {
        $minLat = $request->query('min_lat');
        $maxLat = $request->query('max_lat');
        $minLng = $request->query('min_lng');
        $maxLng = $request->query('max_lng');

        $fishBreedId = $request->query('fish_breed_id');
        $anglerId = $request->query('angler_id');
        $lureId = $request->query('lure_id');
        $isTrophy = $request->query('is_trophy');
        $year = $request->query('year');

        $query = Lake::whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Bounding box filtering if coordinates provided
        if (!is_null($minLat) && !is_null($maxLat) && !is_null($minLng) && !is_null($maxLng)) {
            $query->whereBetween('latitude', [(float)$minLat, (float)$maxLat])
                  ->whereBetween('longitude', [(float)$minLng, (float)$maxLng]);
        }

        // Apply Record Filters via whereHas('records')
        if ($fishBreedId || $anglerId || $lureId || $isTrophy || $year) {
            $query->whereHas('records', function ($q) use ($fishBreedId, $anglerId, $lureId, $isTrophy, $year) {
                if ($fishBreedId) {
                    $q->where('fish_breeds_id', $fishBreedId);
                }
                if ($anglerId) {
                    $q->where('anglers_id', $anglerId);
                }
                if ($lureId) {
                    $q->where('lures_id', $lureId);
                }
                if ($isTrophy) {
                    $q->where(function ($tQ) {
                        $tQ->where('weight', '>=', 10)
                           ->orWhere('length', '>=', 20);
                    });
                }
                if ($year) {
                    $q->whereYear('caught', $year);
                }
            });
        }

        $lakes = $query->withCount('records')
            ->withCount(['records as visits_count' => function ($q) {
                $q->select(DB::raw('count(distinct records.caught)'));
            }])
            ->get();

        return response()->json([
            'data' => $lakes,
            'count' => $lakes->count(),
        ]);
    }

    /**
     * Get rich catch analytics for a specific lake (Right Slide-Over Drawer).
     */
    public function lakeDetail(Request $request, Lake $lake)
    {
        $fishBreedId = $request->query('fish_breed_id');
        $anglerId = $request->query('angler_id');
        $lureId = $request->query('lure_id');
        $isTrophy = $request->query('is_trophy');
        $year = $request->query('year');

        // Base records query for this lake
        $recordsQuery = Record::where('lakes_id', $lake->id);

        if ($fishBreedId) {
            $recordsQuery->where('fish_breeds_id', $fishBreedId);
        }
        if ($anglerId) {
            $recordsQuery->where('anglers_id', $anglerId);
        }
        if ($lureId) {
            $recordsQuery->where('lures_id', $lureId);
        }
        if ($isTrophy) {
            $recordsQuery->where(function ($tQ) {
                $tQ->where('weight', '>=', 10)
                   ->orWhere('length', '>=', 20);
            });
        }
        if ($year) {
            $recordsQuery->whereYear('caught', $year);
        }

        $totalCatches = (clone $recordsQuery)->count();
        $visitsCount = (clone $recordsQuery)->distinct('caught')->count('caught');
        $anglersCount = (clone $recordsQuery)->distinct('anglers_id')->count('anglers_id');

        // Longest & Fattest catch
        $longest = (clone $recordsQuery)->with('fishBreed')->orderBy('length', 'desc')->first();
        $fattest = (clone $recordsQuery)->whereNotNull('weight')->with('fishBreed')->orderBy('weight', 'desc')->first();

        // Species Breakdown
        $speciesBreakdown = (clone $recordsQuery)
            ->select(
                'fish_breeds_id',
                DB::raw('count(*) as count'),
                DB::raw('round(avg(length), 1) as avg_length'),
                DB::raw('max(length) as max_length'),
                DB::raw('round(avg(weight), 2) as avg_weight'),
                DB::raw('max(weight) as max_weight')
            )
            ->with('fishBreed')
            ->groupBy('fish_breeds_id')
            ->orderBy('count', 'desc')
            ->get();

        // Top 3 Lures
        $topLures = (clone $recordsQuery)
            ->whereNotNull('lures_id')
            ->select('lures_id', DB::raw('count(*) as count'))
            ->with('lure')
            ->groupBy('lures_id')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'lake' => $lake,
            'total_catches' => $totalCatches,
            'visits_count' => $visitsCount,
            'anglers_count' => $anglersCount,
            'longest_catch' => $longest,
            'fattest_catch' => $fattest,
            'species_breakdown' => $speciesBreakdown,
            'top_lures' => $topLures,
        ]);
    }
}
