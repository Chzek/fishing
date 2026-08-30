@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- 1. Dark Slate Hero Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-5">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                    <x-lucide-fish class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Catches Logbook & Telemetry</h1>
                    <p class="text-xs text-slate-400 font-medium pt-0.5">High-Level Angling Production, System Records & Logbook Directory</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ url('/record/create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-950/50 transition-all flex items-center gap-1.5">
                    <x-lucide-plus class="w-4 h-4" />
                    <span>Log New Catch</span>
                </a>
                <a href="{{ url('/record/quick') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-950/50 transition-all flex items-center gap-1.5">
                    <x-lucide-zap class="w-4 h-4 text-emerald-200" />
                    <span>Boat Quick Catch</span>
                </a>
                <a href="{{ url('/record/offline-review') }}" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                    <x-lucide-refresh-cw class="w-3.5 h-3.5" />
                    <span>Offline Review</span>
                </a>
            </div>
        </div>

        <div class="flex flex-row items-stretch gap-2.5 pt-3.5 border-t border-slate-800 text-xs font-mono">
            <div class="flex-1 p-2.5 rounded-xl bg-slate-800/50 border border-slate-700/60 min-w-0 flex flex-col justify-between">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold truncate">Total Catches</span>
                <span class="text-base sm:text-xl font-black text-white block pt-1 truncate">{{ number_format($totalCatches) }}</span>
            </div>
            <div class="flex-1 p-2.5 rounded-xl bg-slate-800/50 border border-slate-700/60 min-w-0 flex flex-col justify-between">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold truncate">Landed Feet</span>
                <span class="text-base sm:text-xl font-black text-teal-400 block pt-1 truncate">{{ number_format($totalFeet, 1) }} ft</span>
            </div>
            <div class="flex-1 p-2.5 rounded-xl bg-slate-800/50 border border-slate-700/60 min-w-0 flex flex-col justify-between">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold truncate">C&R Release</span>
                <span class="text-base sm:text-xl font-black text-emerald-400 block pt-1 truncate">{{ $releaseRate }}%</span>
            </div>
            <div class="flex-1 p-2.5 rounded-xl bg-slate-800/50 border border-slate-700/60 min-w-0 flex flex-col justify-between">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold truncate">Average Length</span>
                <span class="text-base sm:text-xl font-black text-sky-400 block pt-1 truncate">{{ $avgLength }} in</span>
            </div>
            <div class="flex-1 p-2.5 rounded-xl bg-slate-800/50 border border-slate-700/60 min-w-0 flex flex-col justify-between">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold truncate">Weather Sync</span>
                <span class="text-base sm:text-xl font-black text-amber-400 block pt-1 truncate">{{ $weatherCoverageRate }}%</span>
            </div>
        </div>
    </div>

    <!-- 2. High-Level Telemetry Cards Grid -->
    <div class="flex flex-col lg:flex-row items-stretch gap-4">
        <!-- Card 1: Cumulative Production -->
        <div class="flex-1 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2 min-w-0">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <x-lucide-ruler class="w-3.5 h-3.5 text-teal-600" /> Lifetime Production
                </span>
                <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200 font-mono">Distance</span>
            </div>
            <div class="space-y-1 pt-1">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black text-slate-900 font-mono">{{ number_format($totalFeet, 1) }}</span>
                    <span class="text-xs font-bold text-slate-500">ft. landed</span>
                </div>
                <div class="text-xs text-slate-500 flex items-center justify-between pt-1.5 border-t border-slate-100 font-mono">
                    <span>Total: <strong class="text-slate-800">{{ number_format($totalInches, 1) }} in</strong></span>
                    <span>Avg: <strong class="text-slate-800">{{ $avgLength }} in</strong></span>
                </div>
            </div>
        </div>

        <!-- Card 2: C&R Conservation Index -->
        <div class="flex-1 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2 min-w-0">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <x-lucide-heart-handshake class="w-3.5 h-3.5 text-emerald-600" /> C&R Conservation Rate
                </span>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 font-mono">Released</span>
            </div>
            <div class="space-y-1 pt-1">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black text-emerald-600 font-mono">{{ $releaseRate }}%</span>
                    <span class="text-xs font-bold text-slate-500">release rate</span>
                </div>
                <div class="text-xs text-slate-500 flex items-center justify-between pt-1.5 border-t border-slate-100 font-mono">
                    <span>Released: <strong class="text-amber-700">{{ number_format($releasedCount) }}</strong></span>
                    <span>Kept: <strong class="text-emerald-700">{{ number_format($totalCatches - $releasedCount) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Card 3: All-Time Longest Catch -->
        <div class="flex-1 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2 min-w-0">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <x-lucide-trophy class="w-3.5 h-3.5 text-amber-500" /> Longest Catch Record
                </span>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 font-mono">Lunker</span>
            </div>
            @if($longestCatch)
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ $longestCatch->length }}″</span>
                        <span class="text-xs font-bold text-teal-600 truncate max-w-[120px]">{{ $longestCatch->fishBreed->name }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-100 truncate">
                        👤 {{ $longestCatch->angler->full_name }} • 🏞️ {{ $longestCatch->lake->name }}
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400 pt-3 italic">No length records logged.</p>
            @endif
        </div>

        <!-- Card 4: All-Time Heaviest Catch -->
        <div class="flex-1 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2 min-w-0">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <x-lucide-scale class="w-3.5 h-3.5 text-sky-600" /> Heavyweight Record
                </span>
                <span class="text-xs font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200 font-mono">Weight</span>
            </div>
            @if($heaviestCatch)
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ $heaviestCatch->weight }} lbs</span>
                        <span class="text-xs font-bold text-sky-600 truncate max-w-[110px]">{{ $heaviestCatch->fishBreed->name }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-100 truncate">
                        👤 {{ $heaviestCatch->angler->full_name }} • 🏞️ {{ $heaviestCatch->lake->name }}
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400 pt-3 italic">No weight records logged.</p>
            @endif
        </div>

        <!-- Card 5: Atmospheric & Weather Telemetry -->
        <div class="flex-1 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2 min-w-0">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <x-lucide-cloud-sun class="w-3.5 h-3.5 text-indigo-600" /> Atmospheric Weather
                </span>
                <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200 font-mono">Air & Wind</span>
            </div>
            <div class="space-y-1 pt-1">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black text-slate-900 font-mono">{{ $avgAirTemp ? $avgAirTemp . '°F' : 'N/A' }}</span>
                    <span class="text-xs font-bold text-indigo-600 font-mono">mean temp</span>
                </div>
                <div class="text-[11px] text-slate-500 pt-1.5 border-t border-slate-100 flex items-center justify-between font-mono">
                    <span>Press: <strong class="text-slate-800">{{ $avgBarometricPressure ? $avgBarometricPressure . ' hPa' : '—' }}</strong></span>
                    <span>Wind: <strong class="text-slate-800">{{ $avgWindSpeed ? $avgWindSpeed . ' mph' : '—' }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Leaderboards & Macro Target Species Trends Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top 5 Anglers Leaderboard -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <x-lucide-award class="w-4 h-4 text-amber-500" />
                    <span>Top 5 Angler Producers</span>
                </h2>
                <a href="{{ url('/angler') }}" class="text-[11px] font-bold text-teal-600 hover:underline">View All ↗</a>
            </div>

            <div class="space-y-2.5">
                @foreach($topAnglers as $index => $tAngler)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/80 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-6 h-6 rounded-lg text-xs font-bold flex items-center justify-center shrink-0 font-mono {{ $index === 0 ? 'bg-amber-400 text-slate-950 shadow-sm' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-200 text-slate-600')) }}">
                                #{{ $index + 1 }}
                            </span>
                            <div class="truncate">
                                <a href="{{ url('/angler/' . $tAngler->angler->id . '/profile') }}" class="text-xs font-bold text-slate-900 hover:text-teal-600 block truncate">
                                    {{ $tAngler->angler->full_name }}
                                </a>
                                <span class="text-[10px] text-slate-500 font-mono block">Max Lunker: {{ $tAngler->max_length ? $tAngler->max_length . '″' : '—' }}</span>
                            </div>
                        </div>
                        <span class="text-xs font-black text-teal-700 font-mono bg-teal-50 px-2 py-0.5 rounded border border-teal-200 shrink-0">
                            {{ $tAngler->catches_count }} catches
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top 5 Lakes Leaderboard -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <x-lucide-waves class="w-4 h-4 text-teal-600" />
                    <span>Top 5 Waterbodies</span>
                </h2>
                <a href="{{ url('/lake') }}" class="text-[11px] font-bold text-teal-600 hover:underline">View All ↗</a>
            </div>

            <div class="space-y-2.5">
                @foreach($topLakes as $index => $tLake)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/80 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-6 h-6 rounded-lg text-xs font-bold flex items-center justify-center shrink-0 font-mono bg-slate-200 text-slate-700">
                                #{{ $index + 1 }}
                            </span>
                            <div class="truncate">
                                <a href="{{ url('/lake/' . $tLake->lake->id) }}" class="text-xs font-bold text-slate-900 hover:text-teal-600 block truncate">
                                    {{ $tLake->lake->name }}
                                </a>
                                <span class="text-[10px] text-slate-500 font-mono block">Max Fish: {{ $tLake->max_length ? $tLake->max_length . '″' : '—' }}</span>
                            </div>
                        </div>
                        <span class="text-xs font-black text-slate-800 font-mono bg-slate-100 px-2 py-0.5 rounded border border-slate-200 shrink-0">
                            {{ $tLake->catches_count }} catches
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Macro Species Catch Trends -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <x-lucide-trending-up class="w-4 h-4 text-indigo-600" />
                    <span>Macro Species Target Shifts</span>
                </h2>
                <span class="text-[10px] font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                    {{ $latestYear }} vs {{ $prevYear }}
                </span>
            </div>

            <div class="space-y-3">
                @foreach($speciesTrends as $trend)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5 truncate">
                                <x-lucide-fish class="w-3.5 h-3.5 text-teal-600 shrink-0" />
                                <span class="truncate">{{ $trend->fishBreed->name }}</span>
                            </span>
                            <div class="flex items-center gap-2 shrink-0 font-mono text-[11px]">
                                <span class="text-slate-600 font-semibold">{{ $trend->percentage }}% share</span>
                                @if($trend->shift > 0)
                                    <span class="text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">+{{ $trend->shift }}% ↑</span>
                                @elseif($trend->shift < 0)
                                    <span class="text-rose-700 font-bold bg-rose-50 px-1.5 py-0.5 rounded">{{ $trend->shift }}% ↓</span>
                                @else
                                    <span class="text-slate-500 font-bold bg-slate-100 px-1.5 py-0.5 rounded">0%</span>
                                @endif
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-teal-500 to-indigo-500 h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(5, $trend->percentage)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 4. Weather Condition Distribution & Best Lake Analytics Section -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <x-lucide-cloud-sun class="w-5 h-5 text-sky-600" />
                    <span>Weather Condition Distribution & Top Producing Lakes</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Atmospheric field telemetry breakdown and top performing waterbody for each weather condition</p>
            </div>

            <span class="text-xs font-mono font-bold text-sky-700 bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
                {{ $weatherDistribution->count() }} Weather Conditions Logged
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($weatherDistribution as $item)
                @php
                    $cond = strtolower($item->weather_condition ?? '');
                    $config = match (true) {
                        str_contains($cond, 'clear sky') || $cond === 'sunny' || $cond === 'clear' => [
                            'icon' => 'sun',
                            'emoji' => '☀️',
                            'color' => 'text-amber-500',
                            'bgColor' => 'bg-amber-50 border-amber-200/80',
                        ],
                        str_contains($cond, 'mainly clear') => [
                            'icon' => 'sun-medium',
                            'emoji' => '🌤️',
                            'color' => 'text-amber-400',
                            'bgColor' => 'bg-amber-50 border-amber-200/80',
                        ],
                        str_contains($cond, 'partly') || str_contains($cond, 'cloudy') => [
                            'icon' => 'cloud-sun',
                            'emoji' => '⛅',
                            'color' => 'text-amber-300',
                            'bgColor' => 'bg-sky-50 border-sky-200/80',
                        ],
                        str_contains($cond, 'overcast') => [
                            'icon' => 'cloud',
                            'emoji' => '☁️',
                            'color' => 'text-slate-400',
                            'bgColor' => 'bg-slate-100 border-slate-200',
                        ],
                        str_contains($cond, 'fog') => [
                            'icon' => 'cloud-fog',
                            'emoji' => '🌫️',
                            'color' => 'text-slate-400',
                            'bgColor' => 'bg-slate-100 border-slate-200',
                        ],
                        str_contains($cond, 'drizzle') => [
                            'icon' => 'cloud-drizzle',
                            'emoji' => '🌧️',
                            'color' => 'text-sky-400',
                            'bgColor' => 'bg-sky-50 border-sky-200/80',
                        ],
                        str_contains($cond, 'rain') => [
                            'icon' => 'cloud-rain',
                            'emoji' => '🌧️',
                            'color' => 'text-blue-500',
                            'bgColor' => 'bg-blue-50 border-blue-200/80',
                        ],
                        str_contains($cond, 'snow') => [
                            'icon' => 'snowflake',
                            'emoji' => '❄️',
                            'color' => 'text-cyan-300',
                            'bgColor' => 'bg-cyan-50 border-cyan-200/80',
                        ],
                        str_contains($cond, 'thunder') || str_contains($cond, 'storm') => [
                            'icon' => 'cloud-lightning',
                            'emoji' => '🌩️',
                            'color' => 'text-purple-500',
                            'bgColor' => 'bg-purple-50 border-purple-200/80',
                        ],
                        default => [
                            'icon' => 'thermometer',
                            'emoji' => '🌡️',
                            'color' => 'text-slate-500',
                            'bgColor' => 'bg-slate-50 border-slate-200',
                        ],
                    };

                    $cleanTitle = trim(preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $item->weather_condition ?? 'Unknown'));
                @endphp
                <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 hover:bg-slate-50 hover:border-slate-300 transition-all space-y-3">
                    <div class="flex items-center gap-3">
                        <!-- Prominent Left Weather Icon Badge -->
                        <div class="w-10 h-10 rounded-xl {{ $config['bgColor'] }} flex items-center justify-center shrink-0 shadow-xs">
                            <x-dynamic-component :component="'lucide-' . $config['icon']" class="w-5 h-5 {{ $config['color'] }}" />
                        </div>

                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-extrabold text-sm text-slate-900 truncate" title="{{ $cleanTitle }}">
                                    {{ $cleanTitle }}
                                </span>
                                <div class="flex items-center gap-1 font-mono text-[11px] shrink-0">
                                    <span class="text-slate-800 font-black">{{ number_format($item->catches_count) }} catches</span>
                                    <span class="text-teal-700 font-bold bg-teal-50 px-1.5 py-0.5 rounded-full border border-teal-200">{{ $item->percentage }}%</span>
                                </div>
                            </div>

                            <!-- Progress bar -->
                            <div class="w-full bg-slate-200/80 h-2 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-teal-500 to-sky-500 h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(5, $item->percentage)) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Key Weather Metrics -->
                    <div class="flex items-center justify-between text-xs text-slate-500 font-mono pt-0.5">
                        <span>Avg Length: <strong class="text-slate-900">{{ $item->avg_length ? $item->avg_length . '″' : '—' }}</strong></span>
                        <span>Barometer: <strong class="text-slate-900">{{ $item->avg_pressure ? $item->avg_pressure . ' hPa' : '—' }}</strong></span>
                    </div>

                    <!-- Best Lake Banner -->
                    @if($item->best_lake_name)
                        <div class="pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-xs">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1">
                                <x-lucide-waves class="w-3.5 h-3.5 text-teal-600" /> Best Lake:
                            </span>
                            <a href="{{ url('/lake/' . $item->best_lake_id) }}" class="font-extrabold text-teal-700 hover:text-teal-900 hover:underline truncate max-w-[170px]" title="{{ $item->best_lake_name }}">
                                {{ $item->best_lake_name }}
                                <span class="font-mono text-[10px] font-normal text-slate-500">({{ $item->best_lake_catches }})</span>
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-3 py-8 text-center text-slate-400 italic text-xs">
                    No daily weather telemetry logged yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 5. Catches Logbook Directory Quick Access Banner Card -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                <x-lucide-book-open class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>Catches Logbook Directory</span>
                    <span class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">{{ number_format($totalCatches) }} Records</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Explore, search, and filter the complete catches directory with weather telemetry, lake locations, and lure history.</p>
            </div>
        </div>

        <a href="{{ url('/record/directory') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-md transition-all shrink-0">
            <span>Open Logbook Directory</span>
            <x-lucide-arrow-right class="w-4 h-4 text-teal-400" />
        </a>
    </div>
</div>
@endsection
