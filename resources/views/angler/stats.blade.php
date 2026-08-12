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

    <!-- Hero Angler Telemetry Header Card -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Angler Telemetry & Summary Stats</h1>
                <p class="text-xs font-medium text-teal-400 mt-1 flex items-center gap-1.5">
                    <i data-lucide="activity" class="w-3.5 h-3.5 text-teal-400"></i>
                    <span>High-Level Angler Metrics & Aggregate Logbook Analytics</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 font-mono text-xs text-slate-400 shrink-0">
            <span class="bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700">Total Catches: <strong class="text-white">{{ number_format($totalRecords) }}</strong></span>
        </div>
    </div>

    <!-- High-Level Summary Telemetry KPI Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Registered Anglers -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Total Anglers</span>
                <span class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 block">{{ $totalAnglers }}</span>
                <span class="text-[11px] text-teal-600 font-semibold mt-1 inline-flex items-center gap-1">
                    <i data-lucide="users" class="w-3 h-3"></i> Active Crew Members
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Avg Catches Per Angler -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Avg. Catches / Angler</span>
                <span class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 block">{{ $avgCatchesPerAngler }}</span>
                <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> Mean Logbook Output
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                <i data-lucide="fish" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Avg Waters Fished -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Avg. Waters Fished</span>
                <span class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 block">{{ $avgLakesPerAngler }}</span>
                <span class="text-[11px] text-sky-600 font-semibold mt-1 inline-flex items-center gap-1">
                    <i data-lucide="compass" class="w-3 h-3"></i> Unique Lakes / Angler
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                <i data-lucide="waves" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Overall Conservation Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Conservation Rate</span>
                <span class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 block">{{ $overallReleaseRate }}%</span>
                <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-flex items-center gap-1">
                    <i data-lucide="heart" class="w-3 h-3"></i> Overall C&R Average
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Production & Activity Distribution Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lifetime Production Summary (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i data-lucide="ruler" class="w-4 h-4 text-teal-600"></i>
                    <span>Lifetime Production & Seasonal Activity</span>
                </h2>
                <span class="text-xs font-mono font-semibold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200">
                    {{ $totalFeet }} ft. Landed
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Cumulative Length</span>
                    <strong class="text-lg font-black text-slate-900 font-mono block mt-0.5">{{ number_format($totalInches, 1) }} in.</strong>
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
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden space-y-4">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-teal-600"></i>
                    <span>Angler Summary Telemetry</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">High-level aggregate statistics across all registered crew anglers.</p>
            </div>

            <span class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-3 py-1 rounded-full font-mono">
                {{ count($anglersList) }} Active Anglers
            </span>
        </div>

        @if(count($anglersList) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th scope="col" class="py-3 px-4">Angler</th>
                            <th scope="col" class="py-3 px-4 text-center">Catches</th>
                            <th scope="col" class="py-3 px-4 text-center">Lakes Fished</th>
                            <th scope="col" class="py-3 px-4 text-center">Expeditions</th>
                            <th scope="col" class="py-3 px-4 text-center">Avg. Length</th>
                            <th scope="col" class="py-3 px-4 text-center">Avg. Weight</th>
                            <th scope="col" class="py-3 px-4 text-center">C&R Release %</th>
                            <th scope="col" class="py-3 px-4">Target Species</th>
                            <th scope="col" class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($anglersList as $ang)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-medium text-slate-900">
                                    <div class="flex items-center gap-3">
                                        <x-anglerAvatar :angler="$ang" size="sm" />
                                        <div>
                                            <span class="font-bold text-slate-900 block text-xs leading-tight">{{ $ang->firstName }} {{ $ang->lastName }}</span>
                                            <span class="text-[10px] text-slate-400 block font-mono">Angler #{{ $ang->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-teal-700 text-xs">{{ number_format($ang->records_count) }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800 text-xs">{{ $ang->unique_lakes_count }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800 text-xs">{{ $ang->expeditions_count }}</td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-700 text-xs">{{ $ang->avg_length ? $ang->avg_length . ' in.' : '—' }}</td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-700 text-xs">{{ $ang->avg_weight ? $ang->avg_weight . ' lbs.' : '—' }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-600 text-xs">{{ $ang->release_rate }}%</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800 text-xs">
                                    <span class="bg-slate-100 text-slate-800 px-2 py-0.5 rounded-md border border-slate-200">{{ $ang->top_species_name }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ url('/angler/' . $ang->id . '/profile') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-teal-50 text-slate-700 hover:text-teal-700 font-semibold text-xs rounded-xl border border-slate-200 hover:border-teal-200 transition-colors">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-teal-600"></i>
                                        <span>Profile</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
