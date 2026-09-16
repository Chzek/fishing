@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Lake Detail Hero Card -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <x-lucide-waves class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ $lake->name }}</h1>
                <p class="text-xs font-medium text-teal-400 mt-1 flex items-center gap-1.5">
                    <x-lucide-map-pin class="w-3.5 h-3.5 text-teal-400" />
                    <span>Canadian Angling Waterbody & Telemetry</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="/lake/{{ $lake->id }}/visits" class="px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-950/40 transition-all flex items-center gap-1.5">
                <x-lucide-calendar class="w-4 h-4 text-teal-200" />
                <span>Visits Log</span>
            </a>
            <a href="/lake/{{ $lake->id }}/edit" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <x-lucide-edit-3 class="w-3.5 h-3.5" />
                <span>Edit</span>
            </a>
            <form action="/lake/{{ $lake->id }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this lake?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2.5 bg-rose-950/80 hover:bg-rose-900 text-rose-300 font-semibold text-xs rounded-xl border border-rose-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-400" />
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
                    <x-lucide-shield class="w-3.5 h-3.5 text-indigo-600" />
                    <span>Zone: <strong class="font-mono">{{ $lake->fishingZone->code }}</strong> — {{ $lake->fishingZone->name }}</span>
                    <x-lucide-arrow-up-right class="w-3 h-3 text-indigo-500" />
                </a>
            @endif
            @if($lake->structure)
                <span class="bg-teal-50 text-teal-800 border border-teal-200 px-3 py-1.5 rounded-xl font-semibold flex items-center gap-1 shadow-2xs">
                    <x-lucide-layers class="w-3.5 h-3.5 text-teal-600" />
                    <span>Bottom Cover: <strong>{{ $lake->structure }}</strong></span>
                </span>
            @endif
            @if($lake->max_depth)
                <span class="bg-slate-100 text-slate-800 border border-slate-200 px-3 py-1.5 rounded-xl font-semibold flex items-center gap-1 shadow-2xs">
                    <x-lucide-ruler class="w-3.5 h-3.5 text-slate-600" />
                    <span>Max Depth: <strong>{{ $lake->max_depth }} ft</strong></span>
                </span>
            @endif
            @if($lake->latitude && $lake->longitude)
                <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-3 py-1.5 rounded-xl font-semibold flex items-center gap-1 shadow-2xs font-mono">
                    <x-lucide-map-pin class="w-3.5 h-3.5 text-emerald-600" />
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
                    <x-lucide-calendar class="w-3 h-3" /> {{ $visits }} Visit(s) • {{ $anglers }} Angler(s)
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                <x-lucide-fish class="w-6 h-6" />
            </div>
        </div>

        <!-- Card 2: Lunker Legend (Longest Catch) -->
        <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent bg-white p-5 rounded-2xl border border-amber-200 shadow-sm space-y-2 relative overflow-hidden">
            <x-watermarkTapeMeasure />
            <div class="flex items-center justify-between relative z-10">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1">
                    👑 Lunker Legend
                </span>
                <span class="text-xs font-black text-amber-600 bg-amber-100 px-2.5 py-0.5 rounded-full font-mono">Length</span>
            </div>
            @isset($longest)
                <div class="space-y-1 pt-1 relative z-10">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($longest->length, 1) }}</span>
                        <span class="text-xs font-bold text-slate-500">inches</span>
                    </div>
                    <div class="text-xs font-bold text-teal-700">{{ $longest->fishBreed->name ?? 'Fish' }}</div>
                    <div class="pt-2 border-t border-amber-100/80 flex items-center justify-between text-xs text-slate-600">
                        <span class="flex items-center gap-1 truncate">
                            <x-lucide-user class="w-3 h-3 text-slate-400 shrink-0" />
                            <span class="truncate font-medium">{{ $longest->angler->full_name ?? 'Angler' }}</span>
                        </span>
                        <span class="font-mono text-[11px] text-slate-400 shrink-0">{{ $longest->caught }}</span>
                    </div>
                </div>
            @else
                <div class="py-4 text-center text-slate-400 text-xs italic relative z-10">
                    No length record logged yet.
                </div>
            @endisset
        </div>

        <!-- Card 3: Heavyweight Champ (Fattest Catch) -->
        <div class="bg-gradient-to-br from-sky-500/10 via-sky-500/5 to-transparent bg-white p-5 rounded-2xl border border-sky-200 shadow-sm space-y-2 relative overflow-hidden">
            <x-watermarkDialScale />
            <div class="flex items-center justify-between relative z-10">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-800 flex items-center gap-1">
                    🏋️ Heavyweight Champ
                </span>
                <span class="text-xs font-black text-sky-600 bg-sky-100 px-2.5 py-0.5 rounded-full font-mono">Weight</span>
            </div>
            @if(isset($fattest) && !is_null($fattest->weight))
                <div class="space-y-1 pt-1 relative z-10">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($fattest->weight, 1) }}</span>
                        <span class="text-xs font-bold text-slate-500">lbs.</span>
                    </div>
                    <div class="text-xs font-bold text-teal-700">{{ $fattest->fishBreed->name ?? 'Fish' }}</div>
                    <div class="pt-2 border-t border-sky-100/80 flex items-center justify-between text-xs text-slate-600">
                        <span class="flex items-center gap-1 truncate">
                            <x-lucide-user class="w-3 h-3 text-slate-400 shrink-0" />
                            <span class="truncate font-medium">{{ $fattest->angler->full_name ?? 'Angler' }}</span>
                        </span>
                        <span class="font-mono text-[11px] text-slate-400 shrink-0">{{ $fattest->caught }}</span>
                    </div>
                </div>
            @else
                <div class="py-4 text-center text-slate-400 text-xs italic relative z-10">
                    No weight record logged yet.
                </div>
            @endif
        </div>
    </div>

    <!-- Location & Bathymetric Topographic Map Card -->
    @if($lake->latitude && $lake->longitude)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden space-y-0">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <x-lucide-compass class="w-4 h-4 text-teal-600" />
                    <span>Location, Bathymetry & Topo Map</span>
                </h2>
                <span id="viewport-lakes-badge" class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono transition-all">
                    Loading Viewport Lakes...
                </span>
            </div>

            <div id="lake-show-map" class="w-full h-[420px]"></div>
        </div>
    @endif

    <!-- Species Statistics Grid -->
    @if(isset($stats) && count($stats) > 0)
        <div class="space-y-4">
            <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <x-lucide-fish class="w-4.5 h-4.5 text-teal-600" />
                <span>Species Statistics</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($stats as $stat)
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-3">
                        <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2 flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <x-lucide-dna class="w-4 h-4 text-emerald-600" />
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
                <x-lucide-book-open class="w-6 h-6" />
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
            <x-lucide-arrow-right class="w-4 h-4 text-teal-400" />
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
        const currentLakeId = '{{ $lake->id }}';

        const map = L.map('lake-show-map').setView([lat, lng], 13);

        // OpenTopoMap Bathymetry & Relief Contours (Default active base layer)
        const bathyBaseLayer = L.tileLayer('https://tile.opentopomap.org/{z}/{x}/{y}.png', {
            maxZoom: 16,
            attribution: 'Map data: &copy; OpenStreetMap, SRTM | Style: &copy; OpenTopoMap'
        }).addTo(map);

        // ESRI Topo Layer
        const topoLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 16,
            attribution: 'Tiles &copy; Esri, NRCan CanVec'
        });

        // ESRI Satellite Layer
        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 16,
            attribution: 'Source: Esri, Maxar'
        });

        // OpenSeaMap Nautical Seamarks & Marine Soundings
        const seamarkOverlay = L.tileLayer('https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png', {
            maxZoom: 16,
            attribution: 'Map data: &copy; OpenSeaMap contributors'
        });

        const layerControl = L.control.layers({
            "🌊 Bathymetry & Contours": bathyBaseLayer,
            "🗺️ Topo / Waterbody": topoLayer,
            "🛰️ Satellite Imagery": satLayer
        }, null, { position: 'bottomleft' }).addTo(map);

        // Bathymetric Depth Contours & Structure Overlay Layer (Default Active)
        const bathyContoursLayer = L.layerGroup().addTo(map);

        fetch('/json/bathymetry-contours.geojson')
            .then(res => res.json())
            .then(geoJson => {
                const bathyData = L.geoJSON(geoJson, {
                    style: function (feature) {
                        if (feature.geometry.type === 'LineString') {
                            const depth = feature.properties.depth_ft || 10;
                            let strokeColor = '#38bdf8';
                            let weight = 2.5;
                            if (depth >= 60) {
                                strokeColor = '#1e3a8a';
                                weight = 3.5;
                            } else if (depth >= 30) {
                                strokeColor = '#1d4ed8';
                                weight = 3.0;
                            } else if (depth >= 15) {
                                strokeColor = '#0284c7';
                                weight = 2.5;
                            } else if (depth >= 10) {
                                strokeColor = '#0ea5e9';
                                weight = 2.0;
                            }
                            return {
                                color: feature.properties.color || strokeColor,
                                weight: weight,
                                opacity: 0.9,
                                dashArray: feature.properties.contour_type === 'shallow_shelf' ? '4, 4' : null
                            };
                        }
                    },
                    pointToLayer: function (feature, latlng) {
                        const structType = feature.properties.structure_type || 'reef';
                        let icon = '🪨';
                        let bg = 'bg-amber-600';
                        let border = 'border-amber-300';
                        if (structType === 'dropoff') {
                            icon = '📉';
                            bg = 'bg-sky-600';
                            border = 'border-sky-300';
                        } else if (structType === 'deep_hole') {
                            icon = '🕳️';
                            bg = 'bg-indigo-700';
                            border = 'border-indigo-300';
                        } else if (structType === 'shoal') {
                            icon = '🏖️';
                            bg = 'bg-teal-600';
                            border = 'border-teal-300';
                        }
                        const customIcon = L.divIcon({
                            className: 'bathy-structure-marker',
                            html: `<div class="w-6 h-6 rounded-full ${bg} border-2 ${border} shadow-md flex items-center justify-center text-xs select-none cursor-pointer hover:scale-125 transition-transform">${icon}</div>`,
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        });
                        return L.marker(latlng, { icon: customIcon });
                    },
                    onEachFeature: function (feature, layer) {
                        if (feature.geometry.type === 'Point') {
                            const p = feature.properties;
                            layer.bindPopup(`
                                <div class="p-1.5 text-slate-900 font-sans space-y-1 min-w-[190px]">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="bg-sky-100 text-sky-800 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase font-mono">${p.structure_type || 'Structure'}</span>
                                        <span class="text-xs font-black text-blue-700 font-mono">${p.depth_ft} ft / ${p.depth_m}m</span>
                                    </div>
                                    <div class="font-bold text-xs text-slate-900">${p.name || 'Underwater Structure'}</div>
                                    <div class="text-[10px] text-teal-700 font-semibold">${p.waterbody || 'Lake'}</div>
                                    ${p.desc ? `<div class="text-[11px] text-slate-600 pt-0.5">${p.desc}</div>` : ''}
                                </div>
                            `);
                        } else if (feature.geometry.type === 'LineString') {
                            const p = feature.properties;
                            layer.bindPopup(`
                                <div class="p-1 text-slate-900 font-sans space-y-1">
                                    <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.5 rounded font-mono">🌊 Depth Contour</span>
                                    <div class="font-bold text-xs pt-0.5">${p.waterbody || 'Lake'} &bull; <span class="text-blue-700 font-mono">${p.depth_ft} ft</span> (${p.depth_m}m)</div>
                                    <div class="text-[11px] text-slate-600">${p.label || 'Isobath line'}</div>
                                </div>
                            `);
                        }
                    }
                });

                bathyData.addTo(bathyContoursLayer);
                layerControl.addOverlay(bathyContoursLayer, "🌊 Depth Contours & Reefs");
                layerControl.addOverlay(seamarkOverlay, "⚓ OpenSeaMap Soundings");
            })
            .catch(err => console.log('Lake show bathymetry load status:', err));

        // Target Lake Marker (prominent pin with higher zIndex)
        L.marker([lat, lng], { zIndexOffset: 1000 }).addTo(map)
            .bindPopup("<div class='p-1 font-sans'><b class='text-slate-900'>📍 {{ $lake->name }}</b><br><span class='text-xs text-teal-700 font-bold'>Current Lake</span><br><span class='text-[11px] text-slate-500 font-mono'>" + lat.toFixed(4) + ", " + lng.toFixed(4) + "</span></div>")
            .openPopup();

        // Layer group for dynamic viewport lake markers
        const viewportMarkersLayer = L.layerGroup().addTo(map);

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 3958.8; // Earth radius in miles
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return (R * c).toFixed(2);
        }

        let debounceTimer = null;
        function onMapMove() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchViewportLakes, 250);
        }

        function fetchViewportLakes() {
            const bounds = map.getBounds();
            const minLat = bounds.getSouth();
            const maxLat = bounds.getNorth();
            const minLng = bounds.getWest();
            const maxLng = bounds.getEast();

            fetch(`/api/v1/explorer/lakes?min_lat=${minLat}&max_lat=${maxLat}&min_lng=${minLng}&max_lng=${maxLng}`)
                .then(res => res.json())
                .then(resData => {
                    viewportMarkersLayer.clearLayers();
                    const lakes = resData.data || [];
                    const otherLakes = lakes.filter(l => String(l.id) !== currentLakeId);

                    const badge = document.getElementById('viewport-lakes-badge');
                    if (badge) {
                        badge.innerText = `${lakes.length} Lake${lakes.length === 1 ? '' : 's'} in Viewport`;
                    }

                    otherLakes.forEach(nLake => {
                        if (nLake.latitude && nLake.longitude) {
                            const dist = calculateDistance(lat, lng, nLake.latitude, nLake.longitude);
                            const nMarker = L.circleMarker([nLake.latitude, nLake.longitude], {
                                radius: 7,
                                fillColor: "#0d9488",
                                color: "#ffffff",
                                weight: 2,
                                opacity: 1,
                                fillOpacity: 0.9
                            }).addTo(viewportMarkersLayer);

                            nMarker.bindPopup(
                                `<div class="p-1 font-sans">` +
                                `<b class="text-slate-900">🏞️ ${nLake.name}</b><br>` +
                                `<span class="text-xs text-slate-500 font-mono">📍 ${dist} mi away</span><br>` +
                                `<a href="/lake/${nLake.id}" class="inline-block mt-1.5 text-xs font-semibold text-teal-700 hover:text-teal-900 bg-teal-50 border border-teal-200 px-2 py-0.5 rounded">View Lake Dossier &rarr;</a>` +
                                `</div>`
                            );
                        }
                    });
                })
                .catch(err => console.error('Failed to load viewport lakes:', err));
        }

        map.on('moveend', onMapMove);
        map.on('zoomend', onMapMove);

        // Initial fetch on mount
        fetchViewportLakes();
    });
</script>
@endif
@endsection
