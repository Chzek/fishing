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

            // 5-axis Angler Multi-Skill Radar for Top Crew Members
            $anglerDiversityAndMax = DB::table('records')
                ->whereNull('deleted_at')
                ->select(
                    'anglers_id',
                    DB::raw('COUNT(DISTINCT fish_breeds_id) as diversity_count'),
                    DB::raw('MAX(length) as max_length')
                )
                ->groupBy('anglers_id')
                ->get()
                ->keyBy('anglers_id');

            $maxVolume = max($anglersList->max('records_count') ?: 1, 1);
            $maxLunkerOverall = max($anglerDiversityAndMax->max('max_length') ?: 1, 1);
            $maxDiversityOverall = max($anglerDiversityAndMax->max('diversity_count') ?: 1, 1);
            $maxWatersOverall = max($anglersList->max('unique_lakes_count') ?: 1, 1);

            $radarColors = [
                ['border' => '#14b8a6', 'bg' => 'rgba(20, 184, 166, 0.2)'], // Teal
                ['border' => '#38bdf8', 'bg' => 'rgba(56, 189, 248, 0.2)'], // Sky
                ['border' => '#fbbf24', 'bg' => 'rgba(251, 191, 36, 0.2)'], // Amber
                ['border' => '#a78bfa', 'bg' => 'rgba(167, 139, 250, 0.2)'], // Purple
            ];

            $radarDatasets = [];
            $topAnglersForRadar = $anglersList->filter(fn($a) => $a->records_count > 0)->take(3);

            foreach ($topAnglersForRadar as $idx => $a) {
                $divData = $anglerDiversityAndMax->get($a->id);
                $volScore = min(100, round(($a->records_count / $maxVolume) * 100));
                $lunkerScore = min(100, round((($divData?->max_length ?? 0) / $maxLunkerOverall) * 100));
                $releaseScore = min(100, round($a->release_rate));
                $diversityScore = min(100, round((($divData?->diversity_count ?? 0) / $maxDiversityOverall) * 100));
                $watersScore = min(100, round((($a->unique_lakes_count ?? 0) / $maxWatersOverall) * 100));

                $color = $radarColors[$idx % count($radarColors)];
                $radarDatasets[] = [
                    'label' => $a->full_name,
                    'data' => [$volScore, $lunkerScore, $releaseScore, $diversityScore, $watersScore],
                    'borderColor' => $color['border'],
                    'backgroundColor' => $color['bg'],
                    'pointBackgroundColor' => $color['border'],
                    'pointHoverBorderColor' => '#ffffff',
                    'borderWidth' => 2,
                ];
            }

            $crewRadarChart = [
                'labels' => ['Volume', 'Lunker Max', 'C&R Rate', 'Species Diversity', 'Active Waters'],
                'datasets' => $radarDatasets,
            ];

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
                'anglersList' => $anglersList,
                'crewRadarChart' => $crewRadarChart,
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
