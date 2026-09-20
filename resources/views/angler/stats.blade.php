@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- 1. Header Hero Telemetry Banner -->
    <x-pageHero 
        title="Angler Telemetry & Summary Stats"
        subtitle="Aggregate crew analytics, catch distributions, and comprehensive crew comparison metrics"
        icon="bar-chart-3"
        iconColor="teal"
        badge="{{ $totalAnglers }} Registered Anglers"
        badgeVariant="teal">
        <x-slot:actions>
            <a href="{{ url('/record/quick') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer">
                <x-lucide-zap class="w-4 h-4 text-teal-400" />
                <span>Log Catch</span>
            </a>
            <a href="{{ url('/angler/create') }}" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-user-plus class="w-4 h-4" />
                <span>Register Angler</span>
            </a>
        </x-slot:actions>
    </x-pageHero>

    <!-- 2. Sub-navigation Tab Switcher -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <a href="{{ url('/angler') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-xl text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
                <x-lucide-users class="w-4 h-4 text-slate-400" />
                <span>Anglers Directory</span>
            </a>
            <a href="{{ url('/angler/stats') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 flex items-center gap-2 shadow-2xs">
                <x-lucide-bar-chart-3 class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                <span>Angler Stats & Summary</span>
            </a>
        </div>
    </div>

    <!-- 3. Summary KPI Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <x-kpiMetric label="Total Catches" :value="$totalRecords" icon="fish" color="teal" subtext="Logged across all waters" />
        <x-kpiMetric label="Conservation Rate" :value="$overallReleaseRate . '%'" icon="waves" color="emerald" subtext="Catch and release conservation" />
        <x-kpiMetric label="Avg Catches / Angler" :value="$avgCatchesPerAngler" icon="trending-up" color="sky" subtext="Mean logbook output" />
        <x-kpiMetric label="Avg Waters Fished" :value="$avgLakesPerAngler" icon="compass" color="indigo" subtext="Unique lakes / angler" />
    </div>

    <!-- 4. Telemetry Visual Breakdown & Activity Tiering -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cumulative Fish Length & Monthly Distribution (2 Cols) -->
        <x-card class="lg:col-span-2 space-y-5" title="Cumulative Telemetry & Monthly Distribution" icon="activity" iconColor="teal" badge="{{ $totalRecords }} Catches Recorded" badgeVariant="slate">
            <!-- Length & Weight Stats Banner -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Fish Length</span>
                    <strong class="text-lg font-black text-teal-700 dark:text-teal-400 font-mono block mt-0.5">{{ number_format($totalInches, 1) }} in.</strong>
                    <span class="text-[10px] text-slate-500 font-mono">({{ $totalFeet }} total ft)</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Average Catch Length</span>
                    <strong class="text-lg font-black text-slate-900 dark:text-white font-mono block mt-0.5">{{ $avgLengthOverall > 0 ? $avgLengthOverall . ' in.' : '—' }}</strong>
                    <span class="text-[10px] text-slate-500 font-mono">Mean across all records</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Average Catch Weight</span>
                    <strong class="text-lg font-black text-slate-900 dark:text-white font-mono block mt-0.5">{{ $avgWeightOverall > 0 ? $avgWeightOverall . ' lbs.' : '—' }}</strong>
                    <span class="text-[10px] text-slate-500 font-mono">Mean across weighed catches</span>
                </div>
            </div>

            <!-- Seasonal Catch Distribution Bar Graph -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Monthly Logbook Volume Distribution</span>
                    <span class="text-[11px] font-mono text-slate-400">Total: {{ number_format($totalRecords) }} catches</span>
                </div>

                <div class="h-36 flex items-end gap-2 pt-6 px-3 bg-slate-50/70 dark:bg-slate-800/50 rounded-xl border border-slate-200/60 dark:border-slate-700/60 pb-2">
                    @foreach($monthlyDistribution as $m)
                        @php
                            $heightPct = $maxMonthlyCount > 0 ? round(($m['count'] / $maxMonthlyCount) * 100) : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group relative">
                            <!-- Count Label -->
                            <span class="text-[10px] font-mono font-bold {{ $m['count'] > 0 ? 'text-teal-700 dark:text-teal-300' : 'text-slate-300 dark:text-slate-600' }} transition-colors">
                                {{ $m['count'] }}
                            </span>
                            
                            <div class="w-full max-w-[28px] bg-slate-200/60 dark:bg-slate-700/60 rounded-t-md overflow-hidden flex flex-col justify-end" style="height: 75%;">
                                <div class="w-full {{ $m['count'] > 0 ? 'bg-gradient-to-t from-teal-600 to-teal-400 group-hover:from-teal-500 group-hover:to-teal-300 shadow-sm' : 'bg-slate-300/30 dark:bg-slate-600/30' }} rounded-t-md transition-all duration-300" style="height: {{ max($heightPct, $m['count'] > 0 ? 8 : 0) }}%;"></div>
                            </div>
                            <span class="text-[10px] font-mono font-semibold {{ $m['count'] > 0 ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400 dark:text-slate-500' }}">{{ $m['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-card>

        <!-- Angler Activity Tiering Breakdown (1 Col) -->
        <x-card class="flex flex-col justify-between" title="Angler Activity Tiering" icon="pie-chart" iconColor="teal" badge="{{ $totalAnglers }} Anglers" badgeVariant="slate">
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Distribution of crew members grouped by total logbook catch volume.</p>

                <div class="space-y-3 pt-3">
                    <!-- Light Tier -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
                                Light Anglers (1–10 catches)
                            </span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $activityTiers['light'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-sky-400 h-full rounded-full" style="width: {{ $totalAnglers > 0 ? round(($activityTiers['light'] / $totalAnglers) * 100) : 0 }}%;"></div>
                        </div>
                    </div>

                    <!-- Moderate Tier -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                                Moderate Anglers (11–50 catches)
                            </span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $activityTiers['moderate'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-teal-500 h-full rounded-full" style="width: {{ $totalAnglers > 0 ? round(($activityTiers['moderate'] / $totalAnglers) * 100) : 0 }}%;"></div>
                        </div>
                    </div>

                    <!-- Avid Tier -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Avid Anglers (51+ catches)
                            </span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $activityTiers['avid'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $totalAnglers > 0 ? round(($activityTiers['avid'] / $totalAnglers) * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                Logbook engagement average: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $avgCatchesPerAngler }} catches</strong> per crew member.
            </x-slot:footer>
        </x-card>
    </div>

    <!-- 5. High-Level Angler Summary Table -->
    <x-card title="Angler Summary Telemetry" subtitle="High-level aggregate statistics across all registered crew anglers with multi-sort and instant filtering." icon="users" iconColor="teal" badge="{{ $anglersList->total() }} Active Anglers" badgeVariant="teal">
        @if($anglersList->total() > 0)
            @livewire('components.generic-data-table', [
                'modelClass' => \Fishinglog\Models\Angler::class,
                'columns' => [
                    ['key' => 'lastName', 'label' => 'Angler', 'type' => 'angler_name', 'sortable' => true, 'searchable' => true],
                    ['key' => 'records_count', 'label' => 'Catches', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'records_count'],
                    ['key' => 'lakes_count', 'label' => 'Lakes Fished', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'lakes_count'],
                ],
                'searchPlaceholder' => 'Filter angler telemetry...',
                'itemName' => 'anglers',
                'perPage' => 15,
                'defaultSortBy' => 'records_count',
                'defaultSortOrder' => 'desc',
            ])
        @else
            <div class="text-center py-12 px-4 space-y-3">
                <x-lucide-users class="w-8 h-8 text-slate-400 mx-auto" />
                <p class="text-xs text-slate-500">No angler data logged yet.</p>
            </div>
        @endif
    </x-card>
</div>
@endsection
