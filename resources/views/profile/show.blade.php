@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <x-statusAlert />

    @if (isset($angler))
        <!-- Hero Angler Profile Header -->
        <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-5 text-center md:text-left">
                <div class="relative">
                    <x-anglerAvatar :angler="$angler" size="xl" />
                    <div class="absolute -bottom-1 -right-1 bg-teal-500 text-white rounded-full p-1 shadow">
                        <x-lucide-shield-check class="w-3.5 h-3.5" />
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center justify-center md:justify-start gap-2">
                        <span>{{ $angler->firstName }} {{ $angler->lastName }}</span>
                        <a href="/angler/{{ $angler->id }}/edit" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Edit Angler Profile">
                            <x-lucide-edit-3 class="w-4 h-4 text-teal-400" />
                        </a>
                    </h1>
                    <p class="text-xs font-medium text-teal-400 mt-1 flex items-center justify-center md:justify-start gap-1.5">
                        <x-lucide-anchor class="w-3.5 h-3.5 text-teal-400" />
                        <span>Registered Angler Logbook & Telemetry</span>
                    </p>
                    @if($angler->bio)
                        <p class="text-xs text-slate-300 mt-2 max-w-xl italic">"{{ $angler->bio }}"</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ url('/record/quick') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-teal-950/40 transition-all">
                    <x-lucide-zap class="w-4 h-4 text-teal-200" />
                    <span>Quick Catch</span>
                </a>
            </div>
        </div>

        <!-- Metrics Key Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <x-kpiMetric label="Lakes Visited" :value="$lake_count" icon="waves" color="teal" subtext="Unique Waters" subtextIcon="map-pin" />
            <x-kpiMetric label="Fish Caught" :value="$record_count" icon="fish" color="emerald" subtext="Logbook Catches" subtextIcon="trending-up" />
            <x-kpiMetric label="Expeditions" :value="$crews" icon="ship" color="sky" subtext="Crew Trips" subtextIcon="navigation" />
        </div>

        <!-- In-App Notifications Feed (For All Anglers/Users) -->
        @if(!empty($unreadNotifications) && $unreadNotifications->count() > 0)
            <div id="notifications" class="bg-amber-50/80 dark:bg-amber-950/40 rounded-2xl p-5 border border-amber-200/80 dark:border-amber-800/80 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <x-lucide-bell class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                                <span>Unread Notifications & Trophy Alerts</span>
                                <x-badge variant="amber" size="sm" fontMono>{{ $unreadNotifications->count() }} New</x-badge>
                            </h2>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Personal logbook alerts and milestone notifications.</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.notifications.mark_read') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm transition-colors cursor-pointer">
                            Dismiss All
                        </button>
                    </form>
                </div>

                <div class="divide-y divide-amber-200/60 dark:divide-amber-800/60 border-t border-amber-200/60 dark:border-amber-800/60 pt-2 space-y-2">
                    @foreach($unreadNotifications as $notification)
                        <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 pt-2 gap-3">
                            <div class="flex items-center gap-2.5">
                                <x-lucide-trophy class="w-4 h-4 text-amber-500 shrink-0" />
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $notification->data['title'] ?? ($notification->data['type'] ?? 'Notification') }}</div>
                                    <div class="text-slate-600 dark:text-slate-400 mt-0.5">{{ $notification->data['message'] ?? 'You have a new update.' }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if(!empty($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-[11px] rounded-lg transition-colors">
                                        View →
                                    </a>
                                @endif
                                <form action="{{ route('admin.notifications.mark_single_read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:underline font-medium">
                                        Dismiss
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Personal Best Trophies Cards Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <x-lucide-trophy class="w-5 h-5 text-amber-500" />
                    <span>Personal Best Trophies</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- 👑 Trophy By Length -->
                <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent bg-white dark:bg-slate-900 p-5 rounded-2xl border border-amber-200 dark:border-amber-800/80 shadow-sm space-y-2 relative overflow-hidden">
                    <x-watermarkTapeMeasure />
                    <div class="flex items-center justify-between relative z-10">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-400 flex items-center gap-1">
                            👑 Lunker Legend
                        </span>
                        <x-badge variant="amber" size="sm" fontMono>Length</x-badge>
                    </div>
                    @if(isset($personalBest['byLength']) && $personalBest['byLength'])
                        <div class="flex items-center gap-3.5 pt-1 relative z-10">
                            <x-fishAvatar :breed="$personalBest['byLength']->fishBreed" size="xl" class="shadow-sm ring-2 ring-amber-400/40" />
                            <div class="space-y-1 min-w-0 flex-1">
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ number_format($personalBest['byLength']->length, 1) }}</span>
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">inches</span>
                                </div>
                                <div class="text-xs font-bold text-teal-700 dark:text-teal-400 truncate">{{ $personalBest['byLength']->fishBreed->name ?? 'Fish' }}</div>
                                <div class="pt-2 border-t border-amber-100/80 dark:border-slate-800 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
                                    <span class="flex items-center gap-1 truncate">
                                        <x-lucide-map-pin class="w-3 h-3 text-slate-400 shrink-0" />
                                        <span class="truncate font-medium">{{ $personalBest['byLength']->lake->name ?? 'Lake' }}</span>
                                    </span>
                                    <span class="font-mono text-[11px] text-slate-400 shrink-0">{{ \Illuminate\Support\Carbon::parse($personalBest['byLength']->caught)->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-400 text-xs italic relative z-10">
                            No length record logged yet.
                        </div>
                    @endif
                </div>

                <!-- 🏋️ Trophy By Weight -->
                <div class="bg-gradient-to-br from-sky-500/10 via-sky-500/5 to-transparent bg-white dark:bg-slate-900 p-5 rounded-2xl border border-sky-200 dark:border-sky-800/80 shadow-sm space-y-2 relative overflow-hidden">
                    <x-watermarkDialScale />
                    <div class="flex items-center justify-between relative z-10">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-800 dark:text-sky-400 flex items-center gap-1">
                            🏋️ Heavyweight Champ
                        </span>
                        <x-badge variant="sky" size="sm" fontMono>Weight</x-badge>
                    </div>
                    @if(isset($personalBest['byWeight']) && $personalBest['byWeight'])
                        <div class="flex items-center gap-3.5 pt-1 relative z-10">
                            <x-fishAvatar :breed="$personalBest['byWeight']->fishBreed" size="xl" class="shadow-sm ring-2 ring-sky-400/40" />
                            <div class="space-y-1 min-w-0 flex-1">
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ number_format($personalBest['byWeight']->weight, 1) }}</span>
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">lbs</span>
                                </div>
                                <div class="text-xs font-bold text-sky-700 dark:text-sky-400 truncate">{{ $personalBest['byWeight']->fishBreed->name ?? 'Fish' }}</div>
                                <div class="pt-2 border-t border-sky-100/80 dark:border-slate-800 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
                                    <span class="flex items-center gap-1 truncate">
                                        <x-lucide-map-pin class="w-3 h-3 text-slate-400 shrink-0" />
                                        <span class="truncate font-medium">{{ $personalBest['byWeight']->lake->name ?? 'Lake' }}</span>
                                    </span>
                                    <span class="font-mono text-[11px] text-slate-400 shrink-0">{{ \Illuminate\Support\Carbon::parse($personalBest['byWeight']->caught)->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-400 text-xs italic relative z-10">
                            No weight record logged yet.
                        </div>
                    @endif
                </div>

                <!-- 🗺️ Lake Legend -->
                <div class="bg-gradient-to-br from-teal-500/10 via-teal-500/5 to-transparent bg-white dark:bg-slate-900 p-5 rounded-2xl border border-teal-200 dark:border-teal-800/80 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 dark:text-teal-400 flex items-center gap-1">
                            🗺️ Home Lake Hotspot
                        </span>
                        <x-badge variant="teal" size="sm" fontMono>Hotspot</x-badge>
                    </div>
                    @if(isset($personalBest['lakeWithMostCatches']) && $personalBest['lakeWithMostCatches'])
                        <div class="space-y-1 pt-1">
                            <div class="text-xl font-extrabold text-slate-900 dark:text-white truncate tracking-tight">{{ $personalBest['lakeWithMostCatches']->name }}</div>
                            <div class="text-xs font-bold text-teal-700 dark:text-teal-400">Most Successful Angling Water</div>
                            <div class="pt-2 border-t border-teal-100/80 dark:border-slate-800 flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400">
                                <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" />
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

        <!-- Species Personal Best Trophy Board -->
        @if(!empty($speciesPbs) && $speciesPbs->count() > 0)
            <x-card title="Species Personal Bests (PB)" subtitle="Your longest recorded catches per species" icon="award" iconColor="amber" badge="{{ $speciesPbs->count() }} Species PB(s)" badgeVariant="amber">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($speciesPbs as $pb)
                        <a href="{{ url('/record/' . $pb->id) }}" class="group bg-slate-50 dark:bg-slate-800/60 hover:bg-teal-50/60 dark:hover:bg-teal-950/40 p-3.5 rounded-xl border border-slate-200/80 dark:border-slate-700/80 hover:border-teal-300 dark:hover:border-teal-600 transition-all flex items-center gap-3">
                            <x-fishAvatar :fish="$pb->fishBreed" size="md" />
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-teal-700 dark:group-hover:text-teal-300 transition-colors">{{ $pb->fishBreed->name ?? 'Fish' }}</span>
                                    <span class="text-xs font-black text-slate-900 dark:text-white font-mono bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 shadow-2xs">{{ number_format($pb->length, 1) }}"</span>
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5 flex items-center gap-1">
                                    <x-lucide-map-pin class="w-3 h-3 text-slate-400 shrink-0" />
                                    <span class="truncate">{{ $pb->lake->name ?? 'Waterbody' }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif

        <!-- 🎣 ANGLER PRODUCTION & GEAR TELEMETRY GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 📏 Cumulative Length Landed -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1">
                        <x-lucide-ruler class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" /> Lifetime Production
                    </span>
                    <x-badge variant="teal" size="sm" fontMono>Production</x-badge>
                </div>
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $totalFeet }}</span>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">ft. landed</span>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 pt-1 border-t border-slate-100 dark:border-slate-800">
                        <span>Total: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $totalInches }} in.</strong></span>
                        <span>•</span>
                        <span>Avg: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $avgLength > 0 ? $avgLength . ' in.' : '—' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- 🎣 MVP Go-To Lure -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1">
                        <x-lucide-fishing-hook class="w-3.5 h-3.5 text-amber-500" /> MVP Go-To Lure
                    </span>
                    <x-badge variant="amber" size="sm" fontMono>Tackle</x-badge>
                </div>
                @if($mvpLure && $mvpLure->lure)
                    <div class="space-y-1 pt-1">
                        <div class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $mvpLure->lure->name }}</div>
                        <div class="text-xs text-teal-700 dark:text-teal-400 font-bold font-mono">{{ $mvpLure->catches }} fish landed</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span>Lure PB:</span>
                            <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $mvpLure->longest ? $mvpLure->longest . ' in.' : '—' }}</strong>
                        </div>
                    </div>
                @else
                    <div class="text-xs text-slate-400 py-3 italic">No lure data logged.</div>
                @endif
            </div>

            <!-- 🌱 Conservation C&R Rate -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1">
                        <x-lucide-heart class="w-3.5 h-3.5 text-emerald-500" /> C&R Conservation
                    </span>
                    <x-badge variant="emerald" size="sm" fontMono>Conservation</x-badge>
                </div>
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $releaseRate }}%</span>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">released</span>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
                        <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $releasedCount }}</strong> of {{ $record_count }} fish safely released
                    </div>
                </div>
            </div>

            <!-- 🗓️ Seasonal Peak Month -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1">
                        <x-lucide-calendar class="w-3.5 h-3.5 text-sky-500" /> Peak Month
                    </span>
                    <x-badge variant="sky" size="sm" fontMono>Season</x-badge>
                </div>
                @if($peakMonthName)
                    <div class="space-y-1 pt-1">
                        <div class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $peakMonthName }}</div>
                        <div class="text-xs text-sky-700 dark:text-sky-400 font-medium">Highest Production Month</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
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
            <x-card title="Top Fished Waters" icon="waves" iconColor="teal" badge="{{ count($topWaters) }} Waters" badgeVariant="slate">
                @if(count($topWaters) > 0)
                    <div class="space-y-3">
                        @foreach($topWaters as $idx => $tw)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-teal-500/10 text-teal-700 dark:text-teal-300 font-mono font-bold text-xs flex items-center justify-center border border-teal-500/20">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <a href="/lake/{{ $tw->lake->id }}" class="font-bold text-slate-900 dark:text-white text-xs hover:text-teal-600 dark:hover:text-teal-400 hover:underline">
                                            {{ $tw->lake->name ?? 'Unknown Lake' }}
                                        </a>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block">Lake Record PB: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $tw->longest ? $tw->longest . ' in.' : '—' }}</strong></span>
                                    </div>
                                </div>
                                <div class="text-right font-mono">
                                    <span class="text-xs font-bold text-teal-700 dark:text-teal-400 block">{{ $tw->catches }} fish</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400 text-xs italic">
                        No waterbody data logged.
                    </div>
                @endif
            </x-card>

            <!-- 🐟 Angler Species Diversity Breakdown -->
            <x-card title="Angler Species Ratio" icon="pie-chart" iconColor="teal" badge="{{ count($speciesDistribution) }} Species" badgeVariant="slate" class="flex flex-col justify-between">
                <div>
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
                            <div class="relative w-32 h-32 rounded-full shadow-md border-4 border-white dark:border-slate-800 shrink-0" style="background: conic-gradient({{ $conicStyle }});">
                                <div class="absolute inset-3 rounded-full bg-white dark:bg-slate-900 flex flex-col items-center justify-center border border-slate-100 dark:border-slate-800 shadow-inner">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Species</span>
                                    <span class="text-lg font-black text-slate-900 dark:text-white font-mono leading-none my-0.5">{{ count($speciesDistribution) }}</span>
                                    <span class="text-[10px] text-teal-600 dark:text-teal-400 font-bold font-mono">{{ $record_count }} fish</span>
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
                                            <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $sp->fishBreed->name ?? 'Unknown' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 font-mono shrink-0">
                                            <span class="text-slate-500 dark:text-slate-400 text-[11px]">{{ $sp->count }}</span>
                                            <strong class="text-slate-900 dark:text-white w-8 text-right">{{ $pct }}%</strong>
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
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dominant Target</span>
                            <strong class="text-xs font-black text-slate-900 dark:text-white truncate block">{{ $topSpecies->fishBreed->name ?? 'None' }}</strong>
                            <span class="text-[10px] text-teal-600 dark:text-teal-400 font-bold font-mono block">{{ round(($topSpecies->count / $record_count) * 100) }}% share ({{ $topSpecies->count }} fish)</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Species Diversity</span>
                            <strong class="text-xs font-black text-slate-900 dark:text-white font-mono block">{{ count($speciesDistribution) }} Breeds Logged</strong>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono block">{{ $record_count }} total catches</span>
                        </div>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Catches Logbook Quick Access Banner Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-100 dark:border-teal-800 flex items-center justify-center shrink-0">
                    <x-lucide-list class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <span>Personal Catches Logbook</span>
                        <x-badge variant="teal" size="sm" fontMono>{{ $record_count }} Catches</x-badge>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Explore your complete catch logbook with weather telemetry, lake locations, lure history, and search filters.</p>
                </div>
            </div>

            <a href="{{ url('/record/directory') }}?angler={{ $angler->id }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-md transition-all shrink-0">
                <span>View Full Logbook</span>
                <x-lucide-arrow-right class="w-4 h-4 text-teal-400" />
            </a>
        </div>
    @else
        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 rounded-2xl p-6 text-center space-y-3 shadow-sm">
            <x-lucide-alert-triangle class="w-8 h-8 text-amber-500 mx-auto" />
            <h3 class="font-bold text-base">User Not Associated with Angler Record</h3>
            <p class="text-xs text-amber-700 dark:text-amber-300 max-w-md mx-auto">Your account is not linked to an Angler profile. Please contact your system administrator to associate your account.</p>
        </div>
    @endif
</div>
@endsection

