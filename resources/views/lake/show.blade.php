@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Lake Detail Hero Card -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="waves" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ $lake->name }}</h1>
                <p class="text-xs font-medium text-teal-400 mt-1 flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-teal-400"></i>
                    <span>Canadian Angling Waterbody & Telemetry</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="/lake/{{ $lake->id }}/visits" class="px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-950/40 transition-all flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-teal-200"></i>
                <span>Visits Log</span>
            </a>
            <a href="/lake/{{ $lake->id }}/edit" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit</span>
            </a>
            <form action="/lake/{{ $lake->id }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this lake?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2.5 bg-rose-950/80 hover:bg-rose-900 text-rose-300 font-semibold text-xs rounded-xl border border-rose-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-400"></i>
                    <span>Delete</span>
                </button>
            </form>
            <a href="/lake" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                Back
            </a>
        </div>
    </div>

    <!-- Lake Badges -->
    @if($lake->structure || $lake->max_depth || ($lake->latitude && $lake->longitude) || $lake->fishingZone)
        <div class="flex flex-wrap items-center gap-2 text-xs">
            @if($lake->fishingZone)
                <a href="{{ url('/fishing-zone/' . $lake->fishingZone->id) }}" class="bg-indigo-50 text-indigo-800 border border-indigo-200 hover:bg-indigo-100 px-3 py-1.5 rounded-xl font-semibold transition-colors flex items-center gap-1.5 shadow-2xs">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-indigo-600"></i>
                    <span>Zone: <strong class="font-mono">{{ $lake->fishingZone->code }}</strong> — {{ $lake->fishingZone->name }}</span>
                    <i data-lucide="arrow-up-right" class="w-3 h-3 text-indigo-500"></i>
                </a>
            @endif
            @if($lake->structure)
                <span class="bg-teal-50 text-teal-800 border border-teal-200 px-3 py-1.5 rounded-xl font-semibold flex items-center gap-1 shadow-2xs">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span>Bottom Cover: <strong>{{ $lake->structure }}</strong></span>
                </span>
            @endif
            @if($lake->max_depth)
                <span class="bg-slate-100 text-slate-800 border border-slate-200 px-3 py-1.5 rounded-xl font-semibold flex items-center gap-1 shadow-2xs">
                    <i data-lucide="ruler" class="w-3.5 h-3.5 text-slate-600"></i>
                    <span>Max Depth: <strong>{{ $lake->max_depth }} ft</strong></span>
                </span>
            @endif
            @if($lake->latitude && $lake->longitude)
                <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-3 py-1.5 rounded-xl font-semibold flex items-center gap-1 shadow-2xs font-mono">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>GPS: <strong>{{ number_format($lake->latitude, 4) }}°N, {{ number_format($lake->longitude, 4) }}°W</strong></span>
                </span>
            @endif
        </div>
    @endif

    <!-- Key Trophy Metrics Grid (Angler Profile Match) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card 1: Total Fish Logged -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Total Production</span>
                <span class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 block">{{ number_format($count) }}</span>
                <span class="text-[11px] text-teal-600 font-semibold mt-1 inline-flex items-center gap-1">
                    <i data-lucide="calendar" class="w-3 h-3"></i> {{ $visits }} Visit(s) • {{ $anglers }} Angler(s)
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                <i data-lucide="fish" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 2: Lunker Legend (Longest Catch) -->
        <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent bg-white p-5 rounded-2xl border border-amber-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1">
                    👑 Lunker Legend
                </span>
                <span class="text-xs font-black text-amber-600 bg-amber-100 px-2.5 py-0.5 rounded-full font-mono">Length</span>
            </div>
            @isset($longest)
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($longest->length, 1) }}</span>
                        <span class="text-xs font-bold text-slate-500">inches</span>
                    </div>
                    <div class="text-xs font-bold text-teal-700">{{ $longest->fishBreed->name ?? 'Fish' }}</div>
                    <div class="pt-2 border-t border-amber-100/80 flex items-center justify-between text-xs text-slate-600">
                        <span class="flex items-center gap-1 truncate">
                            <i data-lucide="user" class="w-3 h-3 text-slate-400 shrink-0"></i>
                            <span class="truncate font-medium">{{ $longest->angler->full_name ?? 'Angler' }}</span>
                        </span>
                        <span class="font-mono text-[11px] text-slate-400 shrink-0">{{ $longest->caught }}</span>
                    </div>
                </div>
            @else
                <div class="py-4 text-center text-slate-400 text-xs italic">
                    No length record logged yet.
                </div>
            @endisset
        </div>

        <!-- Card 3: Heavyweight Champ (Fattest Catch) -->
        <div class="bg-gradient-to-br from-sky-500/10 via-sky-500/5 to-transparent bg-white p-5 rounded-2xl border border-sky-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-800 flex items-center gap-1">
                    🏋️ Heavyweight Champ
                </span>
                <span class="text-xs font-black text-sky-600 bg-sky-100 px-2.5 py-0.5 rounded-full font-mono">Weight</span>
            </div>
            @if(isset($fattest) && !is_null($fattest->weight))
                <div class="space-y-1 pt-1">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($fattest->weight, 1) }}</span>
                        <span class="text-xs font-bold text-slate-500">lbs.</span>
                    </div>
                    <div class="text-xs font-bold text-teal-700">{{ $fattest->fishBreed->name ?? 'Fish' }}</div>
                    <div class="pt-2 border-t border-sky-100/80 flex items-center justify-between text-xs text-slate-600">
                        <span class="flex items-center gap-1 truncate">
                            <i data-lucide="user" class="w-3 h-3 text-slate-400 shrink-0"></i>
                            <span class="truncate font-medium">{{ $fattest->angler->full_name ?? 'Angler' }}</span>
                        </span>
                        <span class="font-mono text-[11px] text-slate-400 shrink-0">{{ $fattest->caught }}</span>
                    </div>
                </div>
            @else
                <div class="py-4 text-center text-slate-400 text-xs italic">
                    No weight record logged yet.
                </div>
            @endif
        </div>
    </div>

    <!-- Location & Topographic Map Card -->
    @if($lake->latitude && $lake->longitude)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden space-y-3">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i data-lucide="compass" class="w-4 h-4 text-teal-600"></i>
                    <span>Location & Topographic Map</span>
                </h2>
                @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
                    <span class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">
                        {{ $nearbyLakes->count() }} Nearby Lake(s) within 2 Miles
                    </span>
                @endif
            </div>

            <div id="lake-show-map" class="w-full h-[380px]"></div>

            @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
                <div class="p-4 bg-slate-50 border-t border-slate-100 text-xs space-y-2">
                    <span class="font-bold text-slate-700 block">Identified Lakes Within 2 Miles:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($nearbyLakes as $nearLake)
                            <a href="{{ url('/lake/' . $nearLake->id) }}" class="bg-white hover:bg-teal-50 text-slate-800 border border-slate-200 hover:border-teal-300 font-semibold px-2.5 py-1 rounded-lg transition-colors flex items-center gap-1 shadow-2xs">
                                🏞️ {{ $nearLake->name }} <span class="text-slate-400 font-mono text-[11px]">({{ $nearLake->distance }} mi)</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Species Statistics Grid -->
    @if(isset($stats) && count($stats) > 0)
        <div class="space-y-4">
            <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="fish" class="w-4.5 h-4.5 text-teal-600"></i>
                <span>Species Statistics</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($stats as $stat)
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-3">
                        <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2 flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i data-lucide="dna" class="w-4 h-4 text-emerald-600"></i>
                                <span>{{ $stat->fishBreed->name }}</span>
                            </span>
                            <span class="bg-teal-50 text-teal-700 font-mono text-xs font-bold px-2.5 py-0.5 rounded-full border border-teal-200">{{ $stat->cnt }} Total</span>
                        </h3>

                        <div class="grid grid-cols-2 gap-3 text-center text-xs">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Avg. Length</span>
                                <span class="text-base font-black text-slate-900 font-mono block mt-0.5">{{ $stat->avg_length }} in.</span>
                                <span class="text-[10px] text-slate-500 font-mono">{{ $stat->min_length }}/{{ $stat->max_length }} (Min/Max)</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Avg. Weight</span>
                                @if(!is_null($stat->avg_weight))
                                    <span class="text-base font-black text-slate-900 font-mono block mt-0.5">{{ $stat->avg_weight }} lbs.</span>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ $stat->min_weight }}/{{ $stat->max_weight }} (Min/Max)</span>
                                @else
                                    <span class="text-xs text-slate-400 block py-1.5">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Catches Logbook Directory Quick Access Banner Card -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                <i data-lucide="book-open" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>Catches Logbook Directory</span>
                    <span class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">{{ number_format($count) }} Catches</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Explore all catch records logged at {{ $lake->name }} with weather telemetry, lures, and species history.</p>
            </div>
        </div>

        <a href="{{ url('/record/directory') }}?lake={{ $lake->id }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-md transition-all shrink-0">
            <span>View Lake Catches</span>
            <i data-lucide="arrow-right" class="w-4 h-4 text-teal-400"></i>
        </a>
    </div>
