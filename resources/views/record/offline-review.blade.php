@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="wifi" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Offline Field Sync & Catch Inspection</h1>
                <p class="text-xs text-slate-400">Review pending boat logs, pinpoint GPS coordinates, and server sync history</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.offlineSyncManager.syncNow()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-900/30 transition-all cursor-pointer">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Sync Pending Catches Now</span>
            </button>
            <a href="/record/quick" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                Boat Quick Catch Log ↗
            </a>
        </div>
    </div>

    <!-- Map & Pending Catches Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Interactive Leaflet Map for Pending & Recent Pinpoints -->
        <div class="lg:col-span-2 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-700 font-bold uppercase tracking-wider">
                <span class="flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-teal-600"></i> Catch GPS Map Overview
                </span>
                <span class="text-slate-500 font-mono text-[11px] font-normal">Pending & Recent Pinpoints</span>
            </div>
            <div id="review-sync-map" class="w-full h-[420px] rounded-2xl border border-slate-200 shadow-sm overflow-hidden bg-slate-100"></div>
        </div>

        <!-- Pending Catches Local IndexedDB Queue -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h2 class="font-bold text-slate-900 text-sm">Pending Offline Queue</h2>
                    </div>
                    <span id="pending-count-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">0</span>
                </div>

                <div id="pending-catches-list" class="space-y-2.5 max-h-[300px] overflow-y-auto pr-1">
                    <div class="text-center py-8 text-slate-400 text-xs font-medium">
                        No pending catches stored locally in boat queue.
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                ⛵ Catches logged while offline on the boat are saved in your device browser storage until an internet connection is established.
            </div>
        </div>
    </div>

    <!-- Recently Uploaded Server Catches -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                <h2 class="font-bold text-slate-900 text-sm">Recently Synced Catches</h2>
            </div>
            <span class="text-xs text-slate-500">Last 15 Uploads</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4">Catch Date</th>
                        <th class="py-3 px-4">Angler</th>
                        <th class="py-3 px-4">Lake / Water</th>
                        <th class="py-3 px-4">Species</th>
                        <th class="py-3 px-4">Length / Weight</th>
                        <th class="py-3 px-4">Pinpoint GPS</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($recentCatches as $cat)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-medium">{{ $cat->caught }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $cat->angler->fullName ?? 'Unknown' }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $cat->lake->name ?? 'Unknown' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-teal-700">{{ $cat->fishBreed->name ?? 'Fish' }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <strong>{{ $cat->length }} in.</strong>
                                @if($cat->weight)
                                    <span class="text-slate-400">/ {{ $cat->weight }} lbs.</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px]">
                                @if($cat->latitude && $cat->longitude)
                                    <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                        📍 {{ number_format($cat->latitude, 4) }}, {{ number_format($cat->longitude, 4) }}
                                    </span>
                                @else
                                    <span class="text-slate-400">Lake Default</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="/record/{{ $cat->id }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200">View Detail →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400 text-xs">No catches uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let reviewMap, pendingMarkersGroup = [];

document.addEventListener('DOMContentLoaded', async function () {
    // Initialize Leaflet Map
    reviewMap = L.map('review-sync-map').setView([48.15, -84.85], 9);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 16,
        attribution: 'Tiles &copy; Esri, NRCan CanVec'
    }).addTo(reviewMap);

    // Plot recently synced catches with GPS coordinates
    const recentCatchesData = @json($recentCatches);
    let hasCoords = false;

    recentCatchesData.forEach(cat => {
        if (cat.latitude && cat.longitude) {
            hasCoords = true;
            const marker = L.circleMarker([cat.latitude, cat.longitude], {
                radius: 8,
                fillColor: "#0d9488",
                color: "#ffffff",
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(reviewMap);

            marker.bindPopup(
                `<b>🐟 ${cat.fish_breed ? cat.fish_breed.name : 'Catch'} (${cat.length} in.)</b><br>` +
                `👤 ${cat.angler ? cat.angler.firstName + ' ' + cat.angler.lastName : ''}<br>` +
                `🗓️ ${cat.caught}<br>` +
                `<a href="/record/${cat.id}" class="text-xs font-bold text-teal-600 hover:underline mt-1 inline-block">View Catch →</a>`
            );
        }
    });

    // Render Pending Catches from IndexedDB
    renderPendingQueue();

    // Listen to sync events
    window.addEventListener('online', renderPendingQueue);
});

async function renderPendingQueue() {
    if (!window.offlineSyncManager) return;
    const pending = await window.offlineSyncManager.getPendingCatches();
    const listEl = document.getElementById('pending-catches-list');
    const badgeEl = document.getElementById('pending-count-badge');

    if (badgeEl) badgeEl.textContent = pending.length;

    if (pending.length === 0) {
        listEl.innerHTML = `
            <div class="text-center py-8 text-slate-400 text-xs font-medium">
                🎉 All catches synced! No pending items in boat queue.
            </div>
        `;
        return;
    }

    listEl.innerHTML = '';
    pending.forEach(item => {
        const div = document.createElement('div');
        div.className = 'p-3 bg-amber-50/80 border border-amber-200/90 rounded-xl text-xs space-y-1.5';
        
        let coordsText = item.latitude && item.longitude ? `📍 ${item.latitude.toFixed(4)}, ${item.longitude.toFixed(4)}` : '📍 No GPS Fix';

        div.innerHTML = `
            <div class="flex items-center justify-between">
                <strong class="font-bold text-slate-900">${item.length} in. Catch</strong>
                <span class="text-[10px] font-mono text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded">${item.caught}</span>
            </div>
            <div class="text-[11px] text-slate-600 flex items-center justify-between">
                <span>${coordsText}</span>
                <button onclick="deletePendingItem('${item.client_id}')" class="text-rose-600 hover:underline text-[10px] font-bold">Delete</button>
            </div>
        `;
        listEl.appendChild(div);

        // Plot pending GPS pin on map if present
        if (item.latitude && item.longitude && reviewMap) {
            const pMarker = L.circleMarker([item.latitude, item.longitude], {
                radius: 9,
                fillColor: "#f59e0b",
                color: "#ffffff",
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            }).addTo(reviewMap);

            pMarker.bindPopup(`<b>⛵ Pending Offline Catch</b><br>Length: ${item.length} in.<br>Date: ${item.caught}`);
        }
    });
}

async function deletePendingItem(clientId) {
    if (confirm('Are you sure you want to remove this pending catch from the offline queue?')) {
        await window.offlineSyncManager.removePendingCatch(clientId);
        renderPendingQueue();
    }
}
</script>
@endsection
