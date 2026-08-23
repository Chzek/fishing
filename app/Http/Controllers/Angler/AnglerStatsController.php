<?php

namespace Fishinglog\Http\Controllers\Angler;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Record;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnglerStatsController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('angler_stats_overview', 600, function () {
            $totalAnglers = Angler::count();
            $totalRecords = Record::count();

            $avgCatchesPerAngler = $totalAnglers > 0 ? round($totalRecords / $totalAnglers, 1) : 0;

            // Average unique lakes fished per angler
            $lakesPerAnglerSubquery = DB::table('records')
                ->whereNull('deleted_at')
                ->select('anglers_id', DB::raw('COUNT(DISTINCT lakes_id) as lake_count'))
                ->groupBy('anglers_id')
                ->get();

            $avgLakesPerAngler = $totalAnglers > 0 && $lakesPerAnglerSubquery->count() > 0
                ? round($lakesPerAnglerSubquery->avg('lake_count'), 1)
                : 0;

            // Conservation release rate overall
            $releasedCount = Record::where('released', 1)->count();
            $overallReleaseRate = $totalRecords > 0 ? round(($releasedCount / $totalRecords) * 100, 1) : 0;

            // Cumulative length landed
            $totalInches = Record::whereNotNull('length')->sum('length');
            $totalFeet = round($totalInches / 12, 1);
            $avgLengthOverall = round(Record::whereNotNull('length')->avg('length'), 1);
            $avgWeightOverall = round(Record::whereNotNull('weight')->avg('weight'), 1);

            // Monthly catch activity distribution (1-12) via single SQL GROUP BY MONTH
            $monthlyCountsQuery = DB::table('records')
                ->whereNull('deleted_at')
                ->whereNotNull('caught')
                ->select(DB::raw('MONTH(caught) as m'), DB::raw('COUNT(*) as cnt'))
                ->groupBy(DB::raw('MONTH(caught)'))
                ->pluck('cnt', 'm');

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $monthlyDistribution = [];
            $maxMonthlyCount = 1;
            foreach (range(1, 12) as $m) {
                $count = (int) ($monthlyCountsQuery[$m] ?? 0);
                if ($count > $maxMonthlyCount) {
                    $maxMonthlyCount = $count;
                }
                $monthlyDistribution[] = [
                    'name' => $months[$m - 1],
                    'count' => $count,
                ];
            }

            // Angler Activity Tiering Distribution
            $catchCountsPerAngler = DB::table('records')
                ->whereNull('deleted_at')
                ->select('anglers_id', DB::raw('COUNT(*) as total'))
                ->groupBy('anglers_id')
                ->pluck('total');

            $activityTiers = [
                'light' => 0,    // 1-10 catches
                'moderate' => 0, // 11-50 catches
                'avid' => 0,     // 51+ catches
            ];

            foreach ($catchCountsPerAngler as $cnt) {
                if ($cnt <= 10) {
                    $activityTiers['light']++;
                } elseif ($cnt <= 50) {
                    $activityTiers['moderate']++;
                } else {
                    $activityTiers['avid']++;
                }
            }

            // Single query aggregations for all anglers (eliminates N+4 queries!)
            $avgStatsPerAngler = DB::table('records')
                ->whereNull('deleted_at')
                ->select(
                    'anglers_id',
                    DB::raw('AVG(length) as avg_length'),
                    DB::raw('AVG(weight) as avg_weight'),
                    DB::raw('SUM(CASE WHEN released = 1 THEN 1 ELSE 0 END) as released_count')
                )
                ->groupBy('anglers_id')
                ->get()
                ->keyBy('anglers_id');

            // Single query for top species per angler
            $topSpeciesPerAngler = DB::table('records')
                ->join('fish_breeds', 'records.fish_breeds_id', '=', 'fish_breeds.id')
                ->whereNull('records.deleted_at')
                ->select('records.anglers_id', 'fish_breeds.name', DB::raw('COUNT(*) as cnt'))
                ->groupBy('records.anglers_id', 'fish_breeds.name')
                ->orderBy('records.anglers_id')
                ->orderByDesc('cnt')
                ->get()
                ->groupBy('anglers_id')
                ->map(fn($group) => $group->first()->name ?? 'N/A');

            // Detailed Per-Angler Summary Table Data
            $anglersList = Angler::withCount([
                'records',
                'records as unique_lakes_count' => function ($q) {
                    $q->select(DB::raw('count(distinct(lakes_id))'));
                },
                'crews as expeditions_count' => function ($q) {
                    $q->select(DB::raw('count(distinct(expeditions_id))'));
                },
            ])->get()->map(function ($angler) use ($avgStatsPerAngler, $topSpeciesPerAngler) {
                $totalCatches = $angler->records_count;
                $anglerStats = $avgStatsPerAngler->get($angler->id);

                $avgLength = $anglerStats && $anglerStats->avg_length ? round($anglerStats->avg_length, 1) : null;
                $avgWeight = $anglerStats && $anglerStats->avg_weight ? round($anglerStats->avg_weight, 1) : null;
                $released = $anglerStats ? (int) $anglerStats->released_count : 0;
                $releaseRate = $totalCatches > 0 ? round(($released / $totalCatches) * 100, 1) : 0;

                $angler->avg_length = $avgLength;
                $angler->avg_weight = $avgWeight;
                $angler->release_rate = $releaseRate;
                $angler->top_species_name = $topSpeciesPerAngler->get($angler->id, 'N/A');

                return $angler;
            })->sortByDesc('records_count')->values();

            return [
                'totalAnglers' => $totalAnglers,
                'totalRecords' => $totalRecords,
                'avgCatchesPerAngler' => $avgCatchesPerAngler,
                'avgLakesPerAngler' => $avgLakesPerAngler,
                'overallReleaseRate' => $overallReleaseRate,
                'totalFeet' => $totalFeet,
                'totalInches' => $totalInches,
                'avgLengthOverall' => $avgLengthOverall,
                'avgWeightOverall' => $avgWeightOverall,
                'monthlyDistribution' => $monthlyDistribution,
                'maxMonthlyCount' => $maxMonthlyCount,
                'activityTiers' => $activityTiers,
                'anglersList' => $anglersListCollection,
            ];
        });

        $anglersCollection = $stats['anglersList'];
        $page = (int) request()->get('page', 1);
        $perPage = 15;
        $total = $anglersCollection->count();
        $sliced = $anglersCollection->slice(($page - 1) * $perPage, $perPage)->values();

        $stats['anglersList'] = new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('angler.stats', $stats);
    }
}
