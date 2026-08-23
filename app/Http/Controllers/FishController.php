<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $selectedFamilyId = $request->query('family');
        $search = $request->query('search');

        $families = FishFamily::withCount('breeds')
            ->orderBy('name', 'asc')
            ->get();

        $query = FishBreed::with(['family'])
            ->withCount('records')
            ->withMax('records as longest_record', 'length')
            ->withMax('records as heaviest_record', 'weight');

        if (!empty($selectedFamilyId)) {
            $query->where('fish_families_id', $selectedFamilyId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('family', function ($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalBreedsCount = FishBreed::count();
        $totalFamiliesCount = FishFamily::count();
        $totalCatchesCount = Record::count();
        $topSpecies = FishBreed::withCount('records')->orderBy('records_count', 'desc')->first();

        $fishes = $query->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('fish.index', [
            'fishes' => $fishes,
            'families' => $families,
            'selectedFamilyId' => $selectedFamilyId,
            'search' => $search,
            'totalBreedsCount' => $totalBreedsCount,
            'totalFamiliesCount' => $totalFamiliesCount,
            'totalCatchesCount' => $totalCatchesCount,
            'topSpecies' => $topSpecies,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string|int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fish = FishBreed::with(['family'])->findOrFail($id);

        $longest = Record::where('fish_breeds_id', $fish->id)->max('length');
        $fattest = Record::where('fish_breeds_id', $fish->id)->max('weight');
        $count = Record::where('fish_breeds_id', $fish->id)->count();

        // Trophy record holders
        $recordTrophy = Record::with(['angler', 'lake'])
            ->where('fish_breeds_id', $fish->id)
            ->whereNotNull('length')
            ->orderBy('length', 'desc')
            ->first();

        $heaviestTrophy = Record::with(['angler', 'lake'])
            ->where('fish_breeds_id', $fish->id)
            ->whereNotNull('weight')
            ->orderBy('weight', 'desc')
            ->first();

        // Top productive lures
        $topLures = Record::where('fish_breeds_id', $fish->id)
            ->whereNotNull('lures_id')
            ->select('lures_id', DB::raw('count(*) as catches_count'))
            ->groupBy('lures_id')
            ->with('lure')
            ->orderBy('catches_count', 'desc')
            ->limit(5)
            ->get();

        // Top anglers leaderboard
        $topAnglers = Record::where('fish_breeds_id', $fish->id)
            ->whereNotNull('anglers_id')
            ->select('anglers_id', DB::raw('count(*) as catches_count'), DB::raw('max(length) as longest_catch'))
            ->groupBy('anglers_id')
            ->with('angler')
            ->orderBy('catches_count', 'desc')
            ->limit(5)
            ->get();

        // Monthly catch distribution (May - Oct)
        $monthlyCatchesRaw = Record::where('fish_breeds_id', $fish->id)
            ->whereNotNull('caught')
            ->select(DB::raw('MONTH(caught) as month_num'), DB::raw('count(*) as count'))
            ->groupBy('month_num')
            ->pluck('count', 'month_num')
            ->toArray();

        $monthNames = [
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
        ];

        $monthlyStats = [];
        $maxMonthCount = max(array_values($monthlyCatchesRaw) ?: [1]);

        foreach ($monthNames as $mNum => $mLabel) {
            $mCount = $monthlyCatchesRaw[$mNum] ?? 0;
            $pct = $maxMonthCount > 0 ? round(($mCount / $maxMonthCount) * 100) : 0;
            $monthlyStats[] = [
                'month' => $mLabel,
                'count' => $mCount,
                'percentage' => $pct,
            ];
        }

        // Lakes distribution
        $lakes = $fish->records()
            ->select(
                'lakes_id',
                DB::raw('count(*) as count'),
                DB::raw('count(distinct caught) as visits'),
                DB::raw('min(length) as min_length'),
                DB::raw('max(length) as max_length'),
                DB::raw('round(avg(length), 2) as avg_length')
            )
            ->groupBy('lakes_id')
            ->with('lake')
            ->orderBy('count', 'desc')
            ->get();

        // Recent catches feed
        $recentCatches = Record::with(['angler', 'lake', 'lure'])
            ->where('fish_breeds_id', $fish->id)
            ->orderBy('caught', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Water temperature & weather trigger telemetry
        $weatherTelemetry = Record::where('fish_breeds_id', $fish->id)
            ->whereNotNull('temperature')
            ->select(
                DB::raw('round(avg(temperature), 1) as avg_temp'),
                DB::raw('round(min(temperature), 1) as min_temp'),
                DB::raw('round(max(temperature), 1) as max_temp'),
                DB::raw('count(*) as temp_count')
            )
            ->first();

        // 9 Weather Condition Categories Telemetry
        $weatherConditionCounts = DB::table('records')
            ->join('lake_daily_weather', function ($join) {
                $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                     ->on(DB::raw('DATE(records.caught)'), '=', 'lake_daily_weather.date');
            })
            ->where('records.fish_breeds_id', $fish->id)
            ->whereNull('records.deleted_at')
            ->select('lake_daily_weather.weather_condition', DB::raw('count(*) as count'))
            ->groupBy('lake_daily_weather.weather_condition')
            ->pluck('count', 'weather_condition');

        $weatherCategories = [
            ['key' => 'Clear', 'label' => 'CLEAR', 'icon' => 'sun', 'pattern' => 'Clear sky'],
            ['key' => 'Mainly Clear', 'label' => 'MAINLY', 'icon' => 'sun-medium', 'pattern' => 'Mainly clear'],
            ['key' => 'Partly Cloudy', 'label' => 'PARTLY', 'icon' => 'cloud-sun', 'pattern' => 'Partly cloudy'],
            ['key' => 'Overcast', 'label' => 'OVERCAST', 'icon' => 'cloud', 'pattern' => 'Overcast'],
            ['key' => 'Fog', 'label' => 'FOG', 'icon' => 'cloud-fog', 'pattern' => 'Fog'],
            ['key' => 'Drizzle', 'label' => 'DRIZZLE', 'icon' => 'cloud-drizzle', 'pattern' => 'drizzle'],
            ['key' => 'Rain', 'label' => 'RAIN', 'icon' => 'cloud-rain', 'pattern' => 'rain'],
            ['key' => 'Showers', 'label' => 'SHOWERS', 'icon' => 'cloud-rain-wind', 'pattern' => 'showers'],
            ['key' => 'Storm', 'label' => 'STORM', 'icon' => 'cloud-lightning', 'pattern' => 'Thunderstorm'],
        ];

        $maxWeatherCatches = 1;
        $weatherStats = [];

        foreach ($weatherCategories as $cat) {
            $c = 0;
            foreach ($weatherConditionCounts as $condName => $cnt) {
                if (stripos($condName, $cat['pattern']) !== false) {
                    $c += (int) $cnt;
                }
            }
            if ($c > $maxWeatherCatches) {
                $maxWeatherCatches = $c;
            }
            $weatherStats[] = array_merge($cat, ['count' => $c]);
        }

        foreach ($weatherStats as &$ws) {
            $ws['percentage'] = $maxWeatherCatches > 0 ? round(($ws['count'] / $maxWeatherCatches) * 100) : 0;
        }

        return view('fish.show', [
            'fish' => $fish,
            'longest' => $longest,
            'fattest' => $fattest,
            'count' => $count,
            'recordTrophy' => $recordTrophy,
            'heaviestTrophy' => $heaviestTrophy,
            'topLures' => $topLures,
            'topAnglers' => $topAnglers,
            'monthlyStats' => $monthlyStats,
            'lakes' => $lakes,
            'recentCatches' => $recentCatches,
            'weatherTelemetry' => $weatherTelemetry,
            'weatherStats' => $weatherStats,
        ]);

    }
}