</div>
@endsection

@section('scripts')
@if($lake->latitude && $lake->longitude)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $lake->latitude }};
        const lng = {{ $lake->longitude }};

        const map = L.map('lake-show-map').setView([lat, lng], 12);

        const topoLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Tiles &copy; Esri, NRCan CanVec'
        }).addTo(map);

        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Source: Esri, Maxar'
        });

        L.control.layers({
            "🗺️ Topo / Waterbody": topoLayer,
            "🛰️ Satellite Imagery": satLayer
        }).addTo(map);

        // Draw 2-mile radius circle around lake (3,218.68 meters = 2 miles)
        L.circle([lat, lng], {
            color: '#0d9488',
            fillColor: '#0d9488',
            fillOpacity: 0.08,
            radius: 3218.68
        }).addTo(map);

        // Target Lake Marker
        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>{{ $lake->name }}</b><br>Coordinates: " + lat + ", " + lng)
            .openPopup();

        // Render Nearby Lakes within 2 miles
        @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
            const nearbyLakesData = @json($nearbyLakes);

            nearbyLakesData.forEach(function (nLake) {
                if (nLake.latitude && nLake.longitude) {
                    const nMarker = L.circleMarker([nLake.latitude, nLake.longitude], {
                        radius: 8,
                        fillColor: "#10b981",
                        color: "#ffffff",
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(map);

                    nMarker.bindPopup(
                        "<b>🏞️ " + nLake.name + "</b><br>" +
                        "📍 " + nLake.distance + " miles away<br>" +
                        "<a href='/lake/" + nLake.id + "' class='btn btn-sm btn-outline-success mt-1'>View Lake</a>"
                    );
                }
            });
        @endif
    });
</script>
@endif
@endsection
