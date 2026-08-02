@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-info mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">🗺️ Offline Map Region Downloader</h4>
                    <span class="badge badge-light text-dark">Pre-trip PWA Cache</span>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Pre-download map tiles for your Canadian fishing region while connected to Wi-Fi/Internet. 
                        Once cached, interactive maps and GPS lake location pickers will function 100% offline out on the boat or at the cabin!
                    </p>

                    <!-- Wawa / Highway 17 Preset Region Card -->
                    <div class="card mb-4 border-primary">
                        <div class="card-body bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="text-primary font-weight-bold mb-1">🌲 Wawa, Hawk Junction & White River Region</h5>
                                    <p class="mb-1 text-secondary small">
                                        <strong>Coverage:</strong> Highway 17 Corridor (Lat 47.7°N to 48.7°N, Lng -85.5°W to -84.3°W) — Wawa Lake, Hawk Lake, Magpie River, Dubreuilville & White River waterbodies.
                                    </p>
                                    <span class="badge badge-primary">Zoom 7 – 14</span>
                                    <span class="badge badge-info">Topographic & Waterbody Map</span>
                                    <span class="badge badge-secondary">~3,700 Tiles (~55 MB)</span>
                                </div>
                                <div class="col-md-4 text-right mt-3 mt-md-0">
                                    <button id="btn-download-wawa" onclick="downloadWawaRegion()" class="btn btn-success btn-block font-weight-bold shadow-sm">
                                        📥 Download Wawa Region Pack (~55 MB)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Download Progress & Storage Stats -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="font-weight-bold">📊 Offline Tile Storage Status</h6>
                            <div class="progress mb-3" style="height: 22px;">
                                <div id="tile-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%;">
                                    0%
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span id="storage-count" class="font-weight-bold text-dark">0</span> tiles cached 
                                    (<span id="storage-size" class="text-muted">0 MB</span>)
                                </div>
                                <div>
                                    <button onclick="clearTileCache()" class="btn btn-outline-danger btn-sm">
                                        🗑️ Clear Cached Map Tiles
                                    </button>
                                </div>
                            </div>
                            <div id="download-status-msg" class="small text-info mt-2"></div>
                        </div>
                    </div>

                    <!-- Interactive Region Preview Map -->
                    <div class="card">
                        <div class="card-header bg-light font-weight-bold">
                            🗺️ Map Coverage Preview & Layer Controls
                        </div>
                        <div class="card-body p-0">
                            <div id="offline-preview-map" style="height: 450px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let previewMap;
    let topoLayer;
    let satLayer;

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Leaflet Map centered over Wawa / Hawk Junction
        previewMap = L.map('offline-preview-map').setView([48.15, -84.85], 9);

        // ESRI Topo Layer (NRCan CanVec hydrography)
        topoLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Tiles &copy; Esri, NRCan CanVec Hydrography'
        }).addTo(previewMap);

        // ESRI Satellite Layer
        satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Source: Esri, Maxar, Earthstar Geographics'
        });

        // Layer Control Switcher
        L.control.layers({
            "🗺️ Topo / Waterbody": topoLayer,
            "🛰️ Satellite Imagery": satLayer
        }).addTo(previewMap);

        // Draw Bounding Box Rectangle for Wawa / Highway 17 Region
        const bounds = [[47.7, -85.5], [48.7, -84.3]];
        L.rectangle(bounds, { color: "#007bff", weight: 2, fillOpacity: 0.15 }).addTo(previewMap);
        previewMap.fitBounds(bounds);

        updateStorageStats();
    });

    async function updateStorageStats() {
        if (!('caches' in window)) return;
        try {
            const cache = await caches.open('fishinglog-map-tiles-v1');
            const keys = await cache.keys();
            document.getElementById('storage-count').innerText = keys.length.toLocaleString();

            // Estimate size (~15KB per tile)
            const estMB = (keys.length * 15 / 1024).toFixed(1);
            document.getElementById('storage-size').innerText = estMB + ' MB';
        } catch (e) {
            console.error(e);
        }
    }

    async function downloadWawaRegion() {
        if (!('caches' in window)) {
            alert('CacheStorage is not supported on this browser.');
            return;
        }

        const btn = document.getElementById('btn-download-wawa');
        const progressBar = document.getElementById('tile-progress-bar');
        const statusMsg = document.getElementById('download-status-msg');

        btn.disabled = true;
        btn.innerText = '⏳ Downloading Wawa Pack...';

        // Bounding Box: Lat 47.7 to 48.7, Lng -85.5 to -84.3, Zoom 7 to 14
        const minLat = 47.7, maxLat = 48.7;
        const minLng = -85.5, maxLng = -84.3;
        const minZoom = 7, maxZoom = 14;

        const urls = [];
        const baseUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/';

        for (let z = minZoom; z <= maxZoom; z++) {
            const minTile = latLngToTile(maxLat, minLng, z);
            const maxTile = latLngToTile(minLat, maxLng, z);

            for (let x = minTile.x; x <= maxTile.x; x++) {
                for (let y = minTile.y; y <= maxTile.y; y++) {
                    urls.push(`${baseUrl}${z}/${y}/${x}`);
                }
            }
        }

        const total = urls.length;
        let completed = 0;
        const cache = await caches.open('fishinglog-map-tiles-v1');

        statusMsg.innerText = `Starting download of ${total.toLocaleString()} map tiles...`;

        // Batch download tiles concurrently (5 requests at a time)
        const batchSize = 6;
        for (let i = 0; i < urls.length; i += batchSize) {
            const batch = urls.slice(i, i + batchSize);
            await Promise.all(batch.map(async (url) => {
                try {
                    const match = await cache.match(url);
                    if (!match) {
                        const res = await fetch(url, { mode: 'cors' });
                        if (res.ok || res.type === 'opaque') {
                            await cache.put(url, res);
                        }
                    }
                } catch (err) {
                    console.log('Tile fetch error:', err);
                }
                completed++;
            }));

            const percent = Math.min(100, Math.round((completed / total) * 100));
            progressBar.style.width = percent + '%';
            progressBar.innerText = percent + '%';
            statusMsg.innerText = `Cached ${completed.toLocaleString()} of ${total.toLocaleString()} tiles...`;
            await updateStorageStats();
        }

        btn.disabled = false;
        btn.innerText = '✅ Wawa Pack Cached Offline!';
        statusMsg.innerText = `🎉 Successfully pre-cached ${total.toLocaleString()} map tiles for offline boat use!`;
        alert('🎉 Wawa / Highway 17 Map Pack successfully downloaded for offline use!');
    }

    async function clearTileCache() {
        if (!confirm('Are you sure you want to clear all cached map tiles?')) return;
        if ('caches' in window) {
            await caches.delete('fishinglog-map-tiles-v1');
            document.getElementById('tile-progress-bar').style.width = '0%';
            document.getElementById('tile-progress-bar').innerText = '0%';
            document.getElementById('download-status-msg').innerText = 'Map cache cleared.';
            document.getElementById('btn-download-wawa').innerText = '📥 Download Wawa Region Pack (~55 MB)';
            updateStorageStats();
        }
    }

    // Helper: Convert Lat/Lng/Zoom to Tile X/Y coordinates
    function latLngToTile(lat, lng, zoom) {
        const rad = lat * Math.PI / 180;
        const n = Math.pow(2, zoom);
        const x = Math.floor((lng + 180) / 360 * n);
        const y = Math.floor((1 - Math.log(Math.tan(rad) + 1 / Math.cos(rad)) / Math.PI) / 2 * n);
        return { x: Math.max(0, x), y: Math.max(0, y) };
    }
</script>
@endsection
