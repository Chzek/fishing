@extends('layouts.app')

@section('content')
<div class="relative w-full bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden" style="height: calc(100dvh - 135px); min-height: 520px;">
    <!-- Leaflet Map Container -->
    <div id="explorer-map" class="w-full h-full z-0"></div>

    <!-- In-Map Floating Header & Filter Toolbar (Telemetry v2 Option C) -->
    <div id="explorer-floating-panel" class="absolute top-3 left-3 right-3 z-20 bg-slate-900/95 backdrop-blur-md border border-slate-700/80 text-white rounded-2xl shadow-2xl p-2.5 sm:p-3 transition-all duration-200">
        <!-- Top Toolbar Header Row -->
        <div class="flex items-center justify-between gap-2">
            <!-- Title & Active View Badge -->
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-teal-500/15 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                    <x-lucide-compass class="w-4 h-4" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-xs sm:text-sm font-bold text-white tracking-tight flex items-center gap-1.5 truncate">
                        <span>Lake Explorer</span>
                        <span id="lake-count-badge" class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-full shrink-0">0 Lakes</span>
                    </h1>
                </div>
            </div>

            <!-- Mobile Controls Action Buttons -->
            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Mobile Filter Toggle Button (lg:hidden) -->
                <button type="button" id="mobile-filter-toggle-btn" onclick="toggleMobileFilters()" class="lg:hidden h-8 px-2.5 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors flex items-center gap-1.5">
                    <x-lucide-sliders-horizontal class="w-3.5 h-3.5 text-teal-400" />
                    <span>Filters</span>
                    <span id="mobile-filter-count-badge" class="hidden px-1.5 py-0.2 bg-teal-400 text-slate-950 font-black text-[10px] rounded-full">0</span>
                </button>

                <!-- Reset Button -->
                <button type="button" onclick="resetExplorerFilters()" class="h-8 px-2.5 text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors flex items-center gap-1">
                    <x-lucide-rotate-ccw class="w-3.5 h-3.5 text-slate-400" />
                    <span class="hidden sm:inline">Reset</span>
                </button>
            </div>
        </div>

        <!-- Desktop Inline Filter Row (lg:flex) -->
        <form id="explorer-filter-form" class="hidden lg:flex items-center gap-2 mt-2.5 pt-2.5 border-t border-slate-800/80">
            <!-- Species Filter -->
            <div class="flex-1 min-w-0">
                <select id="filter-species" class="w-full h-8 px-2.5 text-xs rounded-xl border border-slate-700 bg-slate-800/90 text-slate-200 font-medium focus:ring-1 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">🐟 All Species</option>
                    @foreach($fishBreeds as $breed)
                        <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Angler Filter -->
            <div class="flex-1 min-w-0">
                <select id="filter-angler" class="w-full h-8 px-2.5 text-xs rounded-xl border border-slate-700 bg-slate-800/90 text-slate-200 font-medium focus:ring-1 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">👨‍🌾 All Anglers</option>
                    @foreach($anglers as $angler)
                        <option value="{{ $angler->id }}">{{ trim($angler->firstname . ' ' . $angler->lastname) ?: 'Angler #' . $angler->id }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Lure Filter -->
            <div class="flex-1 min-w-0">
                <select id="filter-lure" class="w-full h-8 px-2.5 text-xs rounded-xl border border-slate-700 bg-slate-800/90 text-slate-200 font-medium focus:ring-1 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">🎣 All Lures</option>
                    @foreach($lures as $lure)
                        <option value="{{ $lure->id }}">{{ $lure->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Trophy Filter -->
            <div class="flex-1 min-w-0">
                <select id="filter-trophy" class="w-full h-8 px-2.5 text-xs rounded-xl border border-slate-700 bg-slate-800/90 text-slate-200 font-medium focus:ring-1 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">🏆 All Catches</option>
                    <option value="1">🏆 Trophies (≥10lbs / ≥20in)</option>
                </select>
            </div>

            <!-- Season Year Filter -->
            <div class="flex-1 min-w-0">
                <select id="filter-year" class="w-full h-8 px-2.5 text-xs rounded-xl border border-slate-700 bg-slate-800/90 text-slate-200 font-medium focus:ring-1 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">📅 All Seasons</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}">{{ $yr }} Season</option>
                    @endforeach
                </select>
            </div>
        </form>

        <!-- Mobile Expandable Filter Overlay Panel (lg:hidden) -->
        <div id="mobile-filter-drawer" class="hidden lg:hidden mt-3 pt-3 border-t border-slate-800 space-y-2.5 max-h-[60vh] overflow-y-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label for="m-filter-species" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Species</label>
                    <select id="m-filter-species" onchange="syncMobileFilter('filter-species', this.value)" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-700 bg-slate-800 text-slate-200 font-medium">
                        <option value="">🐟 All Species</option>
                        @foreach($fishBreeds as $breed)
                            <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="m-filter-angler" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Angler</label>
                    <select id="m-filter-angler" onchange="syncMobileFilter('filter-angler', this.value)" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-700 bg-slate-800 text-slate-200 font-medium">
                        <option value="">👨‍🌾 All Anglers</option>
                        @foreach($anglers as $angler)
                            <option value="{{ $angler->id }}">{{ trim($angler->firstname . ' ' . $angler->lastname) ?: 'Angler #' . $angler->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="m-filter-lure" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Lure</label>
                    <select id="m-filter-lure" onchange="syncMobileFilter('filter-lure', this.value)" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-700 bg-slate-800 text-slate-200 font-medium">
                        <option value="">🎣 All Lures</option>
                        @foreach($lures as $lure)
                            <option value="{{ $lure->id }}">{{ $lure->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="m-filter-trophy" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Trophy Status</label>
                    <select id="m-filter-trophy" onchange="syncMobileFilter('filter-trophy', this.value)" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-700 bg-slate-800 text-slate-200 font-medium">
                        <option value="">🏆 All Catches</option>
                        <option value="1">🏆 Trophies (≥10lbs / ≥20in)</option>
                    </select>
                </div>

                <div>
                    <label for="m-filter-year" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Season Year</label>
                    <select id="m-filter-year" onchange="syncMobileFilter('filter-year', this.value)" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-700 bg-slate-800 text-slate-200 font-medium">
                        <option value="">📅 All Seasons</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}">{{ $yr }} Season</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Done / Dismiss Button for Mobile -->
            <div class="pt-1 flex gap-2">
                <button type="button" onclick="toggleMobileFilters()" class="w-full py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl text-center shadow transition-colors">
                    Done & View Lakes
                </button>
            </div>
        </div>
    </div>

    <!-- Right Slide-Over Detail Drawer (Dark Option C Styling) -->
    <div id="explorer-drawer" class="absolute top-0 right-0 w-full sm:w-[420px] max-w-full h-full bg-slate-900 text-slate-200 shadow-2xl border-l border-slate-800 z-30 transition-transform duration-300 ease-in-out overflow-y-auto pointer-events-auto" style="transform: translateX(100%);">
        <div class="p-4 bg-slate-950/90 border-b border-slate-800 flex items-center justify-between sticky top-0 backdrop-blur-md z-10">
            <div>
                <h2 id="drawer-lake-name" class="font-bold text-white text-base tracking-tight truncate max-w-[280px]">Lake Detail</h2>
                <span id="drawer-lake-sub" class="text-xs text-teal-400 font-medium">Lake Catch Analytics</span>
            </div>
            <button type="button" onclick="closeLakeDrawer()" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <x-lucide-x class="w-5 h-5" />
            </button>
        </div>

        <div id="drawer-loading" class="text-center py-12">
            <x-lucide-loader-2 class="w-8 h-8 text-teal-400 animate-spin mx-auto" />
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
                    <x-lucide-trophy class="w-4 h-4" />
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
                    <x-lucide-fish class="w-3.5 h-3.5 text-teal-400" />
                    <span>Species Breakdown</span>
                </div>
                <ul id="drawer-species-list" class="divide-y divide-slate-700/50 text-xs">
                    <li class="p-3 text-slate-400 text-center italic">No catches recorded for active filter.</li>
                </ul>
            </div>

            <!-- Top Producing Lures -->
            <div class="bg-slate-800/60 rounded-xl border border-slate-700/50 overflow-hidden">
                <div class="px-4 py-2.5 bg-slate-800 border-b border-slate-700/60 font-bold text-xs text-slate-300 uppercase tracking-wider flex items-center gap-2">
                    <x-lucide-fishing-hook class="w-3.5 h-3.5 text-teal-400" />
                    <span>Top Producing Lures</span>
                </div>
                <ul id="drawer-lures-list" class="divide-y divide-slate-700/50 text-xs">
                    <li class="p-3 text-slate-400 text-center italic">No lure records logged.</li>
                </ul>
            </div>

            <!-- Quick Action Buttons -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <a id="drawer-quick-catch-btn" href="#" class="py-2.5 px-3 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl text-center shadow transition-colors flex items-center justify-center gap-1">
                    <x-lucide-zap class="w-3.5 h-3.5" />
                    <span>Quick Catch</span>
                </a>
                <a id="drawer-full-log-btn" href="#" class="py-2.5 px-3 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs rounded-xl text-center transition-colors flex items-center justify-center gap-1">
                    <span>Full Log</span>
                    <x-lucide-external-link class="w-3.5 h-3.5" />
                </a>
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
        explorerMap = L.map('explorer-map', {
            zoomControl: false
        }).setView([48.15, -84.85], 9);

        // Zoom Control repositioned to bottom-right for clean UI
        L.control.zoom({ position: 'bottomright' }).addTo(explorerMap);

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
            "🗺️ Topo": topoLayer,
            "🛰️ Satellite": satLayer
        }, null, { position: 'bottomleft' }).addTo(explorerMap);

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

        // Prevent Leaflet touch & click propagation on floating panels
        const floatingPanel = document.getElementById('explorer-floating-panel');
        const explorerDrawer = document.getElementById('explorer-drawer');
        if (floatingPanel) {
            L.DomEvent.disableScrollPropagation(floatingPanel);
            L.DomEvent.disableClickPropagation(floatingPanel);
        }
        if (explorerDrawer) {
            L.DomEvent.disableScrollPropagation(explorerDrawer);
            L.DomEvent.disableClickPropagation(explorerDrawer);
        }

        // Listen for map pan/zoom events to query visible bounding box
        explorerMap.on('moveend zoomend', function () {
            debouncedFetchExplorerLakes();
        });

        // Listen for desktop filter dropdown changes
        document.querySelectorAll('#explorer-filter-form select').forEach(select => {
            select.addEventListener('change', function () {
                syncDesktopFilter(this.id, this.value);
                fetchExplorerLakes();
                if (activeSelectedLakeId) {
                    loadLakeDrawerDetail(activeSelectedLakeId);
                }
            });
        });

        // Initial fetch
        fetchExplorerLakes();
        updateActiveFilterBadge();
    });

    function toggleMobileFilters() {
        const drawer = document.getElementById('mobile-filter-drawer');
        const btn = document.getElementById('mobile-filter-toggle-btn');
        if (drawer.classList.contains('hidden')) {
            drawer.classList.remove('hidden');
            btn.classList.add('bg-teal-600', 'text-white', 'border-teal-500');
            btn.classList.remove('bg-slate-800', 'text-slate-200', 'border-slate-700');
        } else {
            drawer.classList.add('hidden');
            btn.classList.remove('bg-teal-600', 'text-white', 'border-teal-500');
            btn.classList.add('bg-slate-800', 'text-slate-200', 'border-slate-700');
        }
        if (window.initLucideIcons) window.initLucideIcons();
    }

    function syncMobileFilter(targetDesktopId, value) {
        const desktopEl = document.getElementById(targetDesktopId);
        if (desktopEl) {
            desktopEl.value = value;
        }
        updateActiveFilterBadge();
        fetchExplorerLakes();
        if (activeSelectedLakeId) {
            loadLakeDrawerDetail(activeSelectedLakeId);
        }
    }

    function syncDesktopFilter(desktopId, value) {
        const mobileIdMap = {
            'filter-species': 'm-filter-species',
            'filter-angler': 'm-filter-angler',
            'filter-lure': 'm-filter-lure',
            'filter-trophy': 'm-filter-trophy',
            'filter-year': 'm-filter-year'
        };
        const mobileId = mobileIdMap[desktopId];
        if (mobileId) {
            const mobileEl = document.getElementById(mobileId);
            if (mobileEl) mobileEl.value = value;
        }
        updateActiveFilterBadge();
    }

    function updateActiveFilterBadge() {
        const species = document.getElementById('filter-species').value;
        const angler = document.getElementById('filter-angler').value;
        const lure = document.getElementById('filter-lure').value;
        const trophy = document.getElementById('filter-trophy').value;
        const year = document.getElementById('filter-year').value;

        let activeCount = 0;
        if (species) activeCount++;
        if (angler) activeCount++;
        if (lure) activeCount++;
        if (trophy) activeCount++;
        if (year) activeCount++;

        const badge = document.getElementById('mobile-filter-count-badge');
        if (badge) {
            if (activeCount > 0) {
                badge.innerText = activeCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }

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
                document.getElementById('lake-count-badge').innerText = `${lakes.length} Lake${lakes.length === 1 ? '' : 's'}`;

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

        const qCatchBtn = document.getElementById('drawer-quick-catch-btn');
        qCatchBtn.href = `/record/quick?lakes_id=${lake.id}`;
        qCatchBtn.onclick = (e) => {
            e.preventDefault();
            window.Livewire?.dispatch('open-quick-catch', {
                lake_id: lake.id,
                latitude: lake.latitude,
                longitude: lake.longitude
            });
        };
        document.getElementById('drawer-full-log-btn').href = `/lake/${lake.id}`;

        // Slide drawer in from right
        drawer.style.transform = 'translateX(0%)';

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
        const drawer = document.getElementById('explorer-drawer');
        if (drawer) {
            drawer.style.transform = 'translateX(100%)';
        }
    }

    function resetExplorerFilters() {
        document.getElementById('filter-species').value = '';
        document.getElementById('filter-angler').value = '';
        document.getElementById('filter-lure').value = '';
        document.getElementById('filter-trophy').value = '';
        document.getElementById('filter-year').value = '';

        const mSpecies = document.getElementById('m-filter-species');
        const mAngler = document.getElementById('m-filter-angler');
        const mLure = document.getElementById('m-filter-lure');
        const mTrophy = document.getElementById('m-filter-trophy');
        const mYear = document.getElementById('m-filter-year');
        if (mSpecies) mSpecies.value = '';
        if (mAngler) mAngler.value = '';
        if (mLure) mLure.value = '';
        if (mTrophy) mTrophy.value = '';
        if (mYear) mYear.value = '';

        updateActiveFilterBadge();
        fetchExplorerLakes();
        if (activeSelectedLakeId) {
            loadLakeDrawerDetail(activeSelectedLakeId);
        }
    }

    // Reactively refresh explorer markers when a catch is saved
    window.addEventListener('catch-saved', () => {
        fetchExplorerLakes();
        if (activeSelectedLakeId) {
            loadLakeDrawerDetail(activeSelectedLakeId);
        }
    });
</script>
@endsection
