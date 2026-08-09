@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Filter Toolbar Header Card -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="compass" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span>Lake Explorer</span>
                        <span id="lake-count-badge" class="bg-teal-50 text-teal-700 border border-teal-200 text-xs font-semibold px-2.5 py-0.5 rounded-full">0 Lakes in View</span>
                    </h1>
                </div>
            </div>

            <!-- Filter Controls Form -->
            <form id="explorer-filter-form" class="w-full lg:w-auto flex flex-wrap items-center gap-2">
                <!-- Species Filter -->
                <select id="filter-species" class="h-9 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    <option value="">🐟 All Species</option>
                    @foreach($fishBreeds as $breed)
                        <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                    @endforeach
                </select>

                <!-- Angler Filter -->
                <select id="filter-angler" class="h-9 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    <option value="">👨‍🌾 All Anglers</option>
                    @foreach($anglers as $angler)
                        <option value="{{ $angler->id }}">{{ trim($angler->firstname . ' ' . $angler->lastname) ?: 'Angler #' . $angler->id }}</option>
                    @endforeach
                </select>

                <!-- Lure Filter -->
                <select id="filter-lure" class="h-9 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    <option value="">🎣 All Lures</option>
                    @foreach($lures as $lure)
                        <option value="{{ $lure->id }}">{{ $lure->name }}</option>
                    @endforeach
                </select>

                <!-- Trophy Filter -->
                <select id="filter-trophy" class="h-9 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    <option value="">🏆 All Catches</option>
                    <option value="1">🏆 Trophies Only (≥10lbs or ≥20in)</option>
                </select>

                <!-- Season Year Filter -->
                <select id="filter-year" class="h-9 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    <option value="">📅 All Seasons</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}">{{ $yr }} Season</option>
                    @endforeach
                </select>

                <button type="button" onclick="resetExplorerFilters()" class="h-9 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl transition-colors flex items-center gap-1">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Reset</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Explorer Map & Slide Drawer Container -->
    <div class="relative bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden" style="height: calc(100vh - 220px); min-height: 540px;">
        <div id="explorer-map" class="w-full h-full z-0"></div>

        <!-- Right Slide-Over Detail Drawer (Dark Option C Styling) -->
        <div id="explorer-drawer" class="absolute top-0 right-[-450px] w-[420px] max-w-full h-full bg-slate-900 text-slate-200 shadow-2xl border-l border-slate-800 z-30 transition-all duration-300 ease-in-out overflow-y-auto">
            <div class="p-4 bg-slate-950/80 border-b border-slate-800 flex items-center justify-between sticky top-0 backdrop-blur-md z-10">
                <div>
                    <h2 id="drawer-lake-name" class="font-bold text-white text-base tracking-tight">Lake Detail</h2>
                    <span id="drawer-lake-sub" class="text-xs text-teal-400 font-medium">Lake Catch Analytics</span>
                </div>
                <button type="button" onclick="closeLakeDrawer()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div id="drawer-loading" class="text-center py-12">
                <i data-lucide="loader-2" class="w-8 h-8 text-teal-400 animate-spin mx-auto"></i>
                <p class="mt-2 text-xs text-slate-400">Loading lake analytics...</p>
            </div>

            <div id="drawer-body" class="p-5 space-y-5" style="display: none;">
                <!-- Lake Badges & Coordinates -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <span id="drawer-coords-badge" class="bg-teal-500/15 text-teal-300 border border-teal-500/30 px-2.5 py-1 rounded-lg font-medium">📍 Coordinates</span>
                    <span id="drawer-terrain-badge" class="bg-slate-800 text-slate-300 border border-slate-700 px-2.5 py-1 rounded-lg font-medium" style="display: none;">🌊 Terrain</span>
                    <span id="drawer-depth-badge" class="bg-slate-800 text-slate-300 border border-slate-700 px-2.5 py-1 rounded-lg font-medium" style="display: none;">📏 Depth</span>
                </div>

                <!-- Catch & Visit Stats Grid -->
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/50">
                        <span id="drawer-stat-catches" class="text-2xl font-black text-white block">0</span>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Catches</span>
                    </div>
                    <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/50">
                        <span id="drawer-stat-visits" class="text-2xl font-black text-emerald-400 block">0</span>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Visits</span>
                    </div>
                    <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/50">
                        <span id="drawer-stat-anglers" class="text-2xl font-black text-sky-400 block">0</span>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Anglers</span>
                    </div>
                </div>

                <!-- Record Highlights (Longest & Fattest) -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-4 border border-amber-500/30 space-y-2">
                    <div class="flex items-center gap-2 text-amber-400 font-bold text-xs uppercase tracking-wider">
                        <i data-lucide="trophy" class="w-4 h-4"></i>
                        <span>Lake Trophy Records</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2 text-center border-t border-slate-700/60">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Longest</span>
                            <span id="drawer-longest-val" class="text-lg font-bold text-white block">--</span>
                            <span id="drawer-longest-breed" class="text-xs text-teal-300 block">--</span>
                        </div>
                        <div class="border-l border-slate-700/60">
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Fattest</span>
                            <span id="drawer-fattest-val" class="text-lg font-bold text-white block">--</span>
                            <span id="drawer-fattest-breed" class="text-xs text-teal-300 block">--</span>
                        </div>
                    </div>
                </div>

                <!-- Species Catch Breakdown List -->
                <div class="bg-slate-800/60 rounded-xl border border-slate-700/50 overflow-hidden">
                    <div class="px-4 py-2.5 bg-slate-800 border-b border-slate-700/60 font-bold text-xs text-slate-300 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="fish" class="w-3.5 h-3.5 text-teal-400"></i>
                        <span>Species Breakdown</span>
                    </div>
                    <ul id="drawer-species-list" class="divide-y divide-slate-700/50 text-xs">
                        <li class="p-3 text-slate-400 text-center italic">No catches recorded for active filter.</li>
                    </ul>
                </div>

                <!-- Top Producing Lures -->
                <div class="bg-slate-800/60 rounded-xl border border-slate-700/50 overflow-hidden">
                    <div class="px-4 py-2.5 bg-slate-800 border-b border-slate-700/60 font-bold text-xs text-slate-300 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="fishing-hook" class="w-3.5 h-3.5 text-teal-400"></i>
                        <span>Top Producing Lures</span>
                    </div>
                    <ul id="drawer-lures-list" class="divide-y divide-slate-700/50 text-xs">
                        <li class="p-3 text-slate-400 text-center italic">No lure records logged.</li>
                    </ul>
                </div>

                <!-- Quick Action Buttons -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <a id="drawer-quick-catch-btn" href="#" class="py-2.5 px-3 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl text-center shadow transition-colors flex items-center justify-center gap-1">
                        <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                        <span>Quick Catch</span>
                    </a>
                    <a id="drawer-full-log-btn" href="#" class="py-2.5 px-3 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs rounded-xl text-center transition-colors flex items-center justify-center gap-1">
                        <span>Full Log</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let explorerMap;
    let markersLayer;
    let activeSelectedLakeId = null;
    let debounceTimer = null;

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Leaflet Map centered over Wawa / Northern Ontario
        explorerMap = L.map('explorer-map').setView([48.15, -84.85], 9);

        // ESRI Topo Layer
        const topoLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Tiles &copy; Esri, NRCan CanVec'
        }).addTo(explorerMap);

        // ESRI Satellite Layer
        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Source: Esri, Maxar'
        });

        const layerControl = L.control.layers({
            "🗺️ Topo / Waterbody": topoLayer,
            "🛰️ Satellite Imagery": satLayer
        }).addTo(explorerMap);

        markersLayer = L.layerGroup().addTo(explorerMap);
        let fmzLayer = L.layerGroup();

        // Load FMZ GeoJSON boundaries overlay
        fetch('/json/ontario-fmz-boundaries-web.geojson')
            .then(res => res.json())
            .then(geoJson => {
                const fmzGeoJson = L.geoJSON(geoJson, {
                    style: function (feature) {
                        return {
                            color: '#6366f1',
                            weight: 2,
                            opacity: 0.8,
                            fillColor: '#818cf8',
                            fillOpacity: 0.12
                        };
                    },
                    onEachFeature: function (feature, layer) {
                        const code = feature.properties.code || ('FMZ ' + feature.properties.zone_id);
                        layer.bindPopup(`
                            <div class="p-1 text-slate-900 font-sans space-y-1">
                                <span class="bg-indigo-100 text-indigo-800 text-[10px] font-black px-2 py-0.5 rounded font-mono">${code}</span>
                                <div class="font-bold text-xs pt-1">Ontario Fisheries Management Zone ${feature.properties.zone_id}</div>
                                <a href="/fishing-zone?search=${code}" target="_blank" class="text-xs text-indigo-600 font-bold hover:underline block pt-1">View Zone Regulations & Lakes ↗</a>
                            </div>
                        `);
                    }
                });
                fmzGeoJson.addTo(fmzLayer);
                fmzLayer.addTo(explorerMap);

                // Add to Layer Switcher
                layerControl.addOverlay(fmzLayer, "🛡️ FMZ License Boundaries");
            })
            .catch(err => console.log('FMZ GeoJSON overlay load status:', err));

        // Listen for map pan/zoom events to query visible bounding box
        explorerMap.on('moveend zoomend', function () {
            debouncedFetchExplorerLakes();
        });

        // Listen for filter dropdown changes
        document.querySelectorAll('#explorer-filter-form select').forEach(select => {
            select.addEventListener('change', function () {
                fetchExplorerLakes();
                if (activeSelectedLakeId) {
                    loadLakeDrawerDetail(activeSelectedLakeId);
                }
            });
        });

        // Initial fetch
        fetchExplorerLakes();
    });

    function debouncedFetchExplorerLakes() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchExplorerLakes, 250);
    }

    function fetchExplorerLakes() {
        if (!explorerMap) return;

        const bounds = explorerMap.getBounds();
        const minLat = bounds.getSouth();
        const maxLat = bounds.getNorth();
        const minLng = bounds.getWest();
        const maxLng = bounds.getEast();

        const species = document.getElementById('filter-species').value;
        const angler = document.getElementById('filter-angler').value;
        const lure = document.getElementById('filter-lure').value;
        const trophy = document.getElementById('filter-trophy').value;
        const year = document.getElementById('filter-year').value;

        let queryUrl = `/api/v1/explorer/lakes?min_lat=${minLat}&max_lat=${maxLat}&min_lng=${minLng}&max_lng=${maxLng}`;
        if (species) queryUrl += `&fish_breed_id=${species}`;
        if (angler) queryUrl += `&angler_id=${angler}`;
        if (lure) queryUrl += `&lure_id=${lure}`;
        if (trophy) queryUrl += `&is_trophy=${trophy}`;
        if (year) queryUrl += `&year=${year}`;

        fetch(queryUrl)
            .then(res => res.json())
            .then(resData => {
                markersLayer.clearLayers();

                const lakes = resData.data || [];
                document.getElementById('lake-count-badge').innerText = `${lakes.length} Lake(s) in View`;

                lakes.forEach(lake => {
                    if (lake.latitude && lake.longitude) {
                        const marker = L.marker([lake.latitude, lake.longitude]);

                        marker.bindTooltip(`<b>🏞️ ${lake.name}</b><br><small>${lake.records_count || 0} Catches • ${lake.visits_count || 0} Visits</small>`, {
                            permanent: false,
                            direction: 'top'
                        });

                        marker.on('click', function () {
                            openLakeDrawer(lake);
                        });

                        markersLayer.addLayer(marker);
                    }
                });
            })
            .catch(err => console.error('Error fetching explorer lakes:', err));
    }

    function openLakeDrawer(lake) {
        activeSelectedLakeId = lake.id;
        const drawer = document.getElementById('explorer-drawer');
        
        document.getElementById('drawer-lake-name').innerText = lake.name;
        document.getElementById('drawer-lake-sub').innerText = `${lake.records_count || 0} Total Catches Logged`;
        
        document.getElementById('drawer-coords-badge').innerText = `📍 ${lake.latitude.toFixed(4)}°N, ${lake.longitude.toFixed(4)}°W`;

        if (lake.structure) {
            document.getElementById('drawer-terrain-badge').innerText = `🌊 ${lake.structure}`;
            document.getElementById('drawer-terrain-badge').style.display = 'inline-block';
        } else {
            document.getElementById('drawer-terrain-badge').style.display = 'none';
        }

        if (lake.max_depth) {
            document.getElementById('drawer-depth-badge').innerText = `📏 ${lake.max_depth} ft max`;
            document.getElementById('drawer-depth-badge').style.display = 'inline-block';
        } else {
            document.getElementById('drawer-depth-badge').style.display = 'none';
        }

        document.getElementById('drawer-quick-catch-btn').href = `/record/quick?lakes_id=${lake.id}`;
        document.getElementById('drawer-full-log-btn').href = `/lake/${lake.id}`;

        // Slide drawer in from right
        drawer.style.right = '0';

        loadLakeDrawerDetail(lake.id);
    }

    function loadLakeDrawerDetail(lakeId) {
        document.getElementById('drawer-loading').style.display = 'block';
        document.getElementById('drawer-body').style.display = 'none';

        const species = document.getElementById('filter-species').value;
        const angler = document.getElementById('filter-angler').value;
        const lure = document.getElementById('filter-lure').value;
        const trophy = document.getElementById('filter-trophy').value;
        const year = document.getElementById('filter-year').value;

        let queryUrl = `/api/v1/explorer/lake/${lakeId}?`;
        if (species) queryUrl += `&fish_breed_id=${species}`;
        if (angler) queryUrl += `&angler_id=${angler}`;
        if (lure) queryUrl += `&lure_id=${lure}`;
        if (trophy) queryUrl += `&is_trophy=${trophy}`;
        if (year) queryUrl += `&year=${year}`;

        fetch(queryUrl)
            .then(res => res.json())
            .then(data => {
                document.getElementById('drawer-stat-catches').innerText = data.total_catches || 0;
                document.getElementById('drawer-stat-visits').innerText = data.visits_count || 0;
                document.getElementById('drawer-stat-anglers').innerText = data.anglers_count || 0;

                // Longest Catch
                if (data.longest_catch) {
                    document.getElementById('drawer-longest-val').innerText = `${data.longest_catch.length} in.`;
                    document.getElementById('drawer-longest-breed').innerText = data.longest_catch.fish_breed ? data.longest_catch.fish_breed.name : '';
                } else {
                    document.getElementById('drawer-longest-val').innerText = '--';
                    document.getElementById('drawer-longest-breed').innerText = '--';
                }

                // Fattest Catch
                if (data.fattest_catch) {
                    document.getElementById('drawer-fattest-val').innerText = `${data.fattest_catch.weight} lbs.`;
                    document.getElementById('drawer-fattest-breed').innerText = data.fattest_catch.fish_breed ? data.fattest_catch.fish_breed.name : '';
                } else {
                    document.getElementById('drawer-fattest-val').innerText = '--';
                    document.getElementById('drawer-fattest-breed').innerText = '--';
                }

                // Species List
                const speciesListEl = document.getElementById('drawer-species-list');
                speciesListEl.innerHTML = '';
                if (data.species_breakdown && data.species_breakdown.length > 0) {
                    data.species_breakdown.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'p-3 flex justify-between items-center hover:bg-slate-800/40 transition-colors';
                        const breedName = item.fish_breed ? item.fish_breed.name : 'Unknown Species';
                        const avgW = item.avg_weight ? `${item.avg_weight} lbs` : 'unweighed';
                        li.innerHTML = `
                            <div>
                                <strong class="text-white">🐟 ${breedName}</strong>
                                <small class="block text-slate-400">Avg: ${item.avg_length} in. | ${avgW}</small>
                            </div>
                            <span class="px-2.5 py-1 bg-teal-500/20 text-teal-300 font-bold rounded-lg border border-teal-500/30 text-xs">${item.count}</span>
                        `;
                        speciesListEl.appendChild(li);
                    });
                } else {
                    speciesListEl.innerHTML = '<li class="p-3 text-slate-400 text-center italic">No catches recorded for active filter.</li>';
                }

                // Top Lures List
                const luresListEl = document.getElementById('drawer-lures-list');
                luresListEl.innerHTML = '';
                if (data.top_lures && data.top_lures.length > 0) {
                    data.top_lures.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'p-3 flex justify-between items-center hover:bg-slate-800/40 transition-colors';
                        const lureName = item.lure ? item.lure.name : 'Unknown Lure';
                        li.innerHTML = `
                            <span>🎣 <strong class="text-white">${lureName}</strong></span>
                            <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 font-bold rounded-lg border border-emerald-500/30 text-xs">${item.count} Catches</span>
                        `;
                        luresListEl.appendChild(li);
                    });
                } else {
                    luresListEl.innerHTML = '<li class="p-3 text-slate-400 text-center italic">No lure records logged.</li>';
                }

                document.getElementById('drawer-loading').style.display = 'none';
                document.getElementById('drawer-body').style.display = 'block';
                if (window.initLucideIcons) window.initLucideIcons();
            })
            .catch(err => {
                console.error(err);
                document.getElementById('drawer-loading').style.display = 'none';
                document.getElementById('drawer-body').style.display = 'block';
            });
    }

    function closeLakeDrawer() {
        activeSelectedLakeId = null;
        document.getElementById('explorer-drawer').style.right = '-450px';
    }

    function resetExplorerFilters() {
        document.getElementById('filter-species').value = '';
        document.getElementById('filter-angler').value = '';
        document.getElementById('filter-lure').value = '';
        document.getElementById('filter-trophy').value = '';
        document.getElementById('filter-year').value = '';
        fetchExplorerLakes();
        if (activeSelectedLakeId) {
            loadLakeDrawerDetail(activeSelectedLakeId);
        }
    }
</script>
@endsection
