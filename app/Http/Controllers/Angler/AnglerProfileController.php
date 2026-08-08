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
    public function show(Angler $angler)
    {
        $records = Record::where('anglers_id', $angler->id)
            ->orderBy('caught', 'desc')
            ->get()
            ->groupBy('caught');

        $crews = Crew::where('anglers_id', $angler->id)->count();

        $personalBest = [
            'byLength' => PersonalBestController::bestByLength($angler),
            'byWeight' => PersonalBestController::bestByWeight($angler),
            'lakeWithMostCatches' => PersonalBestController::lakeWithMostCatches($angler),
        ];

        $record_count = Record::where('anglers_id', $angler->id)->count();
        $lake_count = Record::where('anglers_id', $angler->id)
            ->distinct('lakes_id')
            ->count('lakes_id');

        $totalInches = round(Record::where('anglers_id', $angler->id)->sum('length'), 1);
        $totalFeet = round($totalInches / 12, 1);
        $avgLength = round(Record::where('anglers_id', $angler->id)->whereNotNull('length')->avg('length'), 1);
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

        $speciesDistribution = Record::select('fish_breeds_id', DB::raw('count(*) as count'))
            ->where('anglers_id', $angler->id)
            ->whereNotNull('fish_breeds_id')
            ->groupBy('fish_breeds_id')
            ->orderBy('count', 'desc')
            ->with('fishBreed')
            ->get();

        return view('angler.profile', [
            'angler' => $angler,
            'records' => $records,
            'crews' => $crews,
            'personalBest' => $personalBest,
            'record_count' => $record_count,
            'lake_count' => $lake_count,
            'totalInches' => $totalInches,
            'totalFeet' => $totalFeet,
            'avgLength' => $avgLength,
            'releasedCount' => $releasedCount,
            'releaseRate' => $releaseRate,
            'mvpLure' => $mvpLure,
            'peakMonthName' => $peakMonthName,
            'topWaters' => $topWaters,
            'speciesDistribution' => $speciesDistribution,
        ]);
    }
}
