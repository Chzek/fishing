@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Sub-navigation Tab Switcher -->
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
        <div class="flex items-center gap-2">
            <a href="{{ url('/angler') }}" class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-slate-400"></i>
                <span>Anglers Directory</span>
            </a>
            <a href="{{ url('/angler/stats') }}" class="px-4 py-2 text-xs font-bold rounded-xl bg-teal-500/10 text-teal-700 border border-teal-500/20 flex items-center gap-2 shadow-2xs">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-teal-600"></i>
                <span>Angler Stats & Summary</span>
            </a>
        </div>

        <a href="{{ url('/angler/create') }}" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
            <span>Add Angler</span>
        </a>
    </div>

    <!-- 1. Header Hero Telemetry Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2.5">
                    <span>Angler Telemetry & Summary Stats</span>
                    <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">{{ $totalAnglers }} Registered Anglers</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium pt-0.5">Aggregate crew analytics, catch distributions, and comprehensive crew comparison metrics</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ url('/record/create') }}" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Log Catch</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Summary KPI Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Crew Catch Volume -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-1">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Total Catches</span>
                <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 flex items-center justify-center">
                    <i data-lucide="fish" class="w-4 h-4"></i>
                </div>
            </div>
            <strong class="text-2xl font-black text-slate-900 font-mono block">{{ number_format($totalRecords) }}</strong>
            <span class="text-[11px] text-slate-400 font-medium">Logged across all waters</span>
        </div>

        <!-- Overall Catch & Release Rate -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-1">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Conservation Rate</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="waves" class="w-4 h-4"></i>
                </div>
            </div>
            <strong class="text-2xl font-black text-emerald-600 font-mono block">{{ $overallReleaseRate }}%</strong>
            <span class="text-[11px] text-slate-400 font-medium">Catch and release conservation</span>
        </div>

        <!-- Avg Catches Per Angler -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-1">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Avg Catches / Angler</span>
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <strong class="text-2xl font-black text-slate-900 font-mono block">{{ $avgCatchesPerAngler }}</strong>
            <span class="text-[11px] text-slate-400 font-medium">Mean logbook output</span>
        </div>

        <!-- Avg Waters Fished -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-1">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Avg Waters Fished</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="compass" class="w-4 h-4"></i>
                </div>
            </div>
            <strong class="text-2xl font-black text-slate-900 font-mono block">{{ $avgLakesPerAngler }}</strong>
            <span class="text-[11px] text-slate-400 font-medium">Unique lakes / angler</span>
        </div>
    </div>

    <!-- 3. Telemetry Visual Breakdown & Activity Tiering -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cumulative Fish Length & Monthly Distribution (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i data-lucide="activity" class="w-4 h-4 text-teal-600"></i>
                    <span>Cumulative Telemetry & Monthly Distribution</span>
                </h2>
                <span class="text-xs font-mono font-semibold text-slate-400">{{ $totalRecords }} Catches Recorded</span>
            </div>

            <!-- Length & Weight Stats Banner -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Fish Length</span>
                    <strong class="text-lg font-black text-teal-700 font-mono block mt-0.5">{{ number_format($totalInches, 1) }} in.</strong>
                    <span class="text-[10px] text-slate-500 font-mono">({{ $totalFeet }} total ft)</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Average Catch Length</span>
                    <strong class="text-lg font-black text-slate-900 font-mono block mt-0.5">{{ $avgLengthOverall > 0 ? $avgLengthOverall . ' in.' : '—' }}</strong>
                    <span class="text-[10px] text-slate-500 font-mono">Mean across all records</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Average Catch Weight</span>
                    <strong class="text-lg font-black text-slate-900 font-mono block mt-0.5">{{ $avgWeightOverall > 0 ? $avgWeightOverall . ' lbs.' : '—' }}</strong>
                    <span class="text-[10px] text-slate-500 font-mono">Mean across weighed catches</span>
                </div>
            </div>

            <!-- Seasonal Catch Distribution Bar Graph -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 block">Monthly Logbook Volume Distribution</span>
                    <span class="text-[11px] font-mono text-slate-400">Total: {{ number_format($totalRecords) }} catches</span>
                </div>

                <div class="h-36 flex items-end gap-2 pt-6 px-3 bg-slate-50/70 rounded-xl border border-slate-200/60 pb-2">
                    @foreach($monthlyDistribution as $m)
                        @php
                            $heightPct = $maxMonthlyCount > 0 ? round(($m['count'] / $maxMonthlyCount) * 100) : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group relative">
                            <!-- Count Label -->
                            <span class="text-[10px] font-mono font-bold {{ $m['count'] > 0 ? 'text-teal-700' : 'text-slate-300' }} transition-colors">
                                {{ $m['count'] }}
                            </span>
                            
                            <div class="w-full max-w-[28px] bg-slate-200/60 rounded-t-md overflow-hidden flex flex-col justify-end" style="height: 75%;">
                                <div class="w-full {{ $m['count'] > 0 ? 'bg-gradient-to-t from-teal-600 to-teal-400 group-hover:from-teal-500 group-hover:to-teal-300 shadow-sm' : 'bg-slate-300/30' }} rounded-t-md transition-all duration-300" style="height: {{ max($heightPct, $m['count'] > 0 ? 8 : 0) }}%;"></div>
                            </div>
                            <span class="text-[10px] font-mono font-semibold {{ $m['count'] > 0 ? 'text-slate-700' : 'text-slate-400' }}">{{ $m['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Angler Activity Tiering Breakdown (1 Col) -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-4 h-4 text-teal-600"></i>
                        <span>Angler Activity Tiering</span>
                    </h2>
                    <span class="text-xs font-mono font-semibold text-slate-400">{{ $totalAnglers }} Anglers</span>
                </div>

                <p class="text-xs text-slate-500 pt-1">Distribution of crew members grouped by total logbook catch volume.</p>

                <div class="space-y-3 pt-3">
                    <!-- Light Tier -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
                                Light Anglers (1–10 catches)
                            </span>
                            <span class="font-mono font-bold text-slate-900">{{ $activityTiers['light'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-sky-400 h-full rounded-full" style="width: {{ $totalAnglers > 0 ? round(($activityTiers['light'] / $totalAnglers) * 100) : 0 }}%;"></div>
                        </div>
                    </div>

                    <!-- Moderate Tier -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                                Moderate Anglers (11–50 catches)
                            </span>
                            <span class="font-mono font-bold text-slate-900">{{ $activityTiers['moderate'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-teal-500 h-full rounded-full" style="width: {{ $totalAnglers > 0 ? round(($activityTiers['moderate'] / $totalAnglers) * 100) : 0 }}%;"></div>
                        </div>
                    </div>

                    <!-- Avid Tier -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Avid Anglers (51+ catches)
                            </span>
                            <span class="font-mono font-bold text-slate-900">{{ $activityTiers['avid'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $totalAnglers > 0 ? round(($activityTiers['avid'] / $totalAnglers) * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                Logbook engagement average: <strong class="font-mono text-slate-800">{{ $avgCatchesPerAngler }} catches</strong> per crew member.
            </div>
        </div>
    </div>

    <!-- High-Level Angler Summary Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden space-y-4 p-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-teal-600"></i>
                    <span>Angler Summary Telemetry</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">High-level aggregate statistics across all registered crew anglers with multi-sort and instant filtering.</p>
            </div>

            <span class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-3 py-1 rounded-full font-mono">
                {{ count($anglersList) }} Active Anglers
            </span>
        </div>

        @if(count($anglersList) > 0)
            <div x-data="dataTable({ defaultDensity: 'normal' })">
                <x-table.wrapper 
                    searchPlaceholder="Filter angler telemetry..." 
                    itemName="anglers"
                    :showColumnPicker="true"
                    :showDensity="true"
                >
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <x-table.th col="angler" type="text" label="Angler">Angler</x-table.th>
                                <x-table.th col="catches" type="number" align="center" label="Catches">Catches</x-table.th>
                                <x-table.th col="lakes" type="number" align="center" label="Lakes Fished">Lakes Fished</x-table.th>
                                <x-table.th col="expeditions" type="number" align="center" label="Expeditions">Expeditions</x-table.th>
                                <x-table.th col="avg_length" type="number" align="center" label="Avg Length">Avg. Length</x-table.th>
                                <x-table.th col="avg_weight" type="number" align="center" label="Avg Weight">Avg. Weight</x-table.th>
                                <x-table.th col="release_rate" type="number" align="center" label="C&R Rate">C&R Release %</x-table.th>
                                <x-table.th col="target_species" type="text" label="Target Species">Target Species</x-table.th>
                                <th scope="col" class="py-3 px-4 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                            @foreach($anglersList as $ang)
                                <tr data-table-row class="hover:bg-slate-50/70 transition-colors">
                                    <td data-col="angler" data-sort-val="{{ $ang->firstName }} {{ $ang->lastName }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-medium text-slate-900">
                                        <div class="flex items-center gap-3">
                                            <x-anglerAvatar :angler="$ang" size="sm" />
                                            <div>
                                                <span class="font-bold text-slate-900 block text-xs leading-tight">{{ $ang->firstName }} {{ $ang->lastName }}</span>
                                                <span class="text-[10px] text-slate-400 block font-mono">Angler #{{ $ang->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-col="catches" data-sort-val="{{ $ang->records_count }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-teal-700 text-xs">{{ number_format($ang->records_count) }}</td>
                                    <td data-col="lakes" data-sort-val="{{ $ang->unique_lakes_count }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-800 text-xs">{{ $ang->unique_lakes_count }}</td>
                                    <td data-col="expeditions" data-sort-val="{{ $ang->expeditions_count }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-800 text-xs">{{ $ang->expeditions_count }}</td>
                                    <td data-col="avg_length" data-sort-val="{{ $ang->avg_length ?? 0 }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono text-slate-700 text-xs">{{ $ang->avg_length ? $ang->avg_length . ' in.' : '—' }}</td>
                                    <td data-col="avg_weight" data-sort-val="{{ $ang->avg_weight ?? 0 }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono text-slate-700 text-xs">{{ $ang->avg_weight ? $ang->avg_weight . ' lbs.' : '—' }}</td>
                                    <td data-col="release_rate" data-sort-val="{{ $ang->release_rate }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-emerald-600 text-xs">{{ $ang->release_rate }}%</td>
                                    <td data-col="target_species" data-sort-val="{{ $ang->top_species_name }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-semibold text-slate-800 text-xs">
                                        <span class="bg-slate-100 text-slate-800 px-2 py-0.5 rounded-md border border-slate-200">{{ $ang->top_species_name }}</span>
                                    </td>
                                    <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right whitespace-nowrap">
                                        <a href="{{ url('/angler/' . $ang->id . '/profile') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-teal-50 text-slate-700 hover:text-teal-700 font-semibold text-xs rounded-xl border border-slate-200 hover:border-teal-200 transition-colors">
                                            <i data-lucide="user" class="w-3.5 h-3.5 text-teal-600"></i>
                                            <span>Profile</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 text-xs font-semibold text-slate-600 border-t border-slate-200">
                            <tr>
                                <td class="py-2.5 px-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">
                                    Live Summary
                                </td>
                                <td data-col="catches" class="py-2.5 px-4 text-center font-mono font-bold text-teal-700">
                                    Total: <span data-aggregate-for="catches" data-aggregate-type="sum">—</span>
                                </td>
                                <td colspan="2"></td>
                                <td data-col="avg_length" class="py-2.5 px-4 text-center font-mono font-bold text-slate-800">
                                    Avg: <span data-aggregate-for="avg_length" data-aggregate-type="avg">—</span>″
                                </td>
                                <td data-col="avg_weight" class="py-2.5 px-4 text-center font-mono font-bold text-slate-800">
                                    Avg: <span data-aggregate-for="avg_weight" data-aggregate-type="avg">—</span> lbs
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </x-table.wrapper>
            </div>
        @else
            <div class="text-center py-12 px-4 space-y-3">
                <i data-lucide="users" class="w-8 h-8 text-slate-400 mx-auto"></i>
                <p class="text-xs text-slate-500">No angler data logged yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
