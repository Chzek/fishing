@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- 1. Dark Slate Hero Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-5">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                    <i data-lucide="fish" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Catches Logbook & Telemetry</h1>
                    <p class="text-xs text-slate-400 font-medium pt-0.5">High-Level Angling Production, System Records & Logbook Directory</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ url('/record/create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-950/50 transition-all flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Log New Catch</span>
                </a>
                <a href="{{ url('/record/quick') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-950/50 transition-all flex items-center gap-1.5">
                    <i data-lucide="zap" class="w-4 h-4 text-emerald-200"></i>
                    <span>Boat Quick Catch</span>
                </a>
                <a href="{{ url('/record/offline-review') }}" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>Offline Review</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-800 text-xs font-mono">
            <div class="p-3 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold">Total Catches</span>
                <span class="text-xl font-black text-white block pt-0.5">{{ number_format($totalCatches) }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold">Landed Feet</span>
                <span class="text-xl font-black text-teal-400 block pt-0.5">{{ number_format($totalFeet, 1) }} ft</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold">C&R Release Rate</span>
                <span class="text-xl font-black text-emerald-400 block pt-0.5">{{ $releaseRate }}%</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold">Average Length</span>
                <span class="text-xl font-black text-sky-400 block pt-0.5">{{ $avgLength }} in</span>
            </div>
        </div>
    </div>

    <!-- 2. High-Level Telemetry Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Cumulative Production -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <i data-lucide="ruler" class="w-3.5 h-3.5 text-teal-600"></i> Lifetime Production
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
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <i data-lucide="heart-handshake" class="w-3.5 h-3.5 text-emerald-600"></i> C&R Conservation Rate
                </span>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 font-mono">Released</span>
            </div>
            <div class="space-y-1 pt-1">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black text-emerald-600 font-mono">{{ $releaseRate }}%</span>
                    <span class="text-xs font-bold text-slate-500">release rate</span>
                </div>
                <div class="text-xs text-slate-500 flex items-center justify-between pt-1.5 border-t border-slate-100 font-mono">
                    <span>Released: <strong class="text-emerald-700">{{ number_format($releasedCount) }}</strong></span>
                    <span>Kept: <strong class="text-slate-700">{{ number_format($totalCatches - $releasedCount) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Card 3: All-Time Longest Catch -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <i data-lucide="trophy" class="w-3.5 h-3.5 text-amber-500"></i> Longest Catch Record
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
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                    <i data-lucide="scale" class="w-3.5 h-3.5 text-sky-600"></i> Heavyweight Record
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
    </div>

    <!-- 3. Leaderboards & Macro Target Species Trends Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top 5 Anglers Leaderboard -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
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
                    <i data-lucide="waves" class="w-4 h-4 text-teal-600"></i>
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
                    <i data-lucide="trending-up" class="w-4 h-4 text-indigo-600"></i>
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
                                <i data-lucide="fish" class="w-3.5 h-3.5 text-teal-600 shrink-0"></i>
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
                        <!-- Progress bar -->
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-teal-500 to-indigo-500 h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(5, $trend->percentage)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 4. Logbook Catches Directory & Filters (Bottom Section) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-teal-600"></i>
                    <span>Catches Logbook Directory</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Search and filter individual catch records by keyword or fish length</p>
            </div>

            <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                Showing {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} of {{ $records->total() }} Records
            </span>
        </div>

        <!-- Filters form -->
        <form action="{{ url('/record') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
                <!-- Unified Keyword Search (Species, Lake, Angler, Lure, Notes) -->
                <div class="relative flex-1 flex items-center min-w-[180px]">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none shrink-0"></i>
                    <input type="text" name="search" value="{{ Request::input('search') }}" placeholder="Search species, lake, angler, lure..."
                        class="w-full h-9 pl-10 pr-3 text-xs rounded-xl border border-slate-200 bg-white text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <!-- Streamlined Length Numeric Filter -->
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Length</span>
                    <select name="length_operator" class="h-9 px-2 text-xs rounded-xl border border-slate-200 bg-white font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20">
                        <option value=">" {{ Request::input('length_operator') === '>' ? 'selected' : '' }}>&gt;</option>
                        <option value="=" {{ Request::input('length_operator') === '=' ? 'selected' : '' }}>=</option>
                        <option value="<" {{ Request::input('length_operator') === '<' ? 'selected' : '' }}>&lt;</option>
                    </select>
                    <input type="number" step="0.25" name="length" value="{{ Request::input('length') }}" placeholder="Inches..."
                        class="h-9 px-3 w-24 text-xs rounded-xl border border-slate-200 bg-white font-mono text-slate-800 focus:ring-2 focus:ring-teal-500/20">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="h-9 px-4 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter Logs</span>
                </button>
                @if(Request::hasAny(['search', 'length', 'length_operator', 'angler']))
                    <a href="{{ url('/record') }}" class="h-9 px-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition-colors flex items-center gap-1">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        <!-- Records Data Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="py-3 px-4">Date</th>
                        <th scope="col" class="py-3 px-4">Angler</th>
                        <th scope="col" class="py-3 px-4">Lake / Water</th>
                        <th scope="col" class="py-3 px-4">Fish Species</th>
                        <th scope="col" class="py-3 px-4">Lure / Bait</th>
                        <th scope="col" class="py-3 px-4 text-center">Weight (lbs)</th>
                        <th scope="col" class="py-3 px-4 text-center">Length (in)</th>
                        <th scope="col" class="py-3 px-4 text-center">Status</th>
                        <th scope="col" class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($records as $record)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-medium text-slate-900 whitespace-nowrap font-mono text-xs">{{ $record->caught }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800 whitespace-nowrap">
                                <a href="{{ url('/angler/' . $record->angler->id . '/profile') }}" class="hover:text-teal-600 hover:underline">
                                    {{ $record->angler->full_name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 whitespace-nowrap">
                                <a href="{{ url('/lake/' . $record->lake->id) }}" class="hover:text-teal-600 hover:underline">
                                    {{ $record->lake->name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-teal-700 whitespace-nowrap">{{ $record->fishBreed->name }}</td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                @if($record->lure)
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium border border-slate-200">
                                        {{ $record->lure->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-900">{{ $record->weight ? $record->weight . ' lbs' : '—' }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-teal-700">{{ $record->length ? $record->length . '″' : '—' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if($record->released)
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold px-2 py-0.5 rounded-full font-mono">Released</span>
                                @else
                                    <span class="bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-medium px-2 py-0.5 rounded-full font-mono">Kept</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <x-tableOptions name='record' identifier='{{ $record->id }}' />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span>Showing {{ $records->firstItem() ?? 0 }} to {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} Catch Records</span>
            <div>{{ $records->links('vendor.pagination.tailwind') }}</div>
        </div>
    </div>
</div>
@endsection
