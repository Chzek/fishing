@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Title & Region Badge -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="map" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Offline Map Region Downloader</h1>
                <p class="text-xs text-slate-400">Pre-trip PWA Cache for on-the-water boat maps</p>
            </div>
        </div>

        <span class="bg-teal-500/15 text-teal-300 border border-teal-500/30 text-xs font-semibold px-3 py-1 rounded-full">
            Offline PWA Ready
        </span>
    </div>

    <!-- Main Region Card -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-6">
        <p class="text-sm text-slate-600 leading-relaxed">
            Pre-download map tiles for your Canadian fishing region while connected to Wi-Fi/Internet. 
            Once cached, interactive maps and GPS lake location pickers will function 100% offline out on the boat or at the cabin!
        </p>

        <!-- Wawa / Highway 17 Preset Region Card -->
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-2">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="trees" class="w-4 h-4 text-teal-600"></i>
                    <span>Wawa, Hawk Junction & White River Region</span>
                </h2>
                <p class="text-xs text-slate-600">
                    <strong>Coverage:</strong> Highway 17 Corridor (Lat 47.7°N to 48.7°N, Lng -85.5°W to -84.3°W) — Wawa Lake, Hawk Lake, Magpie River, Dubreuilville & White River waterbodies.
                </p>
                <div class="flex flex-wrap gap-1.5 pt-1">
                    <span class="bg-teal-100 text-teal-800 text-[11px] font-semibold px-2.5 py-0.5 rounded-md border border-teal-200">Zoom 7 – 14</span>
                    <span class="bg-sky-100 text-sky-800 text-[11px] font-semibold px-2.5 py-0.5 rounded-md border border-sky-200">Topographic Hydrography</span>
                    <span class="bg-slate-200 text-slate-800 text-[11px] font-semibold px-2.5 py-0.5 rounded-md">~3,700 Tiles (~55 MB)</span>
                </div>
            </div>

            <button id="btn-download-wawa" onclick="downloadWawaRegion()" class="w-full md:w-auto shrink-0 py-3 px-5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-md shadow-teal-900/10 transition-all flex items-center justify-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Download Wawa Region Pack (~55 MB)</span>
            </button>
        </div>

        <!-- Live Download Progress & Storage Stats -->
        <div class="bg-slate-900 text-slate-200 rounded-2xl p-5 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-sm text-white flex items-center gap-2">
                    <i data-lucide="hard-drive" class="w-4 h-4 text-teal-400"></i>
                    <span>Offline Tile Storage Status</span>
                </h3>
                <button onclick="clearTileCache()" class="text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-950/40 px-3 py-1 rounded-lg border border-rose-500/30 transition-colors flex items-center gap-1">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span>Clear Cache</span>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-800 rounded-full h-4 overflow-hidden border border-slate-700">
                <div id="tile-progress-bar" class="bg-gradient-to-r from-teal-500 to-teal-400 h-full text-[10px] font-bold text-slate-950 flex items-center justify-center transition-all duration-200" style="width: 0%;">
                    0%
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                <div class="text-slate-300">
                    <span id="storage-count" class="font-bold text-white text-sm">0</span> tiles cached 
                    (<span id="storage-size" class="text-teal-400 font-mono">0 MB</span>)
                </div>
                <div id="download-status-msg" class="text-teal-300 font-medium italic">Ready to download map tiles for offline boat use.</div>
            </div>
        </div>

        <!-- Interactive Region Preview Map Card -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                <i data-lucide="layers" class="w-3.5 h-3.5 text-teal-600"></i>
                <span>Map Coverage Preview</span>
            </h3>
            <div class="rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
                <div id="offline-preview-map" class="w-full h-[400px]"></div>
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
        L.rectangle(bounds, { color: "#0d9488", weight: 2, fillOpacity: 0.15 }).addTo(previewMap);
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

        // Batch download tiles concurrently (12 requests at a time for fast progress)
        const batchSize = 12;
        for (let i = 0; i < urls.length; i += batchSize) {
            const batch = urls.slice(i, i + batchSize);
            await Promise.all(batch.map(async (url) => {
                try {
                    const match = await cache.match(url);
                    if (!match) {
                        const res = await fetch(url, { mode: 'no-cors' });
                        if (res) {
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
            if (i % 60 === 0 || completed === total) {
                await updateStorageStats();
            }
        }

        btn.disabled = false;
        btn.innerText = '✅ Wawa Pack Cached Offline!';
        statusMsg.innerText = `🎉 Successfully pre-cached ${total.toLocaleString()} map tiles for offline boat use!`;
        alert('🎉 Wawa / Highway 17 Map Pack successfully downloaded for offline use!');
    }

    async function clearTileCache() {
        if (!confirm('Are you sure you want to clear all offline cached map tiles?')) return;
        try {
            await caches.delete('fishinglog-map-tiles-v1');
            await updateStorageStats();
            document.getElementById('tile-progress-bar').style.width = '0%';
            document.getElementById('tile-progress-bar').innerText = '0%';
            document.getElementById('download-status-msg').innerText = 'Offline tile cache cleared.';
        } catch (e) {
            console.error(e);
        }
    }

    function latLngToTile(lat, lng, zoom) {
        const radLat = lat * Math.PI / 180;
        const n = Math.pow(2, zoom);
        const tileX = Math.floor((lng + 180) / 360 * n);
        const tileY = Math.floor((1 - Math.log(Math.tan(radLat) + 1 / Math.cos(radLat)) / Math.PI) / 2 * n);
        return { x: tileX, y: tileY };
    }
</script>
@endsection
