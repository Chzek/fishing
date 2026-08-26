<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Expedition;
use Fishinglog\Models\Record;
use Illuminate\Support\Facades\DB;

class ExpeditionAnalyticsService
{
    /**
     * Compute full telemetry analytics, accolades, and crew breakdown for an expedition trip.
     *
     * @param Expedition $expedition
     * @return array
     */
    public function getAnalytics(Expedition $expedition): array
    {
        $start = $expedition->start;
        $finish = $expedition->finish;

        // 1. Single-query aggregate telemetry
        $stats = Record::where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->selectRaw('
                COUNT(*) as total_records,
                COALESCE(SUM(length), 0) as total_inches,
                COALESCE(AVG(length), 0) as avg_length,
                COALESCE(SUM(CASE WHEN released = 1 THEN 1 ELSE 0 END), 0) as released_count
            ')
            ->first();

        $totalRecords = (int) ($stats->total_records ?? 0);
        $releasedCount = (int) ($stats->released_count ?? 0);
        $releaseRate = $totalRecords > 0 ? round(($releasedCount / $totalRecords) * 100) : 0;

        // 2. Trip Accolades
        $lunker = Record::where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->with(['angler', 'lake', 'fishBreed', 'lure'])
            ->orderBy('length', 'desc')
            ->first();

        $heavyweight = Record::where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->whereNotNull('weight')
            ->with(['angler', 'lake', 'fishBreed'])
            ->orderBy('weight', 'desc')
            ->first();

        $topRod = Record::select('anglers_id', DB::raw('count(*) as catch_count'), DB::raw('sum(length) as total_length'))
            ->where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->groupBy('anglers_id')
            ->orderBy('catch_count', 'desc')
            ->with('angler')
            ->first();

        $hotLure = Record::select('lures_id', DB::raw('count(*) as catch_count'))
            ->where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->whereNotNull('lures_id')
            ->groupBy('lures_id')
            ->orderBy('catch_count', 'desc')
            ->with('lure')
            ->first();

        // 3. Cadence & Distributions
        $dailyCadence = Record::select('caught', DB::raw('count(*) as count'))
            ->where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->groupBy('caught')
            ->orderBy('caught', 'asc')
            ->get();

        $speciesDistribution = Record::select('fish_breeds_id', DB::raw('count(*) as count'))
            ->where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->groupBy('fish_breeds_id')
            ->orderBy('count', 'desc')
            ->with('fishBreed')
            ->get();

        return [
            'totalRecords' => $totalRecords,
            'releasedCount' => $releasedCount,
            'releaseRate' => $releaseRate,
            'lunker' => $lunker,
            'heavyweight' => $heavyweight,
            'topRod' => $topRod,
            'hotLure' => $hotLure,
            'dailyCadence' => $dailyCadence,
            'speciesDistribution' => $speciesDistribution,
        ];
    }
}
