<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\Record;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

        $daysFishedCount = $dailyCadence->count();

        $totalTripDays = 1;
        if ($start && $finish) {
            $startDate = Carbon::parse($start)->startOfDay();
            $finishDate = Carbon::parse($finish)->startOfDay();
            $totalTripDays = max(1, $startDate->diffInDays($finishDate) + 1);
        }

        $dailyAvgCatches = $totalTripDays > 0 ? round($totalRecords / $totalTripDays, 1) : 0;

        $speciesDistribution = Record::select('fish_breeds_id', DB::raw('count(*) as count'))
            ->where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->groupBy('fish_breeds_id')
            ->orderBy('count', 'desc')
            ->with('fishBreed')
            ->get();

        // 4. Distinct Angler Association (Union of Registered Crew + Catch Logs)
        $registeredCrewAnglerIds = $expedition->crews()->pluck('anglers_id')->filter();

        $catchingAnglerIds = ($start && $finish)
            ? Record::where('caught', '>=', $start)
                ->where('caught', '<=', $finish)
                ->whereNotNull('anglers_id')
                ->pluck('anglers_id')
            : collect();

        $allAnglerIds = $registeredCrewAnglerIds->concat($catchingAnglerIds)->unique()->values();
        $totalAnglersCount = $allAnglerIds->count();

        $tripRecordMetrics = Record::select(
                'anglers_id',
                DB::raw('count(*) as total_catches'),
                DB::raw('round(sum(length), 2) as total_length'),
                DB::raw('max(length) as longest_fish')
            )
            ->where('caught', '>=', $start)
            ->where('caught', '<=', $finish)
            ->whereIn('anglers_id', $allAnglerIds)
            ->groupBy('anglers_id')
            ->get()
            ->keyBy('anglers_id');

        $anglers = Angler::whereIn('id', $allAnglerIds)->get()->keyBy('id');

        /** @var Collection<int, object> $crewLeaderboard */
        $crewLeaderboard = $allAnglerIds->map(function ($anglerId) use ($anglers, $tripRecordMetrics, $registeredCrewAnglerIds) {
            $angler = $anglers->get($anglerId);
            if (!$angler) {
                return null;
            }

            $metrics = $tripRecordMetrics->get($anglerId);
            $catches = $metrics ? (int) $metrics->total_catches : 0;

            $obj = new \stdClass();
            $obj->anglers_id = $anglerId;
            $obj->angler = $angler;
            $obj->total_catches = $catches;
            $obj->total_length = $metrics ? (float) $metrics->total_length : 0.0;
            $obj->longest_fish = $metrics ? (float) $metrics->longest_fish : 0.0;
            $obj->is_roster_crew = $registeredCrewAnglerIds->contains($anglerId);
            $obj->is_active_catcher = ($catches > 0);

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

        return [
            'totalRecords' => $totalRecords,
            'releasedCount' => $releasedCount,
            'releaseRate' => $releaseRate,
            'daysFishedCount' => $daysFishedCount,
            'totalTripDays' => $totalTripDays,
            'dailyAvgCatches' => $dailyAvgCatches,
            'lunker' => $lunker,
            'heavyweight' => $heavyweight,
            'topRod' => $topRod,
            'hotLure' => $hotLure,
            'dailyCadence' => $dailyCadence,
            'speciesDistribution' => $speciesDistribution,
            'totalAnglersCount' => $totalAnglersCount,
            'totalUniqueAnglersCount' => $totalAnglersCount,
            'crewLeaderboard' => $crewLeaderboard,
        ];
    }
}

