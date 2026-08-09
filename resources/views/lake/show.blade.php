@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Lake Detail Hero Card -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="waves" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ $lake->name }}</h1>
                <p class="text-xs text-slate-400 font-medium">Canadian Angling Waterbody</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="/lake/{{ $lake->id }}/visits" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>Visits Log</span>
            </a>
            <a href="/lake/{{ $lake->id }}/edit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit</span>
            </a>
            <form action="/lake/{{ $lake->id }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this lake?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 bg-rose-950/80 hover:bg-rose-900 text-rose-300 font-semibold text-xs rounded-xl border border-rose-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-400"></i>
                    <span>Delete</span>
                </button>
            </form>
            <a href="/lake" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                Back
            </a>
        </div>
    </div>

    <!-- Lake Badges & Map Card -->
    @if($lake->structure || $lake->max_depth || ($lake->latitude && $lake->longitude) || $lake->fishingZone)
        <div class="flex flex-wrap items-center gap-2 text-xs">
            @if($lake->fishingZone)
                <a href="{{ url('/fishing-zone/' . $lake->fishingZone->id) }}" class="bg-indigo-50 text-indigo-800 border border-indigo-200 hover:bg-indigo-100 px-3 py-1.5 rounded-xl font-medium transition-colors flex items-center gap-1">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-indigo-600"></i>
                    <span>Zone: <strong class="font-mono">{{ $lake->fishingZone->code }}</strong> — {{ $lake->fishingZone->name }}</span>
                    <i data-lucide="arrow-up-right" class="w-3 h-3 text-indigo-500"></i>
                </a>
            @endif
            @if($lake->structure)
                <span class="bg-teal-50 text-teal-800 border border-teal-200 px-3 py-1.5 rounded-xl font-medium">
                    🌊 Bottom Cover: <strong>{{ $lake->structure }}</strong>
                </span>
            @endif
            @if($lake->max_depth)
                <span class="bg-slate-100 text-slate-800 border border-slate-200 px-3 py-1.5 rounded-xl font-medium">
                    📏 Max Depth: <strong>{{ $lake->max_depth }} ft</strong>
                </span>
            @endif
            @if($lake->latitude && $lake->longitude)
                <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-3 py-1.5 rounded-xl font-medium">
                    📍 GPS Coordinates: <strong>{{ number_format($lake->latitude, 4) }}°N, {{ number_format($lake->longitude, 4) }}°W</strong>
                </span>
            @endif
        </div>
    @endif

    @if($lake->latitude && $lake->longitude)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden space-y-3">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i data-lucide="compass" class="w-4 h-4 text-teal-600"></i>
                    <span>Location & Topographic Map</span>
                </h2>
                @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
                    <span class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-2.5 py-0.5 rounded-full">
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
                            <a href="{{ url('/lake/' . $nearLake->id) }}" class="bg-white hover:bg-teal-50 text-slate-800 border border-slate-200 hover:border-teal-300 font-semibold px-2.5 py-1 rounded-lg transition-colors">
                                🏞️ {{ $nearLake->name }} ({{ $nearLake->distance }} mi)
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Applicable Zone Regulations & Limits Card -->
    @if($lake->fishingZone && isset($lake->fishingZone->rules) && count($lake->fishingZone->rules) > 0)
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="space-y-0.5">
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-indigo-600"></i>
                        <span>Applicable Regulations & Possession Limits</span>
                    </h2>
                    <p class="text-xs text-slate-500">Governed by <strong>{{ $lake->fishingZone->code }} — {{ $lake->fishingZone->name }}</strong></p>
                </div>
                <a href="{{ url('/fishing-zone/' . $lake->fishingZone->id) }}" class="text-xs font-semibold text-indigo-600 hover:underline flex items-center gap-1">
                    <span>Full Zone Guide</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th scope="col" class="py-2.5 px-3.5">Species</th>
                            <th scope="col" class="py-2.5 px-3.5">Season</th>
                            <th scope="col" class="py-2.5 px-3.5 text-center">Sport (S)</th>
                            <th scope="col" class="py-2.5 px-3.5 text-center">Conservation (C)</th>
                            <th scope="col" class="py-2.5 px-3.5">Size Restrictions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($lake->fishingZone->rules as $rule)
                            <tr class="{{ $rule->is_aggregate ? 'bg-indigo-50/40 border-l-4 border-l-indigo-500' : '' }}">
                                <td class="py-2.5 px-3.5 font-bold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        @if($rule->is_aggregate)
                                            <i data-lucide="layers" class="w-3.5 h-3.5 text-indigo-600 shrink-0"></i>
                                        @else
                                            <i data-lucide="fish" class="w-3.5 h-3.5 text-teal-600 shrink-0"></i>
                                        @endif
                                        <div>
                                            <span>{{ $rule->species_name }}</span>
                                            @if($rule->is_aggregate)
                                                <span class="block text-[9px] font-black uppercase text-indigo-800 font-mono">
                                                    Aggregate Limit
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3.5 font-medium text-slate-800">{{ $rule->season }}</td>
                                <td class="py-2.5 px-3.5 text-center font-mono font-bold text-slate-900">{{ $rule->sport_limit }}</td>
                                <td class="py-2.5 px-3.5 text-center font-mono font-bold text-teal-700">{{ $rule->conservation_limit }}</td>
                                <td class="py-2.5 px-3.5 text-slate-600">{{ $rule->size_restriction }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Key Trophy Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 text-center space-y-1">
            <span class="text-3xl font-black text-slate-900 tracking-tight block">{{ $count }}</span>
            <span class="text-xs text-slate-500 font-medium block">Total Fish Logged</span>
            <div class="pt-2 flex justify-center gap-3 text-xs text-slate-400 border-t border-slate-100">
                <span>{{ $visits }} Visits</span>
                <span>•</span>
                <span>{{ $anglers }} Anglers</span>
            </div>
        </div>

        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-md border border-slate-700/50 text-center space-y-1">
            @isset($longest)
                <span class="text-3xl font-black text-white tracking-tight block">{{ $longest->length }} <span class="text-base font-normal">in.</span></span>
                <span class="text-xs text-teal-300 font-semibold block">{{ $longest->fishBreed->name ?? 'Fish' }}</span>
                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400 block pt-1">Longest Catch</span>
            @else
                <span class="text-2xl font-bold text-slate-400 block py-2">--</span>
                <span class="text-xs text-slate-400 block">No length record yet</span>
            @endisset
        </div>

        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-md border border-slate-700/50 text-center space-y-1">
            @if(isset($fattest) && !is_null($fattest->weight))
                <span class="text-3xl font-black text-white tracking-tight block">{{ $fattest->weight }} <span class="text-base font-normal">lbs.</span></span>
                <span class="text-xs text-teal-300 font-semibold block">{{ $fattest->fishBreed->name ?? 'Fish' }}</span>
                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400 block pt-1">Fattest Catch</span>
            @else
                <span class="text-2xl font-bold text-slate-400 block py-2">--</span>
                <span class="text-xs text-slate-400 block">No weight record yet</span>
            @endif
        </div>
    </div>

    <!-- Species Statistics Grid -->
    <div class="space-y-4">
        <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
            <i data-lucide="fish" class="w-4 h-4 text-teal-600"></i>
            <span>Species Statistics</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($stats as $stat)
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-3">
                    <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2 flex items-center justify-between">
                        <span>{{ $stat->fishBreed->name }}</span>
                        <span class="bg-teal-50 text-teal-700 text-xs px-2.5 py-0.5 rounded-full border border-teal-200">{{ $stat->cnt }} Total</span>
                    </h3>

                    <div class="grid grid-cols-2 gap-3 text-center text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Avg. Length</span>
                            <span class="text-base font-bold text-slate-800 block mt-0.5">{{ $stat->avg_length }} in.</span>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $stat->min_length }}/{{ $stat->max_length }} (Min/Max)</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Avg. Weight</span>
                            @if(!is_null($stat->avg_weight))
                                <span class="text-base font-bold text-slate-800 block mt-0.5">{{ $stat->avg_weight }} lbs.</span>
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
