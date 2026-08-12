<?php

namespace Fishinglog\Http\Controllers\Angler;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Record;
use Illuminate\Support\Facades\DB;

class AnglerStatsController extends Controller
{
    public function index()
    {
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

        // Monthly catch activity distribution (1-12)
        $records = Record::whereNotNull('caught')->get(['caught']);
        $monthCounts = array_fill(1, 12, 0);

        foreach ($records as $rec) {
            if ($rec->caught) {
                $time = strtotime((string) $rec->caught);
                if ($time !== false) {
                    $m = (int) date('n', $time);
                    if ($m >= 1 && $m <= 12) {
                        $monthCounts[$m]++;
                    }
                }
            }
        }

        $maxMonthlyCount = max(max($monthCounts), 1);
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyDistribution = [];
        foreach (range(1, 12) as $m) {
            $monthlyDistribution[] = [
                'name' => $months[$m - 1],
                'count' => $monthCounts[$m],
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

        // Detailed Per-Angler Summary Table Data
        $anglersList = Angler::withCount([
            'records',
            'records as unique_lakes_count' => function ($q) {
                $q->select(DB::raw('count(distinct(lakes_id))'));
            },
            'crews as expeditions_count' => function ($q) {
                $q->select(DB::raw('count(distinct(expeditions_id))'));
            },
        ])->get()->map(function ($angler) {
            $totalCatches = $angler->records_count;

            $avgLength = round(Record::where('anglers_id', $angler->id)->whereNotNull('length')->avg('length'), 1);
            $avgWeight = round(Record::where('anglers_id', $angler->id)->whereNotNull('weight')->avg('weight'), 1);
            $released = Record::where('anglers_id', $angler->id)->where('released', 1)->count();
            $releaseRate = $totalCatches > 0 ? round(($released / $totalCatches) * 100, 1) : 0;

            // Top species for this angler
            $topSpecies = DB::table('records')
                ->join('fish_breeds', 'records.fish_breeds_id', '=', 'fish_breeds.id')
                ->where('records.anglers_id', $angler->id)
                ->whereNull('records.deleted_at')
                ->select('fish_breeds.name', DB::raw('COUNT(*) as cnt'))
                ->groupBy('fish_breeds.name')
                ->orderByDesc('cnt')
                ->first();

            $angler->avg_length = $avgLength > 0 ? $avgLength : null;
            $angler->avg_weight = $avgWeight > 0 ? $avgWeight : null;
            $angler->release_rate = $releaseRate;
            $angler->top_species_name = $topSpecies ? $topSpecies->name : 'N/A';

            return $angler;
        })->sortByDesc('records_count');

        return view('angler.stats', [
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
        ]);
    }
}
