@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Expedition Header Card -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <x-lucide-ship class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>{{ $expedition->description }}</span>
                    @if(view()->exists('expedition.edit'))
                        <a href="/expedition/{{ $expedition->id }}/edit" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Edit Expedition">
                            <x-lucide-edit-3 class="w-4 h-4" />
                        </a>
                    @endif
                </h1>
                <p class="text-xs text-teal-400 font-medium mt-1 flex items-center gap-2">
                    <x-lucide-calendar class="w-3.5 h-3.5" />
                    <span>{{ $expedition->start }} &mdash; {{ $expedition->finish }}</span>
                    <span>•</span>
                    <span class="font-bold text-white font-mono">{{ $totalRecords }} Total Catches</span>
                    <span>•</span>
                    <span class="text-emerald-400 font-semibold">{{ $releaseRate }}% Released</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="$dispatch('open-quick-catch', { expedition_id: '{{ $expedition->id }}' })" class="px-3.5 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-semibold text-xs rounded-xl shadow transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-zap class="w-3.5 h-3.5 text-teal-200" />
                <span>Log Catch</span>
            </button>
            <form action="/expedition/{{ $expedition->id }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this expedition?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 bg-rose-950/80 hover:bg-rose-900 text-rose-300 font-semibold text-xs rounded-xl border border-rose-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-400" />
                    <span>Delete</span>
                </button>
            </form>
            <a href="/expedition" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                Return to Index
            </a>
        </div>
    </div>

    <!-- TRIP BRAG BOARD & ACCOLADES BANNER -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 👑 Lunker Legend -->
        <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent bg-white p-5 rounded-2xl border border-amber-200 shadow-sm relative overflow-hidden space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1">
                    👑 Lunker Legend
                </span>
                <span class="text-xs font-black text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">Trophy</span>
            </div>

            @if($lunker)
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($lunker->length, 1) }}</span>
                        <span class="text-xs font-bold text-slate-500">inches</span>
                    </div>
                    <div class="text-xs font-bold text-teal-700">{{ $lunker->fishBreed->name ?? 'Fish' }}</div>
                    <div class="flex items-center gap-2 pt-1 border-t border-amber-100/80">
                        @if($lunker->angler)
                            <x-anglerAvatar :angler="$lunker->angler" size="xs" />
                            <span class="text-xs font-semibold text-slate-800">{{ $lunker->angler->fullName }}</span>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-xs text-slate-400 py-3 italic">No catches logged yet.</div>
            @endif
        </div>

        <!-- 🏋️ Heavyweight Champion -->
        <div class="bg-gradient-to-br from-sky-500/10 via-sky-500/5 to-transparent bg-white p-5 rounded-2xl border border-sky-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-800 flex items-center gap-1">
                    🏋️ Heavyweight Champ
                </span>
                <span class="text-xs font-black text-sky-600 bg-sky-100 px-2 py-0.5 rounded-full">Weight</span>
            </div>

            @if($heavyweight)
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($heavyweight->weight, 1) }}</span>
                        <span class="text-xs font-bold text-slate-500">lbs.</span>
                    </div>
                    <div class="text-xs font-bold text-teal-700">{{ $heavyweight->fishBreed->name ?? 'Fish' }}</div>
                    <div class="flex items-center gap-2 pt-1 border-t border-sky-100/80">
                        @if($heavyweight->angler)
                            <x-anglerAvatar :angler="$heavyweight->angler" size="xs" />
                            <span class="text-xs font-semibold text-slate-800">{{ $heavyweight->angler->fullName }}</span>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-xs text-slate-400 py-3 italic">No weight entries logged.</div>
            @endif
        </div>

        <!-- ⚡ Top Rod MVP -->
        <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent bg-white p-5 rounded-2xl border border-emerald-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-1">
                    ⚡ Top Rod MVP
                </span>
                <span class="text-xs font-black text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">Most Fish</span>
            </div>

            @if($topRod && $topRod->angler)
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ $topRod->catch_count }}</span>
                        <span class="text-xs font-bold text-slate-500">catches</span>
                    </div>
                    <div class="text-xs font-semibold text-slate-600 font-mono">{{ number_format($topRod->total_length, 1) }} total inches</div>
                    <div class="flex items-center gap-2 pt-1 border-t border-emerald-100/80">
                        <x-anglerAvatar :angler="$topRod->angler" size="xs" />
                        <span class="text-xs font-bold text-slate-900">{{ $topRod->angler->fullName }}</span>
                    </div>
                </div>
            @else
                <div class="text-xs text-slate-400 py-3 italic">No catches logged.</div>
            @endif
        </div>

        <!-- 🎣 Hot Lure -->
        <div class="bg-gradient-to-br from-purple-500/10 via-purple-500/5 to-transparent bg-white p-5 rounded-2xl border border-purple-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-800 flex items-center gap-1">
                    🎣 MVP Hot Lure
                </span>
                <span class="text-xs font-black text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">Top Bait</span>
            </div>

            @if($hotLure && $hotLure->lure)
                <div class="space-y-1 pt-1">
                    <div class="text-lg font-black text-slate-900 line-clamp-1" title="{{ $hotLure->lure->displayName }}">
                        {{ $hotLure->lure->name }}
                    </div>
                    <div class="text-xs font-semibold text-slate-500">{{ $hotLure->lure->color }} {{ $hotLure->lure->size }}</div>
                    <div class="pt-1 border-t border-purple-100/80 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Catches:</span>
                        <strong class="font-mono text-purple-700">{{ $hotLure->catch_count }} fish</strong>
                    </div>
                </div>
            @else
                <div class="text-xs text-slate-400 py-3 italic">No lure data recorded.</div>
            @endif
        </div>
    </div>

    <!-- 3-COLUMN ANALYTICS DASHBOARD GRID -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Col 1: Daily Catch Cadence Graph -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <x-lucide-trending-up class="w-4 h-4 text-teal-600" />
                        <span>Daily Catch Cadence</span>
                    </h2>
                    <span class="text-[11px] text-slate-400 font-mono font-semibold">Fish Per Day</span>
                </div>

                @if(count($dailyCadence) > 0)
                    @php
                        $maxCount = max($dailyCadence->pluck('count')->toArray());
                        $maxCount = $maxCount > 0 ? $maxCount : 1;
                        $peakDay = $dailyCadence->sortByDesc('count')->first();
                    @endphp

                    @if($peakDay)
                        <div class="mt-3 flex items-center justify-between bg-teal-50/80 border border-teal-200/60 rounded-xl px-3 py-1.5 text-xs text-teal-800 font-medium">
                            <span class="flex items-center gap-1.5">
                                <span>🔥 Peak Day:</span>
                                <strong class="font-bold">{{ date('M j', strtotime($peakDay->caught)) }}</strong>
                            </span>
                            <span class="font-mono font-bold text-teal-700">{{ $peakDay->count }} fish</span>
                        </div>
                    @endif

                    <div class="pt-3 space-y-2">
                        <div class="h-60 flex items-end justify-between gap-2 px-1 pb-2 border-b border-slate-100">
                            @foreach($dailyCadence as $day)
                                @php
                                    $heightPct = round(($day->count / $maxCount) * 100);
                                    $heightPct = max($heightPct, 15);
                                @endphp
                                <div class="flex-1 h-full flex flex-col justify-end items-center gap-1.5 group relative">
                                    <div class="w-full bg-slate-100 hover:bg-teal-50 rounded-t-xl transition-all flex items-end justify-center overflow-hidden" style="height: 100%;">
                                        <div class="w-full bg-gradient-to-t from-teal-600 to-teal-400 rounded-t-lg transition-all group-hover:from-teal-500 group-hover:to-teal-300 flex items-center justify-center text-[10px] font-mono font-bold text-white pb-1 shadow-sm" style="height: {{ $heightPct }}%;">
                                            {{ $day->count }}
                                        </div>
                                    </div>
                                    <span class="text-[9px] font-mono text-slate-600 font-bold truncate w-full text-center">{{ date('M j', strtotime($day->caught)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-10 text-slate-400 text-xs italic">
                        No daily cadence data available.
                    </div>
                @endif
            </div>

            <!-- Trip Pace Telemetry Summary Grid -->
            @if(count($dailyCadence) > 0)
                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 space-y-0.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Daily Average</span>
                        <strong class="text-sm font-black text-slate-900 font-mono block">{{ $dailyAvgCatches }} <span class="text-[10px] font-normal text-slate-500">fish/day</span></strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 space-y-0.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Active Days</span>
                        <strong class="text-sm font-black text-slate-900 font-mono block">{{ $daysFishedCount }} <span class="text-[10px] font-normal text-slate-500">of {{ $totalTripDays }} d</span></strong>
                    </div>
                </div>
            @endif
        </div>

        <!-- Col 2: Species Breakdown (Donut Pie Chart + Legend) -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4 flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <x-lucide-pie-chart class="w-4 h-4 text-teal-600" />
                    <span>Species Breakdown</span>
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
                        $pct = ($sp->count / $totalRecords) * 100;
                        $nextPct = $currentPct + $pct;
                        $hex = $hexColors[$idx % count($hexColors)];
                        $gradientParts[] = "{$hex} {$currentPct}% {$nextPct}%";
                        $currentPct = $nextPct;
                    }
                    $conicStyle = count($gradientParts) > 0 ? implode(', ', $gradientParts) : '#cbd5e1 0% 100%';
                @endphp

                <div class="flex flex-col items-center gap-4 py-1">
                    <!-- Donut Pie Chart -->
                    <div class="relative w-36 h-36 rounded-full shadow-md border-4 border-white shrink-0 transition-transform hover:scale-105" style="background: conic-gradient({{ $conicStyle }});">
                        <div class="absolute inset-4 rounded-full bg-white flex flex-col items-center justify-center border border-slate-100 shadow-inner">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Species</span>
                            <span class="text-xl font-black text-slate-900 font-mono leading-none my-0.5">{{ count($speciesDistribution) }}</span>
                            <span class="text-[10px] text-teal-600 font-bold font-mono">{{ $totalRecords }} fish</span>
                        </div>
                    </div>

                    <!-- Legend Breakdown -->
                    <div class="w-full space-y-2 pt-2 border-t border-slate-100">
                        @foreach($speciesDistribution as $idx => $sp)
                            @php
                                $pct = round(($sp->count / $totalRecords) * 100);
                                $colorClass = $bgColors[$idx % count($bgColors)];
                            @endphp
                            <div class="flex items-center justify-between text-xs px-0.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $colorClass }} shrink-0"></span>
                                    <span class="font-bold text-slate-800 truncate">{{ $sp->fishBreed->name ?? 'Unknown Species' }}</span>
                                </div>
                                <div class="flex items-center gap-2 font-mono shrink-0 pl-1">
                                    <span class="text-slate-500 text-[11px]">{{ $sp->count }} caught</span>
                                    <strong class="text-slate-900 w-9 text-right">{{ $pct }}%</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-10 text-slate-400 text-xs italic">
                    No species breakdown recorded.
                </div>
            @endif

            <!-- Species Telemetry Summary Grid -->
            @if(count($speciesDistribution) > 0)
                @php
                    $topSpecies = $speciesDistribution->first();
                @endphp
                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 space-y-0.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dominant Species</span>
                        <strong class="text-xs font-black text-slate-900 truncate block">{{ $topSpecies->fishBreed->name ?? 'None' }}</strong>
                        <span class="text-[10px] text-teal-600 font-bold font-mono block">{{ round(($topSpecies->count / $totalRecords) * 100) }}% share</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 space-y-0.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Release Rate</span>
                        <strong class="text-sm font-black text-slate-900 font-mono block">{{ $releaseRate }}%</strong>
                        <span class="text-[10px] text-slate-500 font-mono block">{{ $releasedCount }} released</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Col 3: Angler Crew Leaderboard -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <x-lucide-award class="w-4 h-4 text-amber-500" />
                        <span>Trip Crew Leaderboard</span>
                    </h2>
                    @if(view()->exists('expedition.crew.create'))
                        <a href="/crew/create?expeditions_id={{ $expedition->id }}" class="text-xs font-bold text-teal-600 hover:underline">
                            + Add Member
                        </a>
                    @endif
                </div>

                <div class="divide-y divide-slate-100 pt-1">
                    @forelse($crewLeaderboard as $idx => $cl)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="font-extrabold text-xs font-mono w-4 text-slate-400 shrink-0">#{{ $idx + 1 }}</span>
                                @if($cl->angler)
                                    <x-anglerAvatar :angler="$cl->angler" size="xs" />
                                    <div class="min-w-0">
                                        <strong class="font-bold text-slate-900 block truncate leading-tight">{{ $cl->angler->fullName }}</strong>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $cl->longest_fish > 0 ? 'PB: ' . number_format($cl->longest_fish, 1) . ' in.' : 'No catches' }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-right font-mono shrink-0 pl-1">
                                <strong class="text-sm font-black text-slate-900 block">{{ $cl->total_catches }}</strong>
                                <span class="text-[10px] text-slate-400 block">{{ $cl->total_length > 0 ? number_format($cl->total_length, 1) . ' in.' : '—' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs italic">
                            No angler catches recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- EXPEDITION CATCH MAP -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <x-lucide-map-pin class="w-4 h-4 text-teal-600" />
                <span>Expedition Catch & Visited Waterbodies Map</span>
            </h2>
            <span class="text-slate-500 font-mono text-xs font-semibold">
                {{ count($visitedLakes) }} Visited Lake(s) &bull; {{ count($recordsWithGps) }} Catch Pinpoint(s)
            </span>
        </div>
        <div id="expedition-gps-map" class="w-full h-80 rounded-xl border border-slate-200 overflow-hidden bg-slate-100"></div>
    </div>

    <!-- Posts Journal Stream Column -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <x-lucide-message-square class="w-4 h-4 text-teal-600" />
                <span>Trip Journal & Posts</span>
            </h2>
            @if(view()->exists('expedition.post.create'))
                <a href="/post/create?expeditions_id={{ $expedition->id }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors">
                    <x-lucide-plus class="w-3.5 h-3.5" />
                    <span>Post Update</span>
                </a>
            @endif
        </div>

        @if(count($expedition->posts) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($expedition->posts as $post)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 flex items-start gap-3">
                        <x-anglerAvatar :angler="$post->creator" size="md" />
                        <div class="space-y-1 flex-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-900">{{ $post->creator->full_name }}</span>
                                <span class="text-slate-400 font-mono text-[11px]">{{ $post->date }}</span>
                            </div>
                            <blockquote class="text-xs text-slate-700 italic border-l-2 border-teal-500 pl-2">
                                "{{ $post->description }}"
                            </blockquote>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 text-slate-400 text-xs italic">
                No posts logged for this expedition trip yet.
            </div>
        @endif
    </div>

    <!-- TRIP SCRAPBOOK & PHOTO GALLERY -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                    <x-lucide-camera class="w-4 h-4" />
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 text-base tracking-tight">Trip Scrapbook & Photo Gallery</h2>
                    <p class="text-xs text-slate-500">Shared memories, scenic shots, and brag board captures</p>
                </div>
            </div>

            <button type="button" onclick="document.getElementById('expedition-upload-card').classList.toggle('hidden')" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5 cursor-pointer">
                <x-lucide-upload class="w-3.5 h-3.5" />
                <span>Add Trip Photos</span>
            </button>
        </div>

        <!-- Optional Upload Drawer -->
        <div id="expedition-upload-card" class="hidden bg-slate-50 border border-slate-200/80 rounded-2xl p-5 space-y-4">
            <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="photoable_type" value="expedition">
                <input type="hidden" name="photoable_id" value="{{ $expedition->id }}">

                <x-photo-upload-input 
                    name="photos[]" 
                    id="expedition-album-uploader" 
                    label="Select or Take Photos to Upload" 
                    hint="Drag photos here or tap to shoot from camera. Automatically compressed." 
                />

                <div class="space-y-1">
                    <label for="exp-photo-caption" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Optional Caption / Tagline</label>
                    <input type="text" id="exp-photo-caption" name="caption" placeholder="e.g., Evening topwater action on the north bay" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('expedition-upload-card').classList.add('hidden')" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold rounded-xl shadow transition-colors">Upload to Album</button>
                </div>
            </form>
        </div>

        <!-- Photos Grid -->
        @if($expedition->photos && $expedition->photos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($expedition->photos as $photo)
                    <div class="relative group aspect-4/3 rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-slate-900">
                        <img src="{{ $photo->url }}" alt="Expedition photo" class="w-full h-full object-cover cursor-pointer group-hover:scale-105 transition-transform duration-300" onclick="openPhotoLightbox('{{ $photo->url }}', '{{ addslashes($photo->caption ?? $expedition->description) }}')">
                        
                        @if($photo->is_cover)
                            <span class="absolute top-2 left-2 bg-teal-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs backdrop-blur-xs">Cover</span>
                        @endif

                        @if($photo->caption)
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-950/90 via-slate-950/60 to-transparent p-2.5 pointer-events-none">
                                <p class="text-[11px] text-white font-medium line-clamp-1 drop-shadow-xs">{{ $photo->caption }}</p>
                            </div>
                        @endif

                        <!-- Hover Action Bar -->
                        <div class="absolute inset-0 bg-slate-950/70 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-slate-300 font-mono">{{ $photo->created_at->format('M j') }}</span>
                                <form action="{{ route('photos.destroy', $photo) }}" method="POST" class="inline" onsubmit="return confirm('Remove photo from album?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-6 h-6 rounded-full bg-rose-600/90 hover:bg-rose-600 text-white flex items-center justify-center text-xs shadow-xs cursor-pointer" title="Delete photo">✕</button>
                                </form>
                            </div>

                            <div class="flex items-center gap-1.5 pt-2">
                                @if(!$photo->is_cover)
                                    <form action="{{ route('photos.cover', $photo) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-1 bg-white/90 hover:bg-white text-slate-950 text-[10px] font-bold rounded-lg shadow-xs cursor-pointer">Set Cover</button>
                                    </form>
                                @endif
                                @auth
                                    @if(auth()->user()->angler)
                                        <form action="{{ route('photos.avatar', $photo) }}" method="POST" class="flex-1" onsubmit="return confirm('Set this photo as your profile avatar?')">
                                            @csrf
                                            <button type="submit" class="w-full py-1 bg-teal-500/90 hover:bg-teal-400 text-slate-950 text-[10px] font-bold rounded-lg shadow-xs cursor-pointer">Avatar</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 border-2 border-dashed border-slate-100 rounded-2xl space-y-2">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <x-lucide-image class="w-5 h-5" />
                </div>
                <p class="text-xs text-slate-500 font-medium">No trip photos uploaded yet.</p>
                <p class="text-[11px] text-slate-400">Capture the memories from the boat and build your trip scrapbook.</p>
            </div>
        @endif
    </div>

    <!-- Expedition Catches Log -->
    @if(count($records) > 0)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <h2 class="font-bold text-slate-900 text-base flex items-center gap-2 border-b border-slate-100 pb-3">
                <x-lucide-fish class="w-4 h-4 text-teal-600" />
                <span>Expedition Catches Log</span>
            </h2>

            @livewire('components.generic-data-table', [
                'modelClass' => \Fishinglog\Models\Record::class,
                'expeditionId' => (string) $expedition->id,
                'with' => ['angler', 'lake', 'fishBreed', 'lure'],
                'columns' => [
                    ['key' => 'caught', 'label' => 'Date', 'type' => 'date', 'sortable' => true],
                    ['key' => 'angler.lastName', 'label' => 'Angler', 'type' => 'angler_name', 'sortable' => true],
                    ['key' => 'lake.name', 'label' => 'Lake', 'type' => 'lake_link', 'sortable' => true],
                    ['key' => 'fishBreed.name', 'label' => 'Species', 'type' => 'species_name', 'sortable' => true],
                    ['key' => 'length', 'label' => 'Length / Weight', 'type' => 'catch_length_weight', 'sortable' => true],
                ],
                'searchPlaceholder' => 'Search expedition catches...',
                'itemName' => 'catches',
                'perPage' => 10,
            ])
        </div>
    @endif
</div>

<!-- Expedition Photo Lightbox Modal -->
<div id="photo-lightbox-modal" class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-sm hidden flex items-center justify-center p-4" onclick="closePhotoLightbox()">
    <div class="relative max-w-5xl max-h-[90vh] flex flex-col items-center" onclick="event.stopPropagation()">
        <img id="photo-lightbox-img" src="" alt="Full Expedition Photo" class="max-w-full max-h-[80vh] rounded-2xl object-contain shadow-2xl">
        <p id="photo-lightbox-caption" class="text-white text-xs font-semibold mt-3 text-center"></p>
        <button type="button" onclick="closePhotoLightbox()" class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-sm hover:bg-slate-700 shadow-lg cursor-pointer">✕</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openPhotoLightbox(src, caption) {
    document.getElementById('photo-lightbox-img').src = src;
    document.getElementById('photo-lightbox-caption').textContent = caption || '';
    document.getElementById('photo-lightbox-modal').classList.remove('hidden');
}

function closePhotoLightbox() {
    document.getElementById('photo-lightbox-modal').classList.add('hidden');
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closePhotoLightbox();
});

document.addEventListener('DOMContentLoaded', function () {
    const gpsRecords = @json($recordsWithGps);
    const visitedLakes = @json($visitedLakes);
    const mapEl = document.getElementById('expedition-gps-map');
    if (!mapEl) return;

    const map = L.map('expedition-gps-map').setView([48.15, -84.85], 9);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 16,
        attribution: 'Tiles &copy; Esri, NRCan CanVec'
    }).addTo(map);

    const bounds = [];

    // Plot Visited Lakes (Blue Markers)
    if (visitedLakes.length > 0) {
        visitedLakes.forEach(lake => {
            if (lake.latitude && lake.longitude) {
                const latLng = [parseFloat(lake.latitude), parseFloat(lake.longitude)];
                bounds.push(latLng);

                const marker = L.circleMarker(latLng, {
                    radius: 10,
                    fillColor: "#0284c7",
                    color: "#ffffff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.85
                }).addTo(map);

                marker.bindPopup(
                    `<div class="space-y-1">` +
                    `<b class="text-slate-900">🌊 ${lake.name}</b><br>` +
                    `<span class="text-xs text-slate-500 font-mono">${latLng[0].toFixed(4)}, ${latLng[1].toFixed(4)}</span><br>` +
                    `<a href="/lake/${lake.id}" class="text-xs font-bold text-sky-600 hover:underline mt-1 inline-block">View Lake →</a>` +
                    `</div>`
                );
            }
        });
    }

    // Plot Individual Catch GPS Pins (Teal Markers)
    if (gpsRecords.length > 0) {
        gpsRecords.forEach(rec => {
            if (rec.latitude && rec.longitude) {
                const latLng = [parseFloat(rec.latitude), parseFloat(rec.longitude)];
                bounds.push(latLng);

                const marker = L.circleMarker(latLng, {
                    radius: 7,
                    fillColor: "#0d9488",
                    color: "#ffffff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.95
                }).addTo(map);

                marker.bindPopup(
                    `<div class="space-y-1">` +
                    `<b class="text-slate-900">🐟 ${rec.fish_breed ? rec.fish_breed.name : 'Catch'} (${rec.length} in.)</b><br>` +
                    `<span class="text-xs text-slate-700">👤 ${rec.angler ? rec.angler.firstName + ' ' + rec.angler.lastName : ''}</span><br>` +
                    `<span class="text-xs text-slate-500 font-mono">🗓️ ${rec.caught}</span><br>` +
                    `<a href="/record/${rec.id}" class="text-xs font-bold text-teal-600 hover:underline mt-1 inline-block">View Catch →</a>` +
                    `</div>`
                );
            }
        });
    }

    // Auto-fit map to minimum bounding box of all visited lakes & catch pins
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
});
</script>
@endsection