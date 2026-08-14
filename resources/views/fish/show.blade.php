@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Hero Showcase Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Species Illustration Canvas -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm flex flex-col items-center justify-center relative min-h-[240px] lg:min-h-[280px]">
            <div class="absolute top-3.5 left-3.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold rounded-xl shadow-xs">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>{{ $fish->family?->name ? $fish->family->name . ' Family' : 'Taxonomy' }}</span>
                </span>
            </div>

            <div class="w-full h-full flex items-center justify-center p-2">
                @if($fish->imageUrl)
                    <img src="{{ $fish->imageUrl }}" alt="{{ $fish->name }}" class="max-h-56 w-auto object-contain mix-blend-multiply hover:scale-105 transition-transform duration-300">
                @else
                    <div class="text-center space-y-2 py-8">
                        <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center mx-auto">
                            <i data-lucide="fish" class="w-8 h-8"></i>
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
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                            <span>Field Guide</span>
                        </a>
                        <a href="/fish/breed/{{ $fish->id }}/edit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            <span>Edit</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $fish->name }}</h1>
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
                        <i data-lucide="hook" class="w-3.5 h-3.5 text-teal-400"></i>
                    </div>
                    <span class="text-2xl font-black text-white block font-mono">{{ $count }}</span>
                    <span class="text-[10px] text-slate-400 block">Logged across all waters</span>
                </div>

                <div class="bg-gradient-to-br from-slate-800 to-slate-800/90 rounded-xl p-3 border border-amber-500/30 space-y-1">
                    <div class="flex items-center justify-between text-amber-300">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Max Length Record</span>
                        <i data-lucide="trophy" class="w-3.5 h-3.5 text-amber-400"></i>
                    </div>
                    <span class="text-2xl font-black text-amber-300 block font-mono">
                        {{ $longest ? $longest . ' in.' : '—' }}
                    </span>
                    <span class="text-[10px] text-slate-400 block truncate">
                        {{ $recordTrophy?->angler?->fullName ? 'Held by ' . $recordTrophy->angler->fullName : 'No records yet' }}
                    </span>
                </div>

                <div class="bg-gradient-to-br from-slate-800 to-slate-800/90 rounded-xl p-3 border border-amber-500/30 space-y-1">
                    <div class="flex items-center justify-between text-amber-300">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Max Weight Record</span>
                        <i data-lucide="award" class="w-3.5 h-3.5 text-amber-400"></i>
                    </div>
                    <span class="text-2xl font-black text-amber-300 block font-mono">
                        {{ $fattest ? $fattest . ' lbs.' : '—' }}
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
                    <i data-lucide="plus-circle" class="w-4 h-4 text-slate-950"></i>
                    <span>Log Catch for {{ $fish->name }}</span>
                </a>

                <span class="text-xs text-slate-400">
                    {{ count($lakes) }} {{ count($lakes) == 1 ? 'Lake' : 'Lakes' }} documented
                </span>
            </div>
        </div>
    </div>

    <!-- Telemetry Intelligence Modules (Top Lures, Angler Leaderboard, Seasonal Activity) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Module 1: Top Productive Lures -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="target" class="w-4 h-4 text-teal-600"></i>
                    <span>Top Productive Lures</span>
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 font-mono">{{ $topLures->count() }} Types</span>
            </div>

            @if($topLures->count() > 0)
                <div class="space-y-3">
                    @foreach($topLures as $tl)
                        @php
                            $pct = $count > 0 ? round(($tl->catches_count / $count) * 100) : 0;
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <a href="/lure/{{ $tl->lures_id }}" class="font-bold text-slate-800 hover:text-teal-600 truncate max-w-[160px]">
                                    {{ $tl->lure?->name ?? 'Lure #' . $tl->lures_id }}
                                </a>
                                <span class="font-mono font-bold text-slate-700 text-[11px]">
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
                <div class="py-6 text-center text-xs text-slate-400 space-y-1">
                    <i data-lucide="disc" class="w-6 h-6 mx-auto text-slate-300"></i>
                    <p>No lure telemetry logged yet</p>
                </div>
            @endif
        </div>

        <!-- Module 2: Angler Leaderboard -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-teal-600"></i>
                    <span>Angler Leaderboard</span>
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 font-mono">Top Anglers</span>
            </div>

            @if($topAnglers->count() > 0)
                <div class="space-y-3">
                    @foreach($topAnglers as $index => $ta)
                        <div class="flex items-center justify-between text-xs py-1">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black font-mono shrink-0 {{ $index == 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $index + 1 }}
                                </span>
                                @if($ta->angler)
                                    <x-anglerAvatar :angler="$ta->angler" size="w-7 h-7 text-xs" />
                                    <a href="/angler/{{ $ta->angler->id }}" class="font-bold text-slate-900 hover:text-teal-600 truncate">
                                        {{ $ta->angler->fullName }}
                                    </a>
                                @else
                                    <span class="font-semibold text-slate-600">Unknown</span>
                                @endif
                            </div>

                            <div class="text-right shrink-0">
                                <span class="font-bold font-mono text-teal-700">{{ $ta->catches_count }} catches</span>
                                @if($ta->longest_catch)
                                    <span class="text-[10px] text-slate-400 block font-mono">PB: {{ $ta->longest_catch }} in.</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center text-xs text-slate-400 space-y-1">
                    <i data-lucide="user-x" class="w-6 h-6 mx-auto text-slate-300"></i>
                    <p>No angler catches logged yet</p>
                </div>
            @endif
        </div>

        <!-- Module 3: Seasonal Catch Breakdown -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-teal-600"></i>
                    <span>Seasonal Activity (Apr - Nov)</span>
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 font-mono">Timeline</span>
            </div>

            <div class="grid grid-cols-8 gap-1.5 items-end h-28 pt-2">
                @foreach($monthlyStats as $ms)
                    <div class="flex flex-col items-center gap-1.5 h-full justify-end">
                        <span class="text-[10px] font-bold text-slate-600 font-mono {{ $ms['count'] > 0 ? 'text-teal-700' : 'text-slate-300' }}">
                            {{ $ms['count'] }}
                        </span>
                        <div class="w-full bg-slate-100 rounded-t-md flex items-end h-16">
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
        </div>
    </div>

    <!-- Lake Distribution & Waters Breakdown -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="waves" class="w-4 h-4 text-teal-600"></i>
                <span>Documented Lake Distribution for {{ $fish->name }}</span>
            </h2>
            <span class="text-xs text-slate-500 font-mono">{{ count($lakes) }} Waterbody Location(s)</span>
        </div>

        @if(count($lakes) > 0)
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th scope="col" class="py-3 px-4">Lake Name</th>
                            <th scope="col" class="py-3 px-4 text-center">GPS Coordinates</th>
                            <th scope="col" class="py-3 px-4 text-center">Total Catches</th>
                            <th scope="col" class="py-3 px-4 text-center">Length (Min / Max / Avg)</th>
                            <th scope="col" class="py-3 px-4 text-center">Recorded Visits</th>
                            <th scope="col" class="py-3 px-4 text-right">View Lake</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($lakes as $lake)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <a href="/lake/{{ $lake->lake->id }}" class="hover:text-teal-600 hover:underline">
                                        {{ $lake->lake->name }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-500">
                                    {{ number_format($lake->lake->latitude, 4) }}, {{ number_format($lake->lake->longitude, 4) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-teal-700">
                                    {{ $lake->count }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-700 font-semibold">
                                    {{ $lake->min_length }} / <strong class="text-slate-900">{{ $lake->max_length }}</strong> / {{ $lake->avg_length }} in.
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-700">
                                    {{ $lake->visits }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href='/lake/{{ $lake->lake->id }}' class="p-1.5 rounded-lg text-slate-500 hover:text-teal-600 hover:bg-teal-50 transition-colors inline-block" title="View Lake Details">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center text-xs text-slate-400 space-y-1">
                <i data-lucide="map-pin-off" class="w-6 h-6 mx-auto text-slate-300"></i>
                <p>No lake records logged for this species yet</p>
            </div>
        @endif
    </div>

    <!-- Recent Catches Log Feed -->
    @if($recentCatches->count() > 0)
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-teal-600"></i>
                    <span>Recent Catches for {{ $fish->name }}</span>
                </h2>
                <span class="text-xs text-slate-400 font-mono">Latest {{ $recentCatches->count() }} Entries</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentCatches as $catch)
                    <div class="bg-slate-50/80 rounded-xl p-3.5 border border-slate-200/70 hover:border-teal-500/40 transition-colors space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                @if($catch->angler)
                                    <x-anglerAvatar :angler="$catch->angler" size="w-6 h-6 text-xs" />
                                    <span class="font-bold text-xs text-slate-900 truncate">{{ $catch->angler->fullName }}</span>
                                @else
                                    <span class="text-xs font-semibold text-slate-600">Unknown Angler</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono shrink-0">
                                {{ $catch->caught ? \Carbon\Carbon::parse($catch->caught)->format('M d, Y') : $catch->created_at->format('M d, Y') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-1 border-t border-slate-200/60 text-xs">
                            <div>
                                <span class="font-extrabold text-slate-900 font-mono">{{ $catch->length ? $catch->length . ' in.' : 'Length N/A' }}</span>
                                @if($catch->weight)
                                    <span class="text-slate-500 font-mono text-[11px]">• {{ $catch->weight }} lbs.</span>
                                @endif
                            </div>

                            @if($catch->lake)
                                <a href="/lake/{{ $catch->lake->id }}" class="text-[11px] font-semibold text-teal-700 hover:underline truncate max-w-[120px]">
                                    {{ $catch->lake->name }}
                                </a>
                            @endif
                        </div>

                        @if($catch->lure)
                            <div class="text-[10px] text-slate-500 flex items-center gap-1 truncate">
                                <i data-lucide="disc" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                <span>Lure: {{ $catch->lure->name }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
