<?php

namespace Fishinglog\Http\Controllers\Angler;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Http\Controllers\PersonalBestController;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Record;
use Illuminate\Support\Facades\DB;

class AnglerProfileController extends Controller
{
    public function show(Angler $angler, \Illuminate\Pipeline\Pipeline $pipeline, \Illuminate\Http\Request $request)
    {
        $longest = Record::where('anglers_id', $angler->id)
            ->whereNotNull('length')
            ->orderBy('length', 'desc')
            ->with(['fishBreed', 'lake', 'lure'])
            ->first();

        $fattest = Record::where('anglers_id', $angler->id)
            ->whereNotNull('weight')
            ->orderBy('weight', 'desc')
            ->with(['fishBreed', 'lake', 'lure'])
            ->first();

        $crews = Crew::where('anglers_id', $angler->id)->count();

        $personalBest = [
            'byLength' => $longest,
            'byWeight' => $fattest,
            'lakeWithMostCatches' => PersonalBestController::lakeWithMostCatches($angler),
        ];

        // Pipeline query for interactive angler catches table
        $catchesQuery = Record::where('anglers_id', $angler->id)
            ->with(['fishBreed', 'lake', 'lure', 'angler']);

        $catchesQuery = $pipeline->send($catchesQuery)
            ->through([
                \Fishinglog\Pipes\Filters\SortBy::class,
                \Fishinglog\Pipes\Filters\FilterBySearch::class,
            ])
            ->thenReturn();

        $anglerRecords = $catchesQuery->paginate(15)->withQueryString();

        $recordsGrouped = Record::where('anglers_id', $angler->id)
            ->orderBy('caught', 'desc')
            ->with(['fishBreed', 'lake', 'lure'])
            ->get()
            ->groupBy('caught');

        $record_count = Record::where('anglers_id', $angler->id)->count();
        $lake_count = Record::where('anglers_id', $angler->id)
            ->distinct('lakes_id')
            ->count('lakes_id');

        $totalInches = round(Record::where('anglers_id', $angler->id)->sum('length'), 1);
        $totalFeet = round($totalInches / 12, 1);
        $avgLength = round(Record::where('anglers_id', $angler->id)->whereNotNull('length')->avg('length'), 1);
        $avgWeight = round(Record::where('anglers_id', $angler->id)->whereNotNull('weight')->avg('weight'), 1);
        $releasedCount = Record::where('anglers_id', $angler->id)->where('released', 1)->count();
        $releaseRate = $record_count > 0 ? round(($releasedCount / $record_count) * 100) : 0;

        $mvpLure = Record::select('lures_id', DB::raw('count(*) as catches'), DB::raw('max(length) as longest'))
            ->where('anglers_id', $angler->id)
            ->whereNotNull('lures_id')
            ->groupBy('lures_id')
            ->orderBy('catches', 'desc')
            ->with('lure')
            ->first();

        $peakMonth = Record::select(DB::raw('month(caught) as month_num'), DB::raw('count(*) as count'))
            ->where('anglers_id', $angler->id)
            ->whereNotNull('caught')
            ->groupBy(DB::raw('month(caught)'))
            ->orderBy('count', 'desc')
            ->first();

        $peakMonthName = $peakMonth ? \DateTime::createFromFormat('!m', $peakMonth->month_num)->format('F') : null;

        $topWaters = Record::select('lakes_id', DB::raw('count(*) as catches'), DB::raw('max(length) as longest'))
            ->where('anglers_id', $angler->id)
            ->whereNotNull('lakes_id')
            ->groupBy('lakes_id')
            ->orderBy('catches', 'desc')
            ->take(3)
            ->with('lake')
            ->get();

        $speciesDistribution = Record::select('fish_breeds_id', DB::raw('count(*) as count'), DB::raw('round(avg(length), 1) as avg_len'), DB::raw('round(avg(weight), 1) as avg_wt'))
            ->where('anglers_id', $angler->id)
            ->whereNotNull('fish_breeds_id')
            ->groupBy('fish_breeds_id')
            ->orderBy('count', 'desc')
            ->with('fishBreed')
            ->get();

        return view('angler.profile', [
            'angler' => $angler,
            'records' => $recordsGrouped,
            'anglerRecords' => $anglerRecords,
            'longest' => $longest,
            'fattest' => $fattest,
            'crews' => $crews,
            'personalBest' => $personalBest,
            'record_count' => $record_count,
            'lake_count' => $lake_count,
            'totalInches' => $totalInches,
            'totalFeet' => $totalFeet,
            'avgLength' => $avgLength,
            'avgWeight' => $avgWeight,
            'releasedCount' => $releasedCount,
            'releaseRate' => $releaseRate,
            'mvpLure' => $mvpLure,
            'peakMonthName' => $peakMonthName,
            'topWaters' => $topWaters,
            'speciesDistribution' => $speciesDistribution,
        ]);
    }

}
