@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Status Alerts -->
    <x-statusAlert />

    <!-- 1. Unified Species Hero Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="text-center md:text-left">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-1.5">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2.5 py-0.5 rounded-lg">
                    Species Intelligence Dossier
                </span>
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700 px-2.5 py-0.5 rounded-lg">
                    {{ $fish->family?->name ? $fish->family->name . ' Family' : 'Freshwater Taxonomy' }}
                </span>
                <span class="text-[11px] font-mono text-slate-400">ID: {{ substr($fish->id, 0, 8) }}</span>
                @if($trophyThreshold)
                    <span class="text-[11px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2.5 py-0.5 rounded-lg">
                        Trophy Benchmark: {{ $trophyThreshold }}"+
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center justify-center md:justify-start gap-2">
                <span>{{ $fish->name }}</span>
                <a href="/fish/breed/{{ $fish->id }}/edit" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Edit Species">
                    <x-lucide-edit-3 class="w-4 h-4" />
                </a>
            </h1>

            <p class="text-xs font-medium text-teal-400 mt-1 flex items-center justify-center md:justify-start gap-1.5">
                <x-lucide-activity class="w-3.5 h-3.5 text-teal-400" />
                <span>{{ $fish->family?->name ? $fish->family->name . ' Biological Family • ' : '' }}Ontario Fishery Telemetry & Angler Intelligence</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-2.5 shrink-0">
            <a href="/fish" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <x-lucide-arrow-left class="w-3.5 h-3.5 text-slate-400" />
                <span>Field Guide</span>
            </a>
            <a href="/fish/breed/{{ $fish->id }}/edit" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <x-lucide-edit-3 class="w-3.5 h-3.5 text-teal-400" />
                <span>Edit Specs</span>
            </a>
            <a href="{{ url('/record/quick?fish_breed_id=' . $fish->id) }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-teal-950/40 transition-all cursor-pointer active:scale-95">
                <x-lucide-zap class="w-4 h-4 text-teal-200" />
                <span>Quick Catch</span>
            </a>
        </div>
    </div>

    <!-- 2. Species Overview Row: Image Card + 2x2 Telemetry KPI Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        <!-- Left Column: Fish Illustration Card -->
        <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center justify-center min-h-[220px]">
            @if($fish->imageUrl)
                <img src="{{ $fish->imageUrl }}" alt="{{ $fish->name }}" class="max-h-56 w-auto max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition-transform duration-300">
            @elseif($fish->avatarUrl)
                <img src="{{ $fish->avatarUrl }}" alt="{{ $fish->name }}" class="max-h-56 w-auto max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition-transform duration-300">
            @else
                <div class="flex flex-col items-center justify-center text-slate-300 dark:text-slate-600 py-8">
                    <x-lucide-fish class="w-20 h-20 stroke-[1.25]" />
                    <span class="text-xs font-medium text-slate-400 dark:text-slate-500 mt-2">No illustration available</span>
                </div>
            @endif
        </div>

        <!-- Right Column: 2x2 Key Telemetry KPI Metrics Grid -->
        <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-5">
            <!-- Card 1: Total Logged -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Total Logged</span>
                    <span class="text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight mt-1 block">{{ number_format($count) }}</span>
                    <span class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold mt-1 inline-flex items-center gap-1">
                        <x-lucide-map-pin class="w-3 h-3" /> Across {{ count($lakes) }} Unique Waters
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-100 dark:border-teal-800/60 flex items-center justify-center shrink-0">
                    <x-lucide-fish class="w-6 h-6" />
                </div>
            </div>

            <!-- Card 2: Record Length -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Record Length</span>
                    <span class="text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight mt-1 block">{{ $longest ? number_format($longest, 1) . '"' : '—' }}</span>
                    <span class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1 inline-flex items-center gap-1 truncate max-w-[150px]" title="{{ $recordTrophy?->angler?->fullName ? 'By ' . $recordTrophy->angler->fullName : 'No records' }}">
                        <x-lucide-trophy class="w-3 h-3 shrink-0" /> <span class="truncate">{{ $recordTrophy?->angler?->fullName ? 'By ' . $recordTrophy->angler->fullName : 'No records' }}</span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800/60 flex items-center justify-center shrink-0">
                    <x-lucide-ruler class="w-6 h-6" />
                </div>
            </div>

            <!-- Card 3: Record Weight -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Record Weight</span>
                    <span class="text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight mt-1 block">{{ $fattest ? number_format($fattest, 1) . ' lbs' : '—' }}</span>
                    <span class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1 inline-flex items-center gap-1 truncate max-w-[150px]" title="{{ $heaviestTrophy?->angler?->fullName ? 'By ' . $heaviestTrophy->angler->fullName : 'No data' }}">
                        <x-lucide-award class="w-3 h-3 shrink-0" /> <span class="truncate">{{ $heaviestTrophy?->angler?->fullName ? 'By ' . $heaviestTrophy->angler->fullName : 'No data' }}</span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800/60 flex items-center justify-center shrink-0">
                    <x-lucide-scale class="w-6 h-6" />
                </div>
            </div>

            <!-- Card 4: C&R Conservation -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">C&R Conservation</span>
                    <span class="text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight mt-1 block">{{ $speciesReleaseRate }}%</span>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 inline-flex items-center gap-1">
                        <x-lucide-shield-check class="w-3 h-3" /> {{ number_format($releasedCount) }} Released Safe
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/60 flex items-center justify-center shrink-0">
                    <x-lucide-waves class="w-6 h-6" />
                </div>
            </div>
        </div>
    </div>

    <!-- 2. 🏆 Trophy & Master Angler Hall of Fame Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <x-lucide-trophy class="w-5 h-5 text-amber-500" />
                    <span>Trophy Records & Benchmark Hall of Fame</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">All-time species records, benchmark qualifications, and historical trophy catches</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80 text-xs font-bold rounded-xl flex items-center gap-1.5">
                    <x-lucide-award class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                    <span>Ontario Master Angler: {{ $trophyThreshold }} in.</span>
                </span>
                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-slate-700">
                    {{ $trophyCatchesCount }} Trophies
                </span>
            </div>
        </div>

        <!-- Trophy Highlights Grid (Record Length & Record Weight Full Dossier) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- All-Time Length Champion Card -->
            <div class="p-4 rounded-xl bg-gradient-to-br from-amber-50/60 dark:from-amber-950/40 to-slate-50 dark:to-slate-800/60 border border-amber-200/80 dark:border-amber-800/60 flex items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-amber-500 text-white px-2 py-0.5 rounded-md shadow-2xs">
                            Length Champion
                        </span>
                        <span class="text-xs font-mono font-bold text-amber-900 dark:text-amber-300">{{ $longest ? number_format($longest, 1) . ' inches' : '—' }}</span>
                    </div>

                    @if($recordTrophy && $recordTrophy->angler)
                        <div class="flex items-center gap-2.5 pt-1">
                            <x-anglerAvatar :angler="$recordTrophy->angler" size="sm" />
                            <div>
                                <a href="/angler/{{ $recordTrophy->angler->id }}/profile" class="font-bold text-slate-900 dark:text-white hover:text-teal-600 dark:hover:text-teal-400 text-xs block">
                                    {{ $recordTrophy->angler->fullName }}
                                </a>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ $recordTrophy->lake?->name ?? 'Unknown Water' }} &bull; {{ $recordTrophy->caught ? \Illuminate\Support\Carbon::parse($recordTrophy->caught)->format('M d, Y') : 'Historical' }}
                                </span>
                            </div>
                        </div>
                        @if($recordTrophy->lure)
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 pt-1 flex items-center gap-1.5">
                                <x-lucide-target class="w-3 h-3 text-teal-600 dark:text-teal-400" />
                                <span>Lure: <strong class="text-slate-800 dark:text-slate-200">{{ $recordTrophy->lure->name }}</strong> ({{ $recordTrophy->lure->color ?? 'Standard' }})</span>
                            </div>
                        @endif
                    @else
                        <p class="text-xs text-slate-400 dark:text-slate-500 italic pt-1">No length trophy record recorded yet.</p>
                    @endif
                </div>

                <div class="w-12 h-12 rounded-xl bg-amber-500/10 dark:bg-amber-950/60 border border-amber-500/20 dark:border-amber-800/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-inner">
                    <x-lucide-ruler class="w-6 h-6" />
                </div>
            </div>

            <!-- All-Time Weight Champion Card -->
            <div class="p-4 rounded-xl bg-gradient-to-br from-amber-50/60 dark:from-amber-950/40 to-slate-50 dark:to-slate-800/60 border border-amber-200/80 dark:border-amber-800/60 flex items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-amber-600 text-white px-2 py-0.5 rounded-md shadow-2xs">
                            Weight Champion
                        </span>
                        <span class="text-xs font-mono font-bold text-amber-900 dark:text-amber-300">{{ $fattest ? number_format($fattest, 1) . ' lbs.' : '—' }}</span>
                    </div>

                    @if($heaviestTrophy && $heaviestTrophy->angler)
                        <div class="flex items-center gap-2.5 pt-1">
                            <x-anglerAvatar :angler="$heaviestTrophy->angler" size="sm" />
                            <div>
                                <a href="/angler/{{ $heaviestTrophy->angler->id }}/profile" class="font-bold text-slate-900 dark:text-white hover:text-teal-600 dark:hover:text-teal-400 text-xs block">
                                    {{ $heaviestTrophy->angler->fullName }}
                                </a>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ $heaviestTrophy->lake?->name ?? 'Unknown Water' }} &bull; {{ $heaviestTrophy->caught ? \Illuminate\Support\Carbon::parse($heaviestTrophy->caught)->format('M d, Y') : 'Historical' }}
                                </span>
                            </div>
                        </div>
                        @if($heaviestTrophy->lure)
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 pt-1 flex items-center gap-1.5">
                                <x-lucide-target class="w-3 h-3 text-teal-600 dark:text-teal-400" />
                                <span>Lure: <strong class="text-slate-800 dark:text-slate-200">{{ $heaviestTrophy->lure->name }}</strong> ({{ $heaviestTrophy->lure->color ?? 'Standard' }})</span>
                            </div>
                        @endif
                    @else
                        <p class="text-xs text-slate-400 dark:text-slate-500 italic pt-1">No weight trophy record recorded yet.</p>
                    @endif
                </div>

                <div class="w-12 h-12 rounded-xl bg-amber-500/10 dark:bg-amber-950/60 border border-amber-500/20 dark:border-amber-800/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-inner">
                    <x-lucide-scale class="w-6 h-6" />
                </div>
            </div>
        </div>

        <!-- Top 5 All-Time Trophy Roster Strip -->
        @if($topTrophies->count() > 0)
            <div class="space-y-2 pt-2">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block uppercase tracking-wider">Top 5 All-Time Specimens</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    @foreach($topTrophies as $rank => $trophy)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/70 hover:border-amber-300 dark:hover:border-amber-500 transition-all space-y-2 relative group">
                            <div class="flex items-center justify-between">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black font-mono {{ $rank == 0 ? 'bg-amber-400 text-slate-900 shadow-2xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                    #{{ $rank + 1 }}
                                </span>
                                <strong class="text-sm font-black text-amber-700 dark:text-amber-400 font-mono">{{ number_format($trophy->length, 1) }}"</strong>
                            </div>

                            <div>
                                <span class="font-bold text-slate-900 dark:text-white text-xs block truncate">{{ $trophy->angler?->fullName ?? 'Unknown' }}</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block truncate">{{ $trophy->lake?->name ?? 'Waterbody' }}</span>
                            </div>

                            <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 font-mono border-t border-slate-200/50 dark:border-slate-700/50 pt-1.5">
                                <span>{{ $trophy->weight ? number_format($trophy->weight, 1) . ' lbs' : 'Length-only' }}</span>
                                <span>{{ $trophy->caught ? \Illuminate\Support\Carbon::parse($trophy->caught)->format('M Y') : '—' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- 3. Tactical 4-Quadrant Intelligence Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Quadrant 1: 🎯 Tackle & Lure Intelligence -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <x-lucide-target class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                        <span>Productive Tackle & Lures</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Top lure categories, models, and colorways landed for {{ $fish->name }}</p>
                </div>
                <span class="text-xs font-mono font-bold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800/60 px-2.5 py-0.5 rounded-full">
                    {{ $topLures->count() }} Models
                </span>
            </div>

            <!-- Lure Categories Breakdown -->
            @if($topLureCategories->count() > 0)
                <div class="space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Top Category Distribution</span>
                    <div class="space-y-2">
                        @foreach($topLureCategories as $cat)
                            @php
                                $catPct = $count > 0 ? round(($cat->count / $count) * 100) : 0;
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 capitalize">{{ $cat->category }}</span>
                                    <span class="font-mono text-slate-600 dark:text-slate-400 text-[11px] font-semibold">
                                        {{ $cat->count }} catches <span class="text-slate-400 dark:text-slate-500 font-normal">({{ $catPct }}%)</span> &bull; Max: <strong class="text-teal-700 dark:text-teal-400">{{ number_format($cat->max_length, 1) }}"</strong>
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-teal-500 h-full rounded-full transition-all" style="width: {{ $catPct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Top Lure Models -->
            @if($topLures->count() > 0)
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Top Lure Models & Personal Bests</span>
                    <div class="space-y-2">
                        @foreach($topLures as $tl)
                            @php
                                $pct = $count > 0 ? round(($tl->catches_count / $count) * 100) : 0;
                                $lureName = $tl->lure?->name ?? 'Lure #' . $tl->lures_id;
                                $lureColor = $tl->lure?->color;
                            @endphp
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <a href="/lure/{{ $tl->lures_id }}" class="font-bold text-slate-900 dark:text-white hover:text-teal-600 dark:hover:text-teal-400 truncate block">
                                        {{ $lureName }}
                                    </a>
                                    @if($lureColor)
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate block">{{ $lureColor }}</span>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $tl->catches_count }} catches</span>
                                    @if($tl->max_length)
                                        <span class="text-[10px] text-teal-700 dark:text-teal-400 font-bold block font-mono">Max: {{ number_format($tl->max_length, 1) }}"</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <x-emptyState icon="disc" title="No Lure Telemetry" description="No lure entries logged for this species yet." />
            @endif

            <!-- Top Producing Colors Chips -->
            @if($topLureColors->count() > 0)
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Top Producing Color Patterns</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($topLureColors as $c)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 transition-colors">
                                <span>{{ $c->color }}</span>
                                <span class="font-mono text-[10px] text-teal-700 dark:text-teal-400 font-bold">({{ $c->count }})</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Quadrant 2: 🌊 Waterbody Power Rankings & Lake Records -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <x-lucide-waves class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                        <span>Waterbody Hotspot Rankings</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Top ranked waters by catch density and lake record sizes</p>
                </div>
                <span class="text-xs font-mono font-bold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800/60 px-2.5 py-0.5 rounded-full">
                    {{ count($lakes) }} Waters
                </span>
            </div>

            @if($topHotspots->count() > 0)
                <div class="space-y-3">
                    @foreach($topHotspots as $idx => $lake)
                        @php
                            $lakePct = $count > 0 ? round(($lake->count / $count) * 100) : 0;
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black font-mono shrink-0 {{ $idx == 0 ? 'bg-teal-500 text-white shadow-2xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                        {{ $idx + 1 }}
                                    </span>
                                    <a href="/lake/{{ $lake->lakes_id }}" class="font-bold text-slate-900 dark:text-white hover:text-teal-600 dark:hover:text-teal-400 text-xs truncate">
                                        {{ $lake->lake?->name ?? 'Lake #' . $lake->lakes_id }}
                                    </a>
                                </div>
                                <span class="font-mono font-bold text-slate-900 dark:text-white text-xs shrink-0">
                                    {{ $lake->count }} catches <span class="text-slate-400 dark:text-slate-500 font-normal">({{ $lakePct }}%)</span>
                                </span>
                            </div>

                            <!-- Lake Metrics Bar -->
                            <div class="grid grid-cols-3 gap-2 pt-1 text-[11px] border-t border-slate-200/50 dark:border-slate-700/50">
                                <div>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 block uppercase">Lake Record</span>
                                    <strong class="font-mono text-amber-700 dark:text-amber-400 font-bold">{{ $lake->max_length ? number_format($lake->max_length, 1) . '"' : '—' }}</strong>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 block uppercase">Mean Size</span>
                                    <strong class="font-mono text-slate-800 dark:text-slate-200 font-bold">{{ $lake->avg_length ? number_format($lake->avg_length, 1) . '"' : '—' }}</strong>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 block uppercase">Outings</span>
                                    <strong class="font-mono text-slate-800 dark:text-slate-200 font-bold">{{ $lake->visits }} visits</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-emptyState icon="map-pin-off" title="No Waterbody Data" description="No lake locations logged for this species yet." />
            @endif
        </div>

        <!-- Quadrant 3: ⛅ Seasonal, Thermal & Weather Triggers -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <x-lucide-thermometer-sun class="w-4 h-4 text-sky-600 dark:text-sky-400" />
                        <span>Seasonal & Weather Triggers</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Atmospheric conditions, water temperature ranges, and peak activity</p>
                </div>
                @if($peakMonth)
                    <span class="text-xs font-mono font-bold text-sky-800 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800/80 px-2.5 py-0.5 rounded-full">
                        Peak: {{ $peakMonth }}
                    </span>
                @endif
            </div>

            <!-- Thermal Water Temp Telemetry Badge -->
            @if(isset($weatherTelemetry) && $weatherTelemetry->avg_temp)
                <div class="bg-sky-50 dark:bg-sky-950/50 border border-sky-100 dark:border-sky-800/80 rounded-xl p-3 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sky-900 dark:text-sky-200">
                        <x-lucide-thermometer class="w-4 h-4 text-sky-600 dark:text-sky-400" />
                        <div>
                            <span class="text-xs font-bold block">Productive Water Temperature Range</span>
                            <span class="text-[11px] text-sky-700 dark:text-sky-300">Average: {{ round($weatherTelemetry->avg_temp) }}°F</span>
                        </div>
                    </div>
                    <span class="font-mono font-bold text-sky-900 dark:text-sky-200 text-sm">
                        {{ round($weatherTelemetry->min_temp) }}° &mdash; {{ round($weatherTelemetry->max_temp) }}°F
                    </span>
                </div>
            @endif

            <!-- Monthly Activity Distribution Graph -->
            <div class="space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Monthly Logbook Volume Curve</span>
                <div class="grid grid-cols-8 gap-1.5 items-end h-24 pt-1">
                    @foreach($monthlyStats as $ms)
                        <div class="flex flex-col items-center gap-1 h-full justify-end">
                            <span class="text-[10px] font-bold font-mono {{ $ms['count'] > 0 ? 'text-teal-700 dark:text-teal-400' : 'text-slate-300 dark:text-slate-600' }}">
                                {{ $ms['count'] }}
                            </span>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-t-md flex items-end h-14">
                                <div 
                                    class="w-full rounded-t-md transition-all {{ $ms['count'] > 0 ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}" 
                                    style="height: {{ max($ms['percentage'], 6) }}%"
                                ></div>
                            </div>
                            <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-tighter">
                                {{ $ms['month'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Weather Conditions Breakdown -->
            @php
                $activeWeatherStats = collect($weatherStats ?? [])->filter(fn($ws) => $ws['count'] > 0);
            @endphp
            @if($activeWeatherStats->count() > 0)
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                            <x-lucide-cloud-lightning class="w-3.5 h-3.5 text-indigo-500 dark:text-indigo-400" />
                            <span>Productive Sky & Atmospheric Conditions</span>
                        </span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono font-semibold">{{ $activeWeatherStats->count() }} Conditions</span>
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
                                <span class="text-[10px] font-bold font-mono text-indigo-600 dark:text-indigo-400">
                                    {{ $ws['count'] }}
                                </span>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-t-md flex items-end h-14">
                                    <div 
                                        class="w-full rounded-t-md transition-all bg-indigo-500 group-hover:bg-indigo-400 shadow-2xs" 
                                        style="height: {{ max($ws['percentage'], 10) }}%"
                                    ></div>
                                </div>
                                <span class="text-[8px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-tighter truncate w-full text-center">
                                    {{ $ws['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Quadrant 4: 👑 Species Angler Hall of Fame & Leaderboard -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <x-lucide-crown class="w-4 h-4 text-amber-500" />
                        <span>Species Angler Hall of Fame</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Top crew members and master anglers for {{ $fish->name }}</p>
                </div>
                <span class="text-xs font-mono font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 px-2.5 py-0.5 rounded-full">
                    Top Anglers
                </span>
            </div>

            <!-- Top Angler Spotlight Crown Card -->
            @if($topAngler && $topAngler->angler)
                <div class="p-4 rounded-xl bg-gradient-to-br from-amber-50/70 dark:from-amber-950/40 via-slate-50 dark:via-slate-800/60 to-teal-50/50 dark:to-teal-950/30 border border-amber-200/80 dark:border-amber-800/60 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <x-anglerAvatar :angler="$topAngler->angler" size="md" />
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-400 text-slate-950 rounded-full flex items-center justify-center text-[10px] font-black shadow-xs">
                                👑
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-400 block">Top Species Angler</span>
                            <a href="/angler/{{ $topAngler->angler->id }}/profile" class="font-extrabold text-slate-900 dark:text-white hover:text-teal-600 dark:hover:text-teal-400 text-sm block">
                                {{ $topAngler->angler->fullName }}
                            </a>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $topAngler->catches_count }} catches &bull; <strong class="text-amber-800 dark:text-amber-400">{{ $topAnglerShare }}%</strong> of all logged catches
                            </span>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        @if($topAngler->longest_catch)
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase block font-medium">Personal Best</span>
                            <strong class="text-base font-black text-amber-700 dark:text-amber-400 font-mono block">{{ number_format($topAngler->longest_catch, 1) }}"</strong>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Top 5 Anglers Leaderboard List -->
            @if($topAnglers->count() > 0)
                <div class="space-y-2 pt-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Angler Crew Leaderboard</span>
                    <div class="space-y-2">
                        @foreach($topAnglers as $index => $ta)
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black font-mono shrink-0 {{ $index == 0 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-400 border border-amber-300 dark:border-amber-700' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                        {{ $index + 1 }}
                                    </span>
                                    @if($ta->angler)
                                        <x-anglerAvatar :angler="$ta->angler" size="sm" />
                                        <a href="/angler/{{ $ta->angler->id }}/profile" class="font-bold text-slate-900 dark:text-white hover:text-teal-600 dark:hover:text-teal-400 truncate">
                                            {{ $ta->angler->fullName }}
                                        </a>
                                    @else
                                        <span class="font-semibold text-slate-600 dark:text-slate-400">Unknown</span>
                                    @endif
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="font-bold font-mono text-teal-700 dark:text-teal-400">{{ $ta->catches_count }} catches</span>
                                    @if($ta->longest_catch)
                                        <span class="text-[10px] text-amber-700 dark:text-amber-400 font-bold block font-mono">PB: {{ number_format($ta->longest_catch, 1) }}"</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <x-emptyState icon="user-x" title="No Anglers Logged" description="Be the first angler to record a catch for this species!" />
            @endif
        </div>
    </div>

    <!-- 4. Lake Distribution & Waters Breakdown Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <h2 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                    <x-lucide-map-pin class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                    <span>Complete Waterbody Directory for {{ $fish->name }}</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Explore all documented lakes, total catches, and angler outing frequency</p>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ count($lakes) }} Waterbody Locations</span>
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

    <!-- 5. Catches Logbook Directory Callout Banner -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-500/10 dark:bg-teal-950/60 border border-teal-500/20 dark:border-teal-800/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                <x-lucide-book-open class="w-6 h-6" />
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <span>Catches Logbook Directory</span>
                    <span class="bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-400 font-mono text-xs font-bold px-2.5 py-0.5 rounded-full border border-teal-200 dark:border-teal-800/60">
                        {{ number_format($count) }} Records
                    </span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Explore, search, and filter the complete catches directory with weather telemetry, lake locations, and lure history for {{ $fish->name }}.
                </p>
            </div>
        </div>

        <div class="shrink-0 w-full md:w-auto">
            <a href="{{ url('/record/directory?species=' . $fish->id) }}" class="w-full md:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center justify-center gap-2 cursor-pointer">
                <span>Open Logbook Directory</span>
                <x-lucide-arrow-right class="w-4 h-4 text-teal-400" />
            </a>
        </div>
    </div>
</div>
@endsection
