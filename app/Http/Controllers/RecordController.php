<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreRecordRequest;
use Fishinglog\Http\Requests\UpdateRecordRequest;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Photo;
use Fishinglog\Models\Record;
use Fishinglog\Pipes\Filters\FilterByAngler;
use Fishinglog\Pipes\Filters\FilterByLength;
use Fishinglog\Pipes\Filters\FilterByName;
use Fishinglog\Pipes\Filters\FilterByRecordsCount;
use Fishinglog\Pipes\Filters\FilterBySearch;
use Fishinglog\Pipes\Filters\SortBy;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecordController extends Controller
{
    use Notifiable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Pipeline $pipeline, Request $request)
    {
        if ($request->hasAny(['search', 'length', 'length_operator', 'angler', 'sort_by', 'sort_order'])) {
            return redirect()->route('record.directory', $request->query());
        }

        $recordsQuery = Record::with(['angler', 'lake.dailyWeather', 'fishBreed', 'lure'])
            ->orderBy('caught', 'desc')
            ->orderBy('lakes_id', 'asc')
            ->orderBy('anglers_id', 'asc');

        $filteredRecords = $pipeline->send($recordsQuery)
            ->through([
                SortBy::class,
                FilterBySearch::class,
                FilterByLength::class,
                FilterByAngler::class,
            ])
            ->thenReturn();

        $records = (clone $filteredRecords)->paginate(10)->withQueryString();

        // High-level telemetry stats based on active filter query
        $totalCatches = (clone $filteredRecords)->count();
        $totalInches = round((clone $filteredRecords)->sum('length'), 1);
        $totalFeet = round($totalInches / 12, 1);
        $avgLength = round((clone $filteredRecords)->whereNotNull('length')->avg('length') ?? 0, 1);

        $releasedCount = (clone $filteredRecords)->where('released', 1)->count();
        $releaseRate = $totalCatches > 0 ? round(($releasedCount / $totalCatches) * 100) : 0;

        $longestCatch = (clone $filteredRecords)->whereNotNull('length')
            ->reorder()
            ->orderBy('length', 'desc')
            ->with(['angler', 'lake', 'fishBreed'])
            ->first();

        $heaviestCatch = (clone $filteredRecords)->whereNotNull('weight')
            ->reorder()
            ->orderBy('weight', 'desc')
            ->with(['angler', 'lake', 'fishBreed'])
            ->first();

        $avgWaterTemp = round((clone $filteredRecords)->whereNotNull('temperature')->avg('temperature') ?? 0, 1);

        // Top 5 Anglers by production & longest fish
        $topAnglers = (clone $filteredRecords)->select('anglers_id', DB::raw('count(*) as catches_count'), DB::raw('max(length) as max_length'))
            ->reorder()
            ->whereNotNull('anglers_id')
            ->groupBy('anglers_id')
            ->orderBy('catches_count', 'desc')
            ->take(5)
            ->with('angler')
            ->get();

        // Top 5 Lakes by production & longest fish
        $topLakes = (clone $filteredRecords)->select('lakes_id', DB::raw('count(*) as catches_count'), DB::raw('max(length) as max_length'))
            ->reorder()
            ->whereNotNull('lakes_id')
            ->groupBy('lakes_id')
            ->orderBy('catches_count', 'desc')
            ->take(5)
            ->with('lake')
            ->get();

        // Macro Target Species Shifts & Trends
        $latestYear = (clone $filteredRecords)->whereNotNull('caught')->max(DB::raw('year(caught)')) ?: date('Y');
        $prevYear = $latestYear - 1;

        $currentYearBreeds = (clone $filteredRecords)->select('fish_breeds_id', DB::raw('count(*) as count'))
            ->reorder()
            ->whereNotNull('fish_breeds_id')
            ->whereRaw('year(caught) = ?', [$latestYear])
            ->groupBy('fish_breeds_id')
            ->pluck('count', 'fish_breeds_id');

        $prevYearBreeds = (clone $filteredRecords)->select('fish_breeds_id', DB::raw('count(*) as count'))
            ->reorder()
            ->whereNotNull('fish_breeds_id')
            ->whereRaw('year(caught) = ?', [$prevYear])
            ->groupBy('fish_breeds_id')
            ->pluck('count', 'fish_breeds_id');

        $allBreedsWithCatches = (clone $filteredRecords)->select('fish_breeds_id', DB::raw('count(*) as total_count'))
            ->reorder()
            ->whereNotNull('fish_breeds_id')
            ->groupBy('fish_breeds_id')
            ->orderBy('total_count', 'desc')
            ->take(5)
            ->with('fishBreed')
            ->get();

        $speciesTrends = $allBreedsWithCatches->map(function ($item) use ($currentYearBreeds, $prevYearBreeds, $totalCatches) {
            $breedId = $item->fish_breeds_id;
            $currCount = $currentYearBreeds[$breedId] ?? 0;
            $prevCount = $prevYearBreeds[$breedId] ?? 0;
            $percentage = $totalCatches > 0 ? round(($item->total_count / $totalCatches) * 100, 1) : 0;
            $shift = $prevCount > 0 ? round((($currCount - $prevCount) / $prevCount) * 100) : ($currCount > 0 ? 100 : 0);

            return (object) [
                'fishBreed' => $item->fishBreed,
                'total_count' => $item->total_count,
                'percentage' => $percentage,
                'curr_count' => $currCount,
                'prev_count' => $prevCount,
                'shift' => $shift,
            ];
        });

        // Atmospheric & Weather Telemetry Calculations
        $weatherJoinedRecords = DB::table('records')
            ->join('lake_daily_weather', function ($join) {
                $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                     ->on(DB::raw('DATE(records.caught)'), '=', 'lake_daily_weather.date');
            })
            ->whereNull('records.deleted_at');

        $weatherCoverageCount = (clone $weatherJoinedRecords)->count();
        $weatherCoverageRate = $totalCatches > 0 ? round(($weatherCoverageCount / $totalCatches) * 100) : 0;

        $avgAirTemp = round((clone $weatherJoinedRecords)->avg('lake_daily_weather.air_temp_mean') ?? 0, 1);
        $avgBarometricPressure = round((clone $weatherJoinedRecords)->avg('lake_daily_weather.barometric_pressure') ?? 0, 1);
        $avgWindSpeed = round((clone $weatherJoinedRecords)->avg('lake_daily_weather.wind_speed_max') ?? 0, 1);

        // Query Best Lake per Weather Condition
        $bestLakePerCondition = DB::table('records')
            ->join('lake_daily_weather', function ($join) {
                $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                     ->on(DB::raw('DATE(records.caught)'), '=', 'lake_daily_weather.date');
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
                $item->percentage = $totalCatches > 0 ? round(($item->catches_count / $totalCatches) * 100, 1) : 0;
                $item->avg_length = round($item->avg_length ?? 0, 1);
                $item->avg_air_temp = round($item->avg_air_temp ?? 0, 1);
                $item->avg_pressure = round($item->avg_pressure ?? 0, 1);

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
                    'avg_length' => round($bestCondition->avg_length ?? 0, 1),
                ];
            })
            ->take(5)
            ->values();

        return view('record.index', [
            'records' => $records,
            'totalCatches' => $totalCatches,
            'totalFeet' => $totalFeet,
            'totalInches' => $totalInches,
            'avgLength' => $avgLength,
            'releasedCount' => $releasedCount,
            'releaseRate' => $releaseRate,
            'longestCatch' => $longestCatch,
            'heaviestCatch' => $heaviestCatch,
            'avgWaterTemp' => $avgWaterTemp,
            'avgAirTemp' => $avgAirTemp,
            'avgBarometricPressure' => $avgBarometricPressure,
            'avgWindSpeed' => $avgWindSpeed,
            'weatherCoverageRate' => $weatherCoverageRate,
            'weatherDistribution' => $weatherDistribution,
            'lakeWeatherMatrix' => $lakeWeatherMatrix,
            'topAnglers' => $topAnglers,
            'topLakes' => $topLakes,
            'speciesTrends' => $speciesTrends,
            'latestYear' => $latestYear,
            'prevYear' => $prevYear,
        ]);
    }

    /**
     * Display the Catches Logbook Directory table with search and filters.
     *
     * @param Pipeline $pipeline
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function directory(Pipeline $pipeline, Request $request)
    {
        $recordsQuery = Record::with(['angler', 'lake.dailyWeather', 'fishBreed', 'lure'])
            ->orderBy('caught', 'desc')
            ->orderBy('lakes_id', 'asc')
            ->orderBy('anglers_id', 'asc');

        $filteredRecords = $pipeline->send($recordsQuery)
            ->through([
                SortBy::class,
                FilterBySearch::class,
                FilterByLength::class,
                FilterByAngler::class,
            ])
            ->thenReturn();

        $records = (clone $filteredRecords)->paginate(15)->withQueryString();
        $totalCount = (clone $filteredRecords)->count();

        return view('record.directory', [
            'records' => $records,
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * Show touch-optimized quick catch form for boat logging.
     *
     * @return \Illuminate\Http\Response
     */
    public function quick()
    {
        return view('record.quick', [
            'anglers' => Angler::orderBy('lastName', 'asc')->get(),
            'lakes' => Lake::orderBy('name', 'asc')->get(),
            'fishBreeds' => FishBreed::orderBy('name', 'asc')->get(),
            'lures' => Lure::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Show offline catch sync review page.
     *
     * @return \Illuminate\Http\Response
     */
    public function offlineReview()
    {
        $recentCatches = Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        return view('record.offline-review', [
            'recentCatches' => $recentCatches,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $record = Record::find($request->record);

        if ($record == null) {
            $record = new Record;
        }

        return view('record.create', [
            'record' => $record,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreRecordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRecordRequest $request)
    {
        $record = new Record;
        $record->client_id = $request->client_id;
        $record->anglers_id = $request->anglers_id;
        $record->lakes_id = $request->lakes_id;
        $record->fish_breeds_id = $request->fish_breeds_id;
        $record->lures_id = $request->lures_id;
        $record->weight = $request->weight;
        $record->length = $request->length;
        $record->temperature = $request->temperature;
        $record->latitude = $request->latitude;
        $record->longitude = $request->longitude;
        $record->released = $request->released;
        $record->caught = $request->caught;

        $record->save();

        // Handle optional uploaded photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $file) {
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = Str::uuid() . '.' . $extension;
                $path = $file->storeAs('photos/records', $filename, 'public');

                Photo::create([
                    'photoable_type' => Record::class,
                    'photoable_id' => $record->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'is_cover' => ($index === 0),
                    'user_id' => auth()->id(),
                    'sync_status' => 'pending_upstream',
                ]);
            }
        }

        return redirect()->action(
            [self::class, 'create'],
            ['record' => $record->id]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Record $record)
    {
        $record->load(['angler', 'lake.dailyWeather', 'fishBreed', 'lure', 'photos']);

        return view('record.show', [
            'record' => $record,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function edit(Record $record)
    {
        $record->load('photos');

        return view('record.edit', [
            'record' => $record,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\UpdateRecordRequest  $request
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRecordRequest $request, Record $record)
    {
        $targetRecord = Record::find($request->id) ?? $record;

        $targetRecord->anglers_id = $request->anglers_id;
        $targetRecord->lakes_id = $request->lakes_id;
        $targetRecord->fish_breeds_id = $request->fish_breeds_id;
        $targetRecord->lures_id = $request->lures_id;
        $targetRecord->weight = $request->weight;
        $targetRecord->length = $request->length;
        $targetRecord->temperature = $request->temperature;
        $targetRecord->latitude = $request->latitude;
        $targetRecord->longitude = $request->longitude;
        $targetRecord->released = $request->released;
        $targetRecord->caught = $request->caught;

        $targetRecord->save();

        // Handle optional uploaded photos
        if ($request->hasFile('photos')) {
            $hasExistingPhotos = $targetRecord->photos()->count() > 0;
            foreach ($request->file('photos') as $index => $file) {
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = Str::uuid() . '.' . $extension;
                $path = $file->storeAs('photos/records', $filename, 'public');

                Photo::create([
                    'photoable_type' => Record::class,
                    'photoable_id' => $targetRecord->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'is_cover' => (!$hasExistingPhotos && $index === 0),
                    'user_id' => auth()->id(),
                    'sync_status' => 'pending_upstream',
                ]);
            }
        }

        return redirect('/record/' . $targetRecord->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function destroy(Record $record)
    {
        $record->delete();

        return redirect('/record')->with('status', 'Catch record removed successfully.');
    }
}
