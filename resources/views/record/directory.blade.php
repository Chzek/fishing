@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- 1. Header Hero Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ url('/record') }}" class="w-10 h-10 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 flex items-center justify-center transition-colors shrink-0" title="Return to Catches Telemetry Dashboard">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2.5">
                        <span>Catches Logbook Directory</span>
                        <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">{{ number_format($totalCount) }} Total Records</span>
                    </h1>
                    <p class="text-xs text-slate-400 font-medium pt-0.5">Search, filter, and inspect individual catch records, weather telemetry, and lure logs</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ url('/record') }}" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                    <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-teal-400"></i>
                    <span>Telemetry Dashboard</span>
                </a>
                <a href="{{ url('/record/create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-950/50 transition-all flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Log New Catch</span>
                </a>
                <a href="{{ url('/record/quick') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-950/50 transition-all flex items-center gap-1.5">
                    <i data-lucide="zap" class="w-4 h-4 text-emerald-200"></i>
                    <span>Boat Quick Catch</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Reactive Livewire Catches Directory Table with Pluggable Filters -->
    @livewire('components.generic-data-table', [
        'modelClass' => \Fishinglog\Models\Record::class,
        'with' => ['angler', 'lake', 'fishBreed', 'lure'],
        'columns' => [
            ['key' => 'caught', 'label' => 'Date', 'type' => 'date', 'sortable' => true],
            ['key' => 'angler.lastName', 'label' => 'Angler', 'type' => 'angler_name', 'sortable' => true],
            ['key' => 'lake.name', 'label' => 'Lake', 'type' => 'lake_link', 'sortable' => true],
            ['key' => 'fishBreed.name', 'label' => 'Species', 'type' => 'species_name', 'sortable' => true],
            ['key' => 'dailyWeather', 'label' => 'Weather', 'type' => 'weather_badge', 'align' => 'center'],
            ['key' => 'pressure_trend', 'label' => 'Pressure Movement', 'type' => 'pressure_trend', 'align' => 'center'],
            ['key' => 'lure.name', 'label' => 'Lure / Bait', 'sortable' => true],
            ['key' => 'weight', 'label' => 'Weight (lbs)', 'type' => 'heavy_record', 'align' => 'center', 'sortable' => true],
            ['key' => 'length', 'label' => 'Length (in)', 'type' => 'lunker_record', 'align' => 'center', 'sortable' => true],
            ['key' => 'released', 'label' => 'Status', 'type' => 'release_status', 'align' => 'center', 'sortable' => true],
        ],
        'filters' => [
            [
                'key' => 'angler',
                'type' => 'select',
                'label' => 'All Anglers',
                'column' => 'anglers_id',
                'options' => $anglersList->pluck('fullName', 'id')->toArray(),
            ],
            [
                'key' => 'lake',
                'type' => 'select',
                'label' => 'All Lakes & Waters',
                'column' => 'lakes_id',
                'options' => $lakesList->pluck('name', 'id')->toArray(),
            ],
            [
                'key' => 'species',
                'type' => 'select',
                'label' => 'All Species',
                'column' => 'fish_breeds_id',
                'options' => $speciesList->pluck('name', 'id')->toArray(),
            ],
            [
                'key' => 'weatherCondition',
                'type' => 'weather_condition',
                'label' => 'All Weather Conditions',
                'column' => 'weatherCondition',
                'options' => [
                    'Clear' => 'Clear Sky',
                    'Mainly' => 'Mainly Clear',
                    'Partly' => 'Partly Cloudy',
                    'Overcast' => 'Overcast',
                    'Fog' => 'Fog',
                    'Drizzle' => 'Drizzle',
                    'Rain' => 'Rain',
                    'Snow' => 'Snow',
                    'Thunderstorm' => 'Thunderstorm',
                ],
            ],
            [
                'key' => 'pressureTrend',
                'type' => 'pressure_trend',
                'label' => 'All Pressure Trends',
                'column' => 'pressureTrend',
                'options' => [
                    'falling' => '📉 Falling Barometer',
                    'rising' => '📈 Rising Barometer',
                    'stable' => '➡️ Stable Barometer',
                ],
            ],
            [
                'key' => 'length',
                'type' => 'operator_number',
                'label' => 'Length',
                'column' => 'length',
                'operatorKey' => 'lengthOperator',
                'defaultOperator' => '>',
            ],
            [
                'key' => 'caught',
                'type' => 'date_range',
                'label' => 'Date Range',
                'column' => 'caught',
                'startKey' => 'startDate',
                'endKey' => 'endDate',
            ],
        ],
        'searchPlaceholder' => 'Search species, lake, angler, lure...',
        'itemName' => 'catches',
        'perPage' => 15,
        'defaultSortBy' => 'caught',
        'defaultSortOrder' => 'desc',
    ])
</div>
@endsection
