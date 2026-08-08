@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl p-4 flex items-center gap-3 shadow-sm" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (isset($angler))
        <!-- Hero Angler Profile Header -->
        <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-5 text-center md:text-left">
                <div class="relative">
                    <x-anglerAvatar :angler="$angler" size="xl" />
                    <div class="absolute -bottom-1 -right-1 bg-teal-500 text-white rounded-full p-1 shadow">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ $angler->firstName }} {{ $angler->lastName }}</h1>
                    <p class="text-xs font-medium text-teal-400 mt-1 flex items-center justify-center md:justify-start gap-1.5">
                        <i data-lucide="anchor" class="w-3.5 h-3.5 text-teal-400"></i>
                        <span>Registered Angler Logbook & Telemetry</span>
                    </p>
                    @if($angler->bio)
                        <p class="text-xs text-slate-300 mt-2 max-w-xl italic">"{{ $angler->bio }}"</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ url('/record/quick') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-teal-950/40 transition-all">
                    <i data-lucide="zap" class="w-4 h-4 text-teal-200"></i>
                    <span>Quick Catch</span>
                </a>
            </div>
        </div>

        <!-- Metrics Key Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Lakes Visited -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Lakes Visited</span>
                    <span class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 block">{{ $lake_count }}</span>
                    <span class="text-[11px] text-teal-600 font-semibold mt-1 inline-flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3 h-3"></i> Unique Waters
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                    <i data-lucide="waves" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Fish Caught -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Fish Caught</span>
                    <span class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 block">{{ $record_count }}</span>
                    <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-3 h-3"></i> Logbook Catches
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <i data-lucide="fish" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Expeditions -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Expeditions</span>
                    <span class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 block">{{ $crews }}</span>
                    <span class="text-[11px] text-sky-600 font-semibold mt-1 inline-flex items-center gap-1">
                        <i data-lucide="navigation" class="w-3 h-3"></i> Crew Trips
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <i data-lucide="ship" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Personal Best Trophies Cards Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="trophy" class="w-5 h-5 text-amber-500"></i>
                    <span>Personal Best Trophies</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- 👑 Trophy By Length -->
                <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent bg-white p-5 rounded-2xl border border-amber-200 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1">
                            👑 Lunker Legend
                        </span>
                        <span class="text-xs font-black text-amber-600 bg-amber-100 px-2.5 py-0.5 rounded-full">Length</span>
                    </div>
                    @if(isset($personalBest['byLength']) && $personalBest['byLength'])
                        <div class="space-y-1 pt-1">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($personalBest['byLength']->length, 1) }}</span>
                                <span class="text-xs font-bold text-slate-500">inches</span>
                            </div>
                            <div class="text-xs font-bold text-teal-700">{{ $personalBest['byLength']->fishBreed->name ?? 'Fish' }}</div>
                            <div class="pt-2 border-t border-amber-100/80 flex items-center justify-between text-xs text-slate-600">
                                <span class="flex items-center gap-1 truncate">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                    <span class="truncate font-medium">{{ $personalBest['byLength']->lake->name ?? 'Lake' }}</span>
                                </span>
                                <span class="font-mono text-[11px] text-slate-400 shrink-0">{{ $personalBest['byLength']->caught }}</span>
                            </div>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-400 text-xs italic">
                            No length record logged yet.
                        </div>
                    @endif
                </div>

                <!-- 🏋️ Trophy By Weight -->
                <div class="bg-gradient-to-br from-sky-500/10 via-sky-500/5 to-transparent bg-white p-5 rounded-2xl border border-sky-200 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-800 flex items-center gap-1">
                            🏋️ Heavyweight Champ
                        </span>
                        <span class="text-xs font-black text-sky-600 bg-sky-100 px-2.5 py-0.5 rounded-full">Weight</span>
                    </div>
                    @if(isset($personalBest['byWeight']) && $personalBest['byWeight'])
                        <div class="space-y-1 pt-1">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($personalBest['byWeight']->weight, 1) }}</span>
                                <span class="text-xs font-bold text-slate-500">lbs.</span>
                            </div>
                            <div class="text-xs font-bold text-teal-700">{{ $personalBest['byWeight']->fishBreed->name ?? 'Fish' }}</div>
                            <div class="pt-2 border-t border-sky-100/80 flex items-center justify-between text-xs text-slate-600">
                                <span class="flex items-center gap-1 truncate">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                    <span class="truncate font-medium">{{ $personalBest['byWeight']->lake->name ?? 'Lake' }}</span>
                                </span>
                                <span class="font-mono text-[11px] text-slate-400 shrink-0">{{ $personalBest['byWeight']->caught }}</span>
                            </div>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-400 text-xs italic">
                            No weight record logged yet.
                        </div>
                    @endif
                </div>

                <!-- 🌊 Trophy Top Hotspot Lake -->
                <div class="bg-gradient-to-br from-teal-500/10 via-teal-500/5 to-transparent bg-white p-5 rounded-2xl border border-teal-200 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 flex items-center gap-1">
                            🔥 Top Hotspot
                        </span>
                        <span class="text-xs font-black text-teal-600 bg-teal-100 px-2.5 py-0.5 rounded-full">Water</span>
                    </div>
                    @if(isset($personalBest['lakeWithMostCatches']) && $personalBest['lakeWithMostCatches'])
                        <div class="space-y-1 pt-1">
                            <div class="text-xl font-extrabold text-slate-900 truncate tracking-tight">{{ $personalBest['lakeWithMostCatches']->name }}</div>
                            <div class="text-xs font-bold text-teal-700">Most Successful Angling Water</div>
                            <div class="pt-2 border-t border-teal-100/80 flex items-center gap-1.5 text-xs text-slate-600">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-teal-600"></i>
                                <span class="font-medium">High catch probability location</span>
                            </div>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-400 text-xs italic">
                            Log catches to reveal your top hotspot!
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 🎣 ANGLER PRODUCTION & GEAR TELEMETRY GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 📏 Cumulative Length Landed -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <i data-lucide="ruler" class="w-3.5 h-3.5 text-teal-600"></i> Lifetime Production
                    </span>
                    <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">Production</span>
                </div>
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ $totalFeet }}</span>
                        <span class="text-xs font-bold text-slate-500">ft. landed</span>
                    </div>
                    <div class="text-xs text-slate-500 flex items-center gap-2 pt-1 border-t border-slate-100">
                        <span>Total: <strong class="font-mono text-slate-800">{{ $totalInches }} in.</strong></span>
                        <span>•</span>
                        <span>Avg: <strong class="font-mono text-slate-800">{{ $avgLength > 0 ? $avgLength . ' in.' : '—' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- 🎣 MVP Go-To Lure -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <i data-lucide="fishing-hook" class="w-3.5 h-3.5 text-amber-500"></i> MVP Go-To Lure
                    </span>
                    <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Tackle</span>
                </div>
                @if($mvpLure && $mvpLure->lure)
                    <div class="space-y-1 pt-1">
                        <div class="text-sm font-bold text-slate-900 truncate">{{ $mvpLure->lure->name }}</div>
                        <div class="text-xs text-teal-700 font-bold font-mono">{{ $mvpLure->catches }} fish landed</div>
                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-100 flex items-center justify-between">
                            <span>Lure PB:</span>
                            <strong class="font-mono text-slate-800">{{ $mvpLure->longest ? $mvpLure->longest . ' in.' : '—' }}</strong>
                        </div>
                    </div>
                @else
                    <div class="text-xs text-slate-400 py-3 italic">No lure data logged.</div>
                @endif
            </div>

            <!-- 🌱 Conservation C&R Rate -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <i data-lucide="heart" class="w-3.5 h-3.5 text-emerald-500"></i> C&R Conservation
                    </span>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Conservation</span>
                </div>
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ $releaseRate }}%</span>
                        <span class="text-xs font-bold text-emerald-600">released</span>
                    </div>
                    <div class="text-xs text-slate-500 pt-1 border-t border-slate-100">
                        <strong class="font-mono text-slate-800">{{ $releasedCount }}</strong> of {{ $record_count }} fish safely released
                    </div>
                </div>
            </div>

            <!-- 🗓️ Seasonal Peak Month -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-sky-500"></i> Peak Month
                    </span>
                    <span class="text-xs font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200">Season</span>
                </div>
                @if($peakMonthName)
                    <div class="space-y-1 pt-1">
                        <div class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $peakMonthName }}</div>
                        <div class="text-xs text-sky-700 font-medium">Highest Production Month</div>
                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-100">
                            Peak strike window season
                        </div>
                    </div>
                @else
                    <div class="text-xs text-slate-400 py-3 italic">No seasonal data logged.</div>
                @endif
            </div>
        </div>

        <!-- 🌊 TOP WATERS & SPECIES DIVERSITY GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 🌊 Top Fished Waters -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="waves" class="w-4 h-4 text-teal-600"></i>
                        <span>Top Fished Waters</span>
                    </h2>
                    <span class="text-[11px] text-slate-400 font-mono font-semibold">{{ count($topWaters) }} Waters</span>
                </div>

                @if(count($topWaters) > 0)
                    <div class="space-y-3">
                        @foreach($topWaters as $idx => $tw)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-teal-500/10 text-teal-700 font-mono font-bold text-xs flex items-center justify-center border border-teal-500/20">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <a href="/lake/{{ $tw->lake->id }}" class="font-bold text-slate-900 text-xs hover:text-teal-600 hover:underline">
                                            {{ $tw->lake->name ?? 'Unknown Lake' }}
                                        </a>
                                        <span class="text-[10px] text-slate-500 block">Lake Record PB: <strong class="font-mono text-slate-800">{{ $tw->longest ? $tw->longest . ' in.' : '—' }}</strong></span>
                                    </div>
                                </div>
                                <div class="text-right font-mono">
                                    <span class="text-xs font-bold text-teal-700 block">{{ $tw->catches }} fish</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400 text-xs italic">
                        No waterbody data logged.
                    </div>
                @endif
            </div>

            <!-- 🐟 Angler Species Diversity Breakdown -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i data-lucide="pie-chart" class="w-4 h-4 text-teal-600"></i>
                            <span>Angler Species Ratio</span>
                        </h2>
                        <span class="text-[11px] text-slate-400 font-mono font-semibold">{{ count($speciesDistribution) }} Species</span>
                    </div>

                    @if(count($speciesDistribution) > 0)
                        @php
                            $hexColors = ['#0d9488', '#0284c7', '#6366f1', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];
                            $bgColors = ['bg-teal-500', 'bg-sky-500', 'bg-indigo-500', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-500', 'bg-pink-500'];
                            $gradientParts = [];
                            $currentPct = 0;
                            foreach($speciesDistribution as $idx => $sp) {
                                $pct = ($sp->count / $record_count) * 100;
                                $nextPct = $currentPct + $pct;
                                $hex = $hexColors[$idx % count($hexColors)];
                                $gradientParts[] = "{$hex} {$currentPct}% {$nextPct}%";
                                $currentPct = $nextPct;
                            }
                            $conicStyle = count($gradientParts) > 0 ? implode(', ', $gradientParts) : '#cbd5e1 0% 100%';
                        @endphp

                        <div class="flex flex-col sm:flex-row items-center gap-5 pt-2">
                            <!-- Donut Pie Chart -->
                            <div class="relative w-32 h-32 rounded-full shadow-md border-4 border-white shrink-0" style="background: conic-gradient({{ $conicStyle }});">
                                <div class="absolute inset-3 rounded-full bg-white flex flex-col items-center justify-center border border-slate-100 shadow-inner">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Species</span>
                                    <span class="text-lg font-black text-slate-900 font-mono leading-none my-0.5">{{ count($speciesDistribution) }}</span>
                                    <span class="text-[10px] text-teal-600 font-bold font-mono">{{ $record_count }} fish</span>
                                </div>
                            </div>

                            <!-- Legend Breakdown -->
                            <div class="w-full space-y-1.5">
                                @foreach($speciesDistribution as $idx => $sp)
                                    @php
                                        $pct = round(($sp->count / $record_count) * 100);
                                        $colorClass = $bgColors[$idx % count($bgColors)];
                                    @endphp
                                    <div class="flex items-center justify-between text-xs px-1">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $colorClass }} shrink-0"></span>
                                            <span class="font-bold text-slate-800 truncate">{{ $sp->fishBreed->name ?? 'Unknown' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 font-mono shrink-0">
                                            <span class="text-slate-500 text-[11px]">{{ $sp->count }}</span>
                                            <strong class="text-slate-900 w-8 text-right">{{ $pct }}%</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-slate-400 text-xs italic">
                            No species breakdown data available.
                        </div>
                    @endif
                </div>

                <!-- Species Highlights Telemetry Grid -->
                @if(count($speciesDistribution) > 0)
                    @php
                        $topSpecies = $speciesDistribution->first();
                    @endphp
                    <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dominant Target</span>
                            <strong class="text-xs font-black text-slate-900 truncate block">{{ $topSpecies->fishBreed->name ?? 'None' }}</strong>
                            <span class="text-[10px] text-teal-600 font-bold font-mono block">{{ round(($topSpecies->count / $record_count) * 100) }}% share ({{ $topSpecies->count }} fish)</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Species Diversity</span>
                            <strong class="text-xs font-black text-slate-900 font-mono block">{{ count($speciesDistribution) }} Breeds Logged</strong>
                            <span class="text-[10px] text-slate-500 font-mono block">{{ $record_count }} total catches</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Catches Logbook Quick Access Banner Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                    <i data-lucide="list" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Personal Catches Logbook</span>
                        <span class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">{{ $record_count }} Catches</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Explore your complete catch logbook with weather telemetry, lake locations, lure history, and search filters.</p>
                </div>
            </div>

            <a href="{{ url('/record') }}?search={{ urlencode($angler->firstName . ' ' . $angler->lastName) }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-md transition-all shrink-0">
                <span>View Full Logbook</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-teal-400"></i>
            </a>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-6 text-center space-y-3 shadow-sm">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-500 mx-auto"></i>
            <h3 class="font-bold text-base">User Not Associated with Angler Record</h3>
            <p class="text-xs text-amber-700 max-w-md mx-auto">Your account is not linked to an Angler profile. Please contact your system administrator to associate your account.</p>
        </div>
    @endif
</div>
@endsection
