@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Filter Toolbar Header -->
    <div class="card shadow-sm border-info mb-3">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center">
                <div class="col-md-3 mb-2 mb-md-0">
                    <h5 class="mb-0 text-primary font-weight-bold">
                        🧭 Lake Explorer
                        <span id="lake-count-badge" class="badge badge-info ml-2">0 Lakes in View</span>
                    </h5>
                </div>
                <div class="col-md-9">
                    <form id="explorer-filter-form" class="form-inline justify-content-md-end">
                        <!-- Species Filter -->
                        <div class="form-group mr-2 mb-1">
                            <select id="filter-species" class="form-control form-control-sm">
                                <option value="">🐟 All Species</option>
                                @foreach($fishBreeds as $breed)
                                    <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Angler Filter -->
                        <div class="form-group mr-2 mb-1">
                            <select id="filter-angler" class="form-control form-control-sm">
                                <option value="">👨‍🌾 All Anglers</option>
                                @foreach($anglers as $angler)
                                    <option value="{{ $angler->id }}">{{ trim($angler->firstname . ' ' . $angler->lastname) ?: 'Angler #' . $angler->id }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Lure Filter -->
                        <div class="form-group mr-2 mb-1">
                            <select id="filter-lure" class="form-control form-control-sm">
                                <option value="">🎣 All Lures</option>
                                @foreach($lures as $lure)
                                    <option value="{{ $lure->id }}">{{ $lure->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Trophy Filter -->
                        <div class="form-group mr-2 mb-1">
                            <select id="filter-trophy" class="form-control form-control-sm">
                                <option value="">🏆 All Catches</option>
                                <option value="1">🏆 Trophies Only (≥10lbs or ≥20in)</option>
                            </select>
                        </div>

                        <!-- Season Year Filter -->
                        <div class="form-group mr-2 mb-1">
                            <select id="filter-year" class="form-control form-control-sm">
                                <option value="">📅 All Seasons</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}">{{ $yr }} Season</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" onclick="resetExplorerFilters()" class="btn btn-outline-secondary btn-sm mb-1">
                            🔄 Reset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Explorer Map Container -->
    <div class="card shadow-sm border-0 position-relative overflow-hidden" style="height: calc(100vh - 180px); min-height: 520px;">
        <div id="explorer-map" style="height: 100%; width: 100%;"></div>

        <!-- Right Slide-Over Detail Drawer -->
        <div id="explorer-drawer" class="position-absolute bg-white shadow-lg border-left" style="top: 0; right: -450px; width: 440px; height: 100%; z-index: 1050; transition: right 0.3s ease-in-out; overflow-y: auto;">
            <div class="p-3 bg-primary text-white d-flex justify-content-between align-items-center sticky-top">
                <div>
                    <h5 id="drawer-lake-name" class="mb-0 font-weight-bold">Lake Detail</h5>
                    <small id="drawer-lake-sub" class="text-light">Lake Catch Analytics</small>
                </div>
                <button type="button" onclick="closeLakeDrawer()" class="close text-white opacity-100" style="font-size: 1.8rem; line-height: 1;">&times;</button>
            </div>

            <div id="drawer-loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Loading lake analytics...</p>
            </div>

            <div id="drawer-body" class="p-3" style="display: none;">
                <!-- Lake Badges & Coordinates -->
                <div class="mb-3">
                    <span id="drawer-coords-badge" class="badge badge-success p-2 mr-1 mb-1">📍 Coordinates</span>
                    <span id="drawer-terrain-badge" class="badge badge-info p-2 mr-1 mb-1" style="display: none;">🌊 Terrain</span>
                    <span id="drawer-depth-badge" class="badge badge-secondary p-2 mr-1 mb-1" style="display: none;">📏 Depth</span>
                </div>

                <!-- Catch & Visit Stats Grid -->
                <div class="row text-center mb-3">
                    <div class="col-4">
                        <div class="p-2 bg-light rounded border">
                            <h4 id="drawer-stat-catches" class="font-weight-bold text-primary mb-0">0</h4>
                            <small class="text-muted font-weight-bold">Catches</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded border">
                            <h4 id="drawer-stat-visits" class="font-weight-bold text-success mb-0">0</h4>
                            <small class="text-muted font-weight-bold">Visits</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded border">
                            <h4 id="drawer-stat-anglers" class="font-weight-bold text-info mb-0">0</h4>
                            <small class="text-muted font-weight-bold">Anglers</small>
                        </div>
                    </div>
                </div>

                <!-- Record Highlights (Longest & Fattest) -->
                <div class="card mb-3 border-warning">
                    <div class="card-header bg-warning text-dark font-weight-bold py-1 px-2">
                        🏆 Lake Trophy Records
                    </div>
                    <div class="card-body p-2">
                        <div class="row text-center">
                            <div class="col-6 border-right">
                                <small class="text-muted font-weight-bold">Longest Catch</small>
                                <h5 id="drawer-longest-val" class="font-weight-bold text-dark mb-0">--</h5>
                                <small id="drawer-longest-breed" class="text-secondary">--</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted font-weight-bold">Fattest Catch</small>
                                <h5 id="drawer-fattest-val" class="font-weight-bold text-dark mb-0">--</h5>
                                <small id="drawer-fattest-breed" class="text-secondary">--</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Species Catch Breakdown List -->
                <div class="card mb-3">
                    <div class="card-header bg-light font-weight-bold py-2">
                        🐟 Species Caught Breakdown
                    </div>
                    <ul id="drawer-species-list" class="list-group list-group-flush">
                        <li class="list-group-item text-muted text-center py-3">No catches recorded for active filter.</li>
                    </ul>
                </div>

                <!-- Top Producing Lures -->
                <div class="card mb-3">
                    <div class="card-header bg-light font-weight-bold py-2">
                        🎣 Top Producing Lures
                    </div>
                    <ul id="drawer-lures-list" class="list-group list-group-flush">
                        <li class="list-group-item text-muted text-center py-3">No lure records.</li>
                    </ul>
                </div>

                <!-- Quick Action Buttons -->
                <div class="d-flex justify-content-between mt-4 mb-2">
                    <a id="drawer-quick-catch-btn" href="#" class="btn btn-success btn-block font-weight-bold mr-2 shadow-sm">
                        ⚡ Quick Catch Here
                    </a>
                    <a id="drawer-full-log-btn" href="#" class="btn btn-outline-primary btn-block font-weight-bold mt-0 shadow-sm">
                        View Full Log ↗
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

        L.control.layers({
            "🗺️ Topo / Waterbody": topoLayer,
            "🛰️ Satellite Imagery": satLayer
        }).addTo(explorerMap);

        markersLayer = L.layerGroup().addTo(explorerMap);

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
                        li.className = 'list-group-item d-flex justify-content-between align-items-center py-2 px-3';
                        const breedName = item.fish_breed ? item.fish_breed.name : 'Unknown Species';
                        const avgW = item.avg_weight ? `${item.avg_weight} lbs` : 'unweighed';
                        li.innerHTML = `
                            <div>
                                <strong>🐟 ${breedName}</strong>
                                <small class="d-block text-muted">Avg: ${item.avg_length} in. | ${avgW}</small>
                            </div>
                            <span class="badge badge-primary badge-pill font-weight-bold" style="font-size: 0.9rem;">${item.count}</span>
                        `;
                        speciesListEl.appendChild(li);
                    });
                } else {
                    speciesListEl.innerHTML = '<li class="list-group-item text-muted text-center py-3">No catches recorded for active filter.</li>';
                }

                // Top Lures List
                const luresListEl = document.getElementById('drawer-lures-list');
                luresListEl.innerHTML = '';
                if (data.top_lures && data.top_lures.length > 0) {
                    data.top_lures.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center py-2 px-3';
                        const lureName = item.lure ? item.lure.name : 'Unknown Lure';
                        li.innerHTML = `
                            <span>🎣 <strong>${lureName}</strong></span>
                            <span class="badge badge-success badge-pill">${item.count} Catches</span>
                        `;
                        luresListEl.appendChild(li);
                    });
                } else {
                    luresListEl.innerHTML = '<li class="list-group-item text-muted text-center py-3">No lure records logged.</li>';
                }

                document.getElementById('drawer-loading').style.display = 'none';
                document.getElementById('drawer-body').style.display = 'block';
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
