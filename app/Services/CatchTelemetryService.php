<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Record;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CatchTelemetryService
{
    public const CACHE_KEY = 'catch_telemetry_summary';
    public const CACHE_TTL = 3600; // 1 hour (invalidated on model events)

    /**
     * Get or calculate cached comprehensive catch telemetry.
     *
     * @param Builder|null $baseQuery
     * @param bool $forceRefresh
     * @return array<string, mixed>
     */
    public function getOrCalculateTelemetry(?Builder $baseQuery = null, bool $forceRefresh = false): array
    {
        // If a customized/filtered query is passed, compute directly without modifying global cache
        if ($baseQuery !== null && $this->isFilteredQuery($baseQuery)) {
            return $this->calculateTelemetry($baseQuery);
        }

        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () use ($baseQuery) {
            return $this->calculateTelemetry($baseQuery ?? Record::query());
        });
    }

    /**
     * Clear cached catch telemetry summary.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Check if the builder has non-default where/order clauses.
     */
    protected function isFilteredQuery(Builder $query): bool
    {
        $wheres = $query->getQuery()->wheres;
        // If there are where clauses other than standard soft-delete check
        foreach ($wheres as $where) {
            if (isset($where['column']) && $where['column'] !== 'records.deleted_at' && $where['column'] !== 'deleted_at') {
                return true;
            }
        }
        return false;
    }

    /**
     * Compute comprehensive catch telemetry, leaderboard records, top performers, species shifts, and weather aggregates.
     *
     * @param Builder $baseQuery
     * @return array<string, mixed>
     */
    public function calculateTelemetry(Builder $baseQuery): array
    {
        // 1. High-level telemetry stats + latest catch year in a single query
        $stats = (clone $baseQuery)
            ->reorder()
            ->selectRaw('
                COUNT(*) as total_catches,
                COALESCE(SUM(length), 0) as total_inches,
                COALESCE(AVG(length), 0) as avg_length,
                COALESCE(SUM(CASE WHEN released = 1 THEN 1 ELSE 0 END), 0) as released_count,
                COALESCE(AVG(temperature), 0) as avg_water_temp,
                MAX(YEAR(caught)) as latest_year
            ')
            ->first();

        $totalCatches = (int) ($stats->total_catches ?? 0);
        $totalInches = round((float) ($stats->total_inches ?? 0), 1);
        $totalFeet = round($totalInches / 12, 1);
        $avgLength = round((float) ($stats->avg_length ?? 0), 1);
        $releasedCount = (int) ($stats->released_count ?? 0);
        $releaseRate = $totalCatches > 0 ? (int) round(($releasedCount / $totalCatches) * 100) : 0;
        $avgWaterTemp = round((float) ($stats->avg_water_temp ?? 0), 1);

        $latestYear = (int) (($stats && $stats->getAttribute('latest_year')) ? $stats->getAttribute('latest_year') : date('Y'));
        $prevYear = $latestYear - 1;

        // 2. Standout catches (Longest and Heaviest) with strict relationship scoping
        $longestCatch = (clone $baseQuery)->whereNotNull('length')
            ->reorder()
            ->orderBy('length', 'desc')
            ->with(['angler', 'lake', 'fishBreed'])
            ->first();

        $heaviestCatch = (clone $baseQuery)->whereNotNull('weight')
            ->reorder()
            ->orderBy('weight', 'desc')
            ->with(['angler', 'lake', 'fishBreed'])
            ->first();

        // 3. Top 5 Anglers
        $topAnglers = (clone $baseQuery)->select('anglers_id', DB::raw('count(*) as catches_count'), DB::raw('max(length) as max_length'))
            ->reorder()
            ->whereNotNull('anglers_id')
            ->groupBy('anglers_id')
            ->orderBy('catches_count', 'desc')
            ->take(5)
            ->with('angler')
            ->get();

        // 4. Top 5 Lakes
        $topLakes = (clone $baseQuery)->select('lakes_id', DB::raw('count(*) as catches_count'), DB::raw('max(length) as max_length'))
            ->reorder()
            ->whereNotNull('lakes_id')
            ->groupBy('lakes_id')
            ->orderBy('catches_count', 'desc')
            ->take(5)
            ->with('lake')
            ->get();

        // 5. Macro Target Species Shifts & Trends (Consolidated Single SQL Query with Conditional Aggregation)
        $topSpeciesRecords = (clone $baseQuery)
            ->select(
                'fish_breeds_id',
                DB::raw('count(*) as total_count'),
                DB::raw("COALESCE(SUM(CASE WHEN YEAR(caught) = {$latestYear} THEN 1 ELSE 0 END), 0) as curr_count"),
                DB::raw("COALESCE(SUM(CASE WHEN YEAR(caught) = {$prevYear} THEN 1 ELSE 0 END), 0) as prev_count")
            )
            ->reorder()
            ->whereNotNull('fish_breeds_id')
            ->groupBy('fish_breeds_id')
            ->orderBy('total_count', 'desc')
            ->take(5)
            ->with('fishBreed')
            ->get();

        $speciesTrends = $topSpeciesRecords->map(function ($item) use ($totalCatches) {
            /** @var Record $item */
            $currCount = (int) ($item->getAttribute('curr_count') ?? 0);
            $prevCount = (int) ($item->getAttribute('prev_count') ?? 0);
            $totalCount = (int) ($item->getAttribute('total_count') ?? 0);
            $percentage = $totalCatches > 0 ? round(($totalCount / $totalCatches) * 100, 1) : 0.0;
            $shift = $prevCount > 0 ? (int) round((($currCount - $prevCount) / $prevCount) * 100) : ($currCount > 0 ? 100 : 0);

            return (object) [
                'fishBreed' => $item->fishBreed,
                'total_count' => $totalCount,
                'percentage' => $percentage,
                'curr_count' => $currCount,
                'prev_count' => $prevCount,
                'shift' => $shift,
            ];
        });

        // 6. Atmospheric & Weather Telemetry Calculations (Consolidated Single SQL Pass)
        $weatherJoinedRecords = DB::table('records')
            ->join('lake_daily_weather', function ($join) {
                $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                     ->on('records.caught', '=', 'lake_daily_weather.date');
            })
            ->whereNull('records.deleted_at');

        $weatherSummary = (clone $weatherJoinedRecords)
            ->selectRaw('
                COUNT(*) as coverage_count,
                COALESCE(AVG(lake_daily_weather.air_temp_mean), 0) as avg_air_temp,
                COALESCE(AVG(lake_daily_weather.barometric_pressure), 0) as avg_pressure,
                COALESCE(AVG(lake_daily_weather.wind_speed_max), 0) as avg_wind
            ')
            ->first();

        $weatherCoverageCount = (int) ($weatherSummary->coverage_count ?? 0);
        $weatherCoverageRate = $totalCatches > 0 ? (int) round(($weatherCoverageCount / $totalCatches) * 100) : 0;
        $avgAirTemp = round((float) ($weatherSummary->avg_air_temp ?? 0), 1);
        $avgBarometricPressure = round((float) ($weatherSummary->avg_pressure ?? 0), 1);
        $avgWindSpeed = round((float) ($weatherSummary->avg_wind ?? 0), 1);

        // Query Best Lake per Weather Condition
        $bestLakePerCondition = DB::table('records')
            ->join('lake_daily_weather', function ($join) {
                $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                     ->on('records.caught', '=', 'lake_daily_weather.date');
            })
            ->join('lakes', 'records.lakes_id', '=', 'lakes.id')
            ->whereNull('records.deleted_at')
            ->whereNull('lakes.deleted_at')
            ->select(
                'lake_daily_weather.weather_condition',
                'lakes.id as lake_id',
                'lakes.name as lake_name',
                DB::raw('count(records.id) as lake_catches_count')
            )
            ->groupBy('lake_daily_weather.weather_condition', 'lakes.id', 'lakes.name')
            ->orderBy('lake_catches_count', 'desc')
            ->get()
            ->groupBy('weather_condition')
            ->map(function ($group) {
                return $group->first();
            });

        // Catch Breakdown by Weather Condition with Best Lake
        $weatherDistribution = (clone $weatherJoinedRecords)
            ->select(
                'lake_daily_weather.weather_condition',
                DB::raw('count(records.id) as catches_count'),
                DB::raw('avg(records.length) as avg_length'),
                DB::raw('avg(lake_daily_weather.air_temp_mean) as avg_air_temp'),
                DB::raw('avg(lake_daily_weather.barometric_pressure) as avg_pressure')
            )
            ->groupBy('lake_daily_weather.weather_condition')
            ->orderBy('catches_count', 'desc')
            ->get()
            ->map(function ($item) use ($totalCatches, $bestLakePerCondition) {
                $item->percentage = $totalCatches > 0 ? round(($item->catches_count / $totalCatches) * 100, 1) : 0.0;
                $item->avg_length = round((float) ($item->avg_length ?? 0), 1);
                $item->avg_air_temp = round((float) ($item->avg_air_temp ?? 0), 1);
                $item->avg_pressure = round((float) ($item->avg_pressure ?? 0), 1);

                $bestLake = $bestLakePerCondition->get($item->weather_condition);
                $item->best_lake_id = $bestLake ? $bestLake->lake_id : null;
                $item->best_lake_name = $bestLake ? $bestLake->lake_name : null;
                $item->best_lake_catches = $bestLake ? $bestLake->lake_catches_count : 0;

                return $item;
            });

        // Best Lake by Weather Condition Matrix
        $lakeWeatherMatrix = (clone $weatherJoinedRecords)
            ->join('lakes', 'records.lakes_id', '=', 'lakes.id')
            ->join('fish_breeds', 'records.fish_breeds_id', '=', 'fish_breeds.id')
            ->select(
                'lakes.id as lake_id',
                'lakes.name as lake_name',
                'lake_daily_weather.weather_condition',
                'fish_breeds.name as top_species',
                DB::raw('count(records.id) as catches_count'),
                DB::raw('avg(records.length) as avg_length')
            )
            ->whereNull('lakes.deleted_at')
            ->groupBy('lakes.id', 'lakes.name', 'lake_daily_weather.weather_condition', 'fish_breeds.name')
            ->orderBy('catches_count', 'desc')
            ->get()
            ->groupBy('lake_id')
            ->map(function ($group) {
                $bestCondition = $group->first();
                return (object) [
                    'lake_id' => $bestCondition->lake_id,
                    'lake_name' => $bestCondition->lake_name,
                    'weather_condition' => $bestCondition->weather_condition,
                    'top_species' => $bestCondition->top_species,
                    'catches_count' => $bestCondition->catches_count,
                    'avg_length' => round((float) ($bestCondition->avg_length ?? 0), 1),
                ];
            })
            ->take(5)
            ->values();

        return [
            'totalCatches' => $totalCatches,
            'totalInches' => $totalInches,
            'totalFeet' => $totalFeet,
            'avgLength' => $avgLength,
            'releasedCount' => $releasedCount,
            'releaseRate' => $releaseRate,
            'avgWaterTemp' => $avgWaterTemp,
            'longestCatch' => $longestCatch,
            'heaviestCatch' => $heaviestCatch,
            'topAnglers' => $topAnglers,
            'topLakes' => $topLakes,
            'speciesTrends' => $speciesTrends,
            'weatherCoverageCount' => $weatherCoverageCount,
            'weatherCoverageRate' => $weatherCoverageRate,
            'avgAirTemp' => $avgAirTemp,
            'avgBarometricPressure' => $avgBarometricPressure,
            'avgWindSpeed' => $avgWindSpeed,
            'weatherDistribution' => $weatherDistribution,
            'lakeWeatherMatrix' => $lakeWeatherMatrix,
            'latestYear' => $latestYear,
            'prevYear' => $prevYear,
        ];
    }
}
