<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnglerTelemetryService
{
    public function __construct(
        public TrophyScoringService $trophyScoringService
    ) {
    }

    /**
     * Compute consolidated career telemetry, personal bests, species distributions,
     * and normalized trophy rankings for an angler.
     *
     * @param Angler|null $angler
     * @return array{
     *     angler: Angler|null,
     *     records: Collection<int, Record>,
     *     anglerRecords: Collection<int, Record>,
     *     longest: Record|null,
     *     fattest: Record|null,
     *     crews: int,
     *     personalBest: array{byLength: Record|null, byWeight: Record|null, lakeWithMostCatches: Lake|null}|array<empty, empty>,
     *     record_count: int,
     *     lake_count: int,
     *     totalInches: float,
     *     totalFeet: float,
     *     avgLength: float,
     *     avgWeight: float,
     *     releasedCount: int,
     *     releaseRate: int,
     *     mvpLure: Record|null,
     *     peakMonthName: string|null,
     *     topWaters: Collection<int, Record>,
     *     speciesDistribution: Collection<int, Record>,
     *     speciesPbs: Collection<int, Record>,
     *     topTrophies: Collection<int, Record>
     * }
     */
    public function getAnglerTelemetry(?Angler $angler): array
    {
        if (!$angler || !$angler->id) {
            return $this->getEmptyTelemetry();
        }

        $angler->loadMissing('earnedBadges.badge');

        // 1. Single-pass career aggregate telemetry
        $stats = Record::where('anglers_id', $angler->id)
            ->selectRaw('
                COUNT(*) as record_count,
                COUNT(DISTINCT lakes_id) as lake_count,
                COALESCE(SUM(length), 0) as total_inches,
                COALESCE(AVG(length), 0) as avg_length,
                COALESCE(AVG(weight), 0) as avg_weight,
                COALESCE(SUM(CASE WHEN released = 1 THEN 1 ELSE 0 END), 0) as released_count
            ')
            ->first();

        $recordCount = (int) ($stats->record_count ?? 0);
        $lakeCount = (int) ($stats->lake_count ?? 0);
        $totalInches = round((float) ($stats->total_inches ?? 0), 1);
        $totalFeet = round($totalInches / 12, 1);
        $avgLength = round((float) ($stats->avg_length ?? 0), 1);
        $avgWeight = round((float) ($stats->avg_weight ?? 0), 1);
        $releasedCount = (int) ($stats->released_count ?? 0);
        $releaseRate = $recordCount > 0 ? (int) round(($releasedCount / $recordCount) * 100) : 0;

        $crews = Crew::where('anglers_id', $angler->id)->count();

        // 2. Career Accolades (Longest & Heaviest)
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

        $personalBest = [
            'byLength' => $longest,
            'byWeight' => $fattest,
            'lakeWithMostCatches' => $angler->lakeWithMostCatches(),
        ];

        // 3. MVP Lure, Peak Month & Top Waters
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

        $peakMonthName = ($peakMonth && $peakMonth->month_num)
            ? (\DateTime::createFromFormat('!m', (string) $peakMonth->month_num) ?: null)?->format('F')
            : null;

        /** @var Collection<int, Record> $topWaters */
        $topWaters = Record::select('lakes_id', DB::raw('count(*) as catches'), DB::raw('max(length) as longest'))
            ->where('anglers_id', $angler->id)
            ->whereNotNull('lakes_id')
            ->groupBy('lakes_id')
            ->orderBy('catches', 'desc')
            ->take(3)
            ->with('lake')
            ->get();

        // 4. Species Breakdown Distribution
        /** @var Collection<int, Record> $speciesDistribution */
        $speciesDistribution = Record::select(
                'fish_breeds_id',
                DB::raw('count(*) as count'),
                DB::raw('round(avg(length), 1) as avg_len'),
                DB::raw('round(avg(weight), 1) as avg_wt')
            )
            ->where('anglers_id', $angler->id)
            ->whereNotNull('fish_breeds_id')
            ->groupBy('fish_breeds_id')
            ->orderBy('count', 'desc')
            ->with('fishBreed')
            ->get();

        // 5. Single-Query Species Personal Bests (Eliminates N+1 per-species lookup loop)
        $maxLengthSubquery = Record::where('anglers_id', $angler->id)
            ->whereNotNull('length')
            ->whereNotNull('fish_breeds_id')
            ->select('fish_breeds_id', DB::raw('MAX(length) as max_length'))
            ->groupBy('fish_breeds_id');

        /** @var Collection<int, Record> $speciesPbs */
        $speciesPbs = Record::query()
            ->select('records.*')
            ->joinSub($maxLengthSubquery, 'pb_max', function ($join) {
                $join->on('records.fish_breeds_id', '=', 'pb_max.fish_breeds_id')
                     ->on('records.length', '=', 'pb_max.max_length');
            })
            ->where('records.anglers_id', $angler->id)
            ->with(['lake', 'fishBreed'])
            ->orderBy('records.caught', 'desc')
            ->get()
            ->unique('fish_breeds_id')
            ->sortByDesc('length')
            ->values();

        // 6. Top 5 Species-Normalized Trophies
        $topTrophies = $this->trophyScoringService->getTopNormalizedCatches(
            Record::where('anglers_id', $angler->id),
            5
        );

        return [
            'angler' => $angler,
            'records' => collect(),
            'anglerRecords' => collect(),
            'longest' => $longest,
            'fattest' => $fattest,
            'crews' => $crews,
            'personalBest' => $personalBest,
            'record_count' => $recordCount,
            'lake_count' => $lakeCount,
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
            'speciesPbs' => $speciesPbs,
            'topTrophies' => $topTrophies,
        ];
    }

    /**
     * Return zeroed telemetry structure when no angler is associated.
     *
     * @return array{
     *     angler: null,
     *     records: Collection<int, Record>,
     *     anglerRecords: Collection<int, Record>,
     *     longest: null,
     *     fattest: null,
     *     crews: int,
     *     personalBest: array<empty, empty>,
     *     record_count: int,
     *     lake_count: int,
     *     totalInches: float,
     *     totalFeet: float,
     *     avgLength: float,
     *     avgWeight: float,
     *     releasedCount: int,
     *     releaseRate: int,
     *     mvpLure: null,
     *     peakMonthName: null,
     *     topWaters: Collection<int, Record>,
     *     speciesDistribution: Collection<int, Record>,
     *     speciesPbs: Collection<int, Record>,
     *     topTrophies: Collection<int, Record>
     * }
     */
    public function getEmptyTelemetry(): array
    {
        return [
            'angler' => null,
            'records' => collect(),
            'anglerRecords' => collect(),
            'longest' => null,
            'fattest' => null,
            'crews' => 0,
            'personalBest' => [],
            'record_count' => 0,
            'lake_count' => 0,
            'totalInches' => 0.0,
            'totalFeet' => 0.0,
            'avgLength' => 0.0,
            'avgWeight' => 0.0,
            'releasedCount' => 0,
            'releaseRate' => 0,
            'mvpLure' => null,
            'peakMonthName' => null,
            'topWaters' => collect(),
            'speciesDistribution' => collect(),
            'speciesPbs' => collect(),
            'topTrophies' => collect(),
        ];
    }
}

