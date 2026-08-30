@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Hero Showcase Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Species Illustration Canvas Header -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm flex flex-col items-center justify-center relative min-h-[240px] lg:min-h-[280px]">
            <div class="absolute top-3.5 left-3.5 flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold rounded-xl shadow-xs">
                    <x-lucide-layers class="w-3.5 h-3.5 text-slate-500" />
                    <span>{{ $fish->family?->name ? $fish->family->name . ' Family' : 'Taxonomy' }}</span>
                </span>
            </div>

            <div class="w-full h-full flex items-center justify-center p-2">
                @if($fish->imageUrl)
                    <img src="{{ $fish->imageUrl }}" alt="{{ $fish->name }}" class="max-h-56 w-auto object-contain mix-blend-multiply hover:scale-105 transition-transform duration-300">
                @else
                    <div class="text-center space-y-2 py-8">
                        <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center mx-auto">
                            <x-lucide-fish class="w-8 h-8" />
                        </div>
                        <span class="text-xs text-slate-400 font-medium block">No biological photo uploaded</span>
                    </div>
                @endif
            </div>
        </div>


        <!-- Right: Species Dossier & Trophy Hero -->
        <div class="lg:col-span-7 bg-slate-900 text-white rounded-2xl p-6 sm:p-7 border border-slate-800 shadow-md flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2.5 py-0.5 rounded-lg">
                            Species Dossier
                        </span>
                        <span class="text-xs text-slate-400 font-mono">ID: {{ substr($fish->id, 0, 8) }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="/fish" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1">
                            <x-lucide-arrow-left class="w-3.5 h-3.5" />
                            <span>Field Guide</span>
                        </a>
                        <a href="/fish/breed/{{ $fish->id }}/edit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1">
                            <x-lucide-edit-3 class="w-3.5 h-3.5" />
                            <span>Edit</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                        <span>{{ $fish->name }}</span>
                    </h1>
                    <p class="text-xs text-teal-400 font-semibold mt-0.5">
                        {{ $fish->family?->name ? $fish->family->name . ' Biological Family' : 'Freshwater Gamefish' }}
                    </p>
                </div>
            </div>

            <!-- Trophy Records Telemetry Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-slate-800/80 rounded-xl p-3 border border-slate-700/80 space-y-1">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Total Catches</span>
                        <x-lucide-fish class="w-3.5 h-3.5 text-teal-400" />
                    </div>
                    <span class="text-2xl font-black text-white block font-mono">{{ number_format($count) }}</span>
                    <span class="text-[10px] text-slate-400 block">Logged across all waters</span>
                </div>

                <div class="bg-gradient-to-br from-slate-800 to-slate-800/90 rounded-xl p-3 border border-amber-500/30 space-y-1">
                    <div class="flex items-center justify-between text-amber-300">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Max Length Record</span>
                        <x-lucide-trophy class="w-3.5 h-3.5 text-amber-400" />
                    </div>
                    <span class="text-2xl font-black text-amber-300 block font-mono">
                        {{ $longest ? number_format($longest, 1) . '"' : '—' }}
                    </span>
                    <span class="text-[10px] text-slate-400 block truncate">
                        {{ $recordTrophy?->angler?->fullName ? 'Held by ' . $recordTrophy->angler->fullName : 'No records yet' }}
                    </span>
                </div>

                <div class="bg-gradient-to-br from-slate-800 to-slate-800/90 rounded-xl p-3 border border-amber-500/30 space-y-1">
                    <div class="flex items-center justify-between text-amber-300">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Max Weight Record</span>
                        <x-lucide-award class="w-3.5 h-3.5 text-amber-400" />
                    </div>
                    <span class="text-2xl font-black text-amber-300 block font-mono">
                        {{ $fattest ? number_format($fattest, 1) . ' lbs.' : '—' }}
                    </span>
                    <span class="text-[10px] text-slate-400 block truncate">
                        {{ $heaviestTrophy?->angler?->fullName ? 'Held by ' . $heaviestTrophy->angler->fullName : 'No weight data' }}
                    </span>
                </div>
            </div>

            <!-- Quick Action CTA -->
            <div class="pt-2 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-3">
                <a 
                    href="/record/quick?fish_breed_id={{ $fish->id }}" 
                    class="px-4 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs rounded-xl shadow transition-all flex items-center gap-1.5 cursor-pointer active:scale-95"
                >
                    <x-lucide-plus-circle class="w-4 h-4 text-slate-950" />
                    <span>Log Catch for {{ $fish->name }}</span>
                </a>

                <span class="text-xs text-slate-400 font-mono">
                    {{ count($lakes) }} {{ count($lakes) == 1 ? 'Waterbody' : 'Waterbodies' }} documented
                </span>
            </div>
        </div>
    </div>

    <!-- Telemetry Intelligence Modules (Top Lures, Angler Leaderboard, Thermal & Seasonal Triggers) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Module 1: Top Productive Lures & Colors -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <x-lucide-target class="w-4 h-4 text-teal-600" />
                    <span>Productive Lures & Colors</span>
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 font-mono">{{ $topLures->count() }} Types</span>
            </div>

            @if($topLures->count() > 0)
                <div class="space-y-3">
                    @foreach($topLures as $tl)
                        @php
                            $pct = $count > 0 ? round(($tl->catches_count / $count) * 100) : 0;
                            $lureName = $tl->lure?->name ?? 'Lure #' . $tl->lures_id;
                            $lureColor = $tl->lure?->color;
                            $lureSize = $tl->lure?->size;
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <div class="truncate max-w-[170px]">
                                    <a href="/lure/{{ $tl->lures_id }}" class="font-bold text-slate-900 hover:text-teal-600 truncate block">
                                        {{ $lureName }}
                                    </a>
                                    @if($lureColor || $lureSize)
                                        <span class="text-[10px] text-slate-500 font-medium block truncate">
                                            {{ implode(' • ', array_filter([$lureColor, $lureSize])) }}
                                        </span>
                                    @endif
                                </div>
                                <span class="font-mono font-bold text-slate-800 text-[11px] shrink-0">
                                    {{ $tl->catches_count }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span>
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-teal-500 h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-emptyState icon="disc" title="No Lure Telemetry" description="No tackle or lure entries logged for this species yet." />
            @endif
        </div>

        <!-- Module 2: Angler Leaderboard -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <x-lucide-trophy class="w-4 h-4 text-amber-500" />
                    <span>Species Angler Leaderboard</span>
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 font-mono">Top Anglers</span>
            </div>

            @if($topAnglers->count() > 0)
                <div class="space-y-3">
                    @foreach($topAnglers as $index => $ta)
                        <div class="flex items-center justify-between text-xs py-1">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black font-mono shrink-0 {{ $index == 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $index + 1 }}
                                </span>
                                @if($ta->angler)
                                    <x-anglerAvatar :angler="$ta->angler" size="sm" />
                                    <a href="/angler/{{ $ta->angler->id }}/profile" class="font-bold text-slate-900 hover:text-teal-600 truncate">
                                        {{ $ta->angler->fullName }}
                                    </a>
                                @else
                                    <span class="font-semibold text-slate-600">Unknown</span>
                                @endif
                            </div>

                            <div class="text-right shrink-0">
                                <span class="font-bold font-mono text-teal-700">{{ $ta->catches_count }} catch{{ $ta->catches_count === 1 ? '' : 'es' }}</span>
                                @if($ta->longest_catch)
                                    <span class="text-[10px] text-amber-700 font-bold block font-mono">PB: {{ number_format($ta->longest_catch, 1) }}"</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-emptyState icon="user-x" title="No Anglers Logged" description="Be the first angler to record a catch for this species!" />
            @endif
        </div>

        <!-- Module 3: Seasonal & Weather Triggers -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <x-lucide-thermometer-sun class="w-4 h-4 text-sky-600" />
                    <span>Seasonal & Weather Triggers</span>
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 font-mono">Telemetry</span>
            </div>

            <!-- Thermal Water Temp Telemetry Badge -->
            @if(isset($weatherTelemetry) && $weatherTelemetry->avg_temp)
                <div class="bg-sky-50 border border-sky-100 rounded-xl p-2 text-xs flex items-center justify-between">
                    <span class="text-[11px] font-bold text-sky-900 flex items-center gap-1.5">
                        <x-lucide-thermometer class="w-3.5 h-3.5 text-sky-600" />
                        <span>Productive Water Temp Range</span>
                    </span>
                    <span class="font-mono font-bold text-sky-900 text-xs">
                        {{ round($weatherTelemetry->min_temp) }}° &mdash; {{ round($weatherTelemetry->max_temp) }}°F
                    </span>
                </div>
            @endif

            <!-- Graph 1: Monthly Activity Bar Chart -->
            <div class="grid grid-cols-8 gap-1.5 items-end h-24 pt-1">
                @foreach($monthlyStats as $ms)
                    <div class="flex flex-col items-center gap-1 h-full justify-end">
                        <span class="text-[10px] font-bold font-mono {{ $ms['count'] > 0 ? 'text-teal-700' : 'text-slate-300' }}">
                            {{ $ms['count'] }}
                        </span>
                        <div class="w-full bg-slate-100 rounded-t-md flex items-end h-14">
                            <div 
                                class="w-full rounded-t-md transition-all {{ $ms['count'] > 0 ? 'bg-teal-500' : 'bg-slate-200' }}" 
                                style="height: {{ max($ms['percentage'], 6) }}%"
                            ></div>
                        </div>
                        <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-tighter">
                            {{ $ms['month'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Graph 2: Atmospheric Weather Conditions Bar Chart (Active Conditions Only) -->
            @php
                $activeWeatherStats = collect($weatherStats ?? [])->filter(fn($ws) => $ws['count'] > 0);
            @endphp
            @if($activeWeatherStats->count() > 0)
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                            <x-lucide-cloud-lightning class="w-3.5 h-3.5 text-indigo-500" />
                            <span>Productive Weather Triggers</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono font-semibold">{{ $activeWeatherStats->count() }} Productive</span>
                    </div>

                    <div class="flex items-end justify-around gap-2 h-28 pt-1">
                        @foreach($activeWeatherStats as $ws)
                            @php
                                $wKey = strtolower($ws['key'] ?? $ws['label'] ?? '');
                                $iconConfig = match (true) {
                                    str_contains($wKey, 'clear sky') || $wKey === 'sunny' || $wKey === 'clear' => ['icon' => 'sun', 'color' => 'text-amber-500'],
                                    str_contains($wKey, 'mainly') => ['icon' => 'sun-medium', 'color' => 'text-amber-400'],
                                    str_contains($wKey, 'partly') => ['icon' => 'cloud-sun', 'color' => 'text-amber-400'],
                                    str_contains($wKey, 'overcast') => ['icon' => 'cloud', 'color' => 'text-slate-400'],
                                    str_contains($wKey, 'fog') => ['icon' => 'cloud-fog', 'color' => 'text-slate-400'],
                                    str_contains($wKey, 'drizzle') => ['icon' => 'cloud-drizzle', 'color' => 'text-sky-400'],
                                    str_contains($wKey, 'rain') => ['icon' => 'cloud-rain', 'color' => 'text-blue-500'],
                                    str_contains($wKey, 'snow') => ['icon' => 'snowflake', 'color' => 'text-cyan-300'],
                                    str_contains($wKey, 'thunder') || str_contains($wKey, 'storm') => ['icon' => 'cloud-lightning', 'color' => 'text-purple-500'],
                                    default => ['icon' => 'cloud-sun', 'color' => 'text-indigo-500'],
                                };
                            @endphp
                            <div class="flex-1 max-w-[60px] flex flex-col items-center gap-1 h-full justify-end group relative" title="{{ $ws['key'] }}: {{ $ws['count'] }} catches">
                                <x-dynamic-component :component="'lucide-' . $iconConfig['icon']" class="w-3.5 h-3.5 {{ $iconConfig['color'] }} shrink-0" />
                                <span class="text-[10px] font-bold font-mono text-indigo-600">
                                    {{ $ws['count'] }}
                                </span>
                                <div class="w-full bg-slate-100 rounded-t-md flex items-end h-14">
                                    <div 
                                        class="w-full rounded-t-md transition-all bg-indigo-500 group-hover:bg-indigo-400 shadow-2xs" 
                                        style="height: {{ max($ws['percentage'], 10) }}%"
                                    ></div>
                                </div>
                                <span class="text-[8px] font-bold text-slate-500 uppercase tracking-tighter truncate w-full text-center">
                                    {{ $ws['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Lake Distribution & Waters Breakdown -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <x-lucide-waves class="w-4 h-4 text-teal-600" />
                <span>Documented Lake Distribution for {{ $fish->name }}</span>
            </h2>
            <span class="text-xs text-slate-500 font-mono">{{ count($lakes) }} Waterbody Location(s)</span>
        </div>

        @if(count($lakes) > 0)
            @livewire('components.generic-data-table', [
                'modelClass' => \Fishinglog\Models\Lake::class,
                'columns' => [
                    ['key' => 'name', 'label' => 'Lake Name', 'type' => 'lake_name', 'sortable' => true, 'searchable' => true],
                    ['key' => 'records_count', 'label' => 'Total Catches', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'records_count'],
                    ['key' => 'visits', 'label' => 'Recorded Visits', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'visits'],
                ],
                'searchPlaceholder' => 'Search lakes...',
                'itemName' => 'lakes',
                'perPage' => 10,
            ])
        @else
            <x-emptyState icon="map-pin-off" title="No Lake Distribution Data" description="No waterbody locations logged for this species yet." />
        @endif
    </div>

    <!-- Catches Logbook Directory Callout Banner -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-600 flex items-center justify-center shrink-0 shadow-inner">
                <x-lucide-book-open class="w-6 h-6" />
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>Catches Logbook Directory</span>
                    <span class="bg-teal-50 text-teal-700 font-mono text-xs font-bold px-2.5 py-0.5 rounded-full border border-teal-200">
                        {{ number_format($count) }} Records
                    </span>
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Explore, search, and filter the complete catches directory with weather telemetry, lake locations, and lure history.
                </p>
            </div>
        </div>

        <div class="shrink-0 w-full md:w-auto">
            <a href="{{ url('/record/directory?species=' . $fish->id) }}" class="w-full md:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center justify-center gap-2 cursor-pointer">
                <span>Open Logbook Directory</span>
                <x-lucide-arrow-right class="w-4 h-4 text-teal-400" />
            </a>
        </div>
    </div>
</div>
@endsection
