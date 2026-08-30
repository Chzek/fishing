@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Title & Network Mode Bar -->
    <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-md border border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="zap" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="font-bold text-white text-lg tracking-tight leading-tight">Boat Quick Catch Log</h1>
                <p class="text-xs text-slate-400">Tactile on-the-water field logger</p>
            </div>
        </div>

        <span id="network-status" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
            Checking connection...
        </span>
    </div>

    <!-- Quick Catch Form Container -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div id="quick-catch-alert" class="hidden text-xs font-bold p-4 rounded-xl shadow-sm border transition-all" role="alert"></div>

        <form id="quickCatchForm" class="space-y-5">
            @csrf
            <input type="hidden" id="client_id" name="client_id">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <!-- GPS Status Banner -->
            <div id="gps-status-box" class="flex items-center justify-between px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <i data-lucide="map-pin" class="w-4 h-4 text-teal-600 shrink-0"></i>
                    <span id="gps-status-text" class="font-medium">Device Pinpoint GPS: Searching...</span>
                </div>
                <button type="button" onclick="acquireQuickGPS()" class="text-[11px] font-bold text-teal-600 hover:text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">Re-query</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Angler Select -->
                <div class="space-y-1.5">
                    <label for="anglers_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Angler</label>
                    <select id="anglers_id" name="anglers_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-medium text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors" required>
                        <option value="">Select Angler...</option>
                        @foreach($anglers as $angler)
                            <option value="{{ $angler->id }}">{{ $angler->fullName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Lake Select -->
                <div class="space-y-1.5">
                    <label for="lakes_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lake / Water</label>
                    <select id="lakes_id" name="lakes_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-medium text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors" required>
                        <option value="">Select Lake...</option>
                        @foreach($lakes as $lake)
                            <option value="{{ $lake->id }}">{{ $lake->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Species Select -->
                <div class="space-y-1.5">
                    <label for="fish_breeds_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Fish Species</label>
                    <select id="fish_breeds_id" name="fish_breeds_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-medium text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors" required>
                        <option value="">Select Fish Species...</option>
                        @foreach($fishBreeds as $breed)
                            <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Lure Select -->
                <div class="space-y-1.5">
                    <label for="lures_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lure / Bait (Optional)</label>
                    <select id="lures_id" name="lures_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-medium text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                        <option value="">Select Lure...</option>
                        @php
                            $lureGroups = is_iterable($lures) ? collect($lures)->groupBy(fn($item) => $item->category ?: 'Other') : collect();
                        @endphp
                        @foreach($lureGroups as $categoryName => $groupedLures)
                            <optgroup label="── {{ $categoryName }} ──">
                                @foreach($groupedLures as $lure)
                                    <option value="{{ $lure->id }}">{{ $lure->displayName ?? $lure->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Length Input -->
                <div class="space-y-1.5">
                    <label for="length" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Length (Inches)</label>
                    <div class="relative">
                        <input type="number" step="0.25" id="length" name="length" class="w-full h-11 pl-3.5 pr-12 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono font-bold text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors" placeholder="e.g. 18.5" required>
                        <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">in.</span>
                    </div>
                </div>

                <!-- Weight Input -->
                <div class="space-y-1.5">
                    <label for="weight" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Weight (Pounds - Optional)</label>
                    <div class="relative">
                        <input type="number" step="0.1" id="weight" name="weight" class="w-full h-11 pl-3.5 pr-12 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono font-bold text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors" placeholder="e.g. 4.2">
                        <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">lbs.</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2 items-center">
                <!-- Date Input -->
                <div class="space-y-1.5">
                    <label for="caught" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Date Caught</label>
                    <input type="date" id="caught" name="caught" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-medium text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Released Toggle Checkbox -->
                <div class="pt-4 md:pt-6">
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-emerald-100 bg-emerald-50/60 hover:bg-emerald-50 transition-colors">
                        <input type="checkbox" id="released" name="released" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-emerald-300" checked>
                        <div class="flex items-center gap-2">
                            <i data-lucide="heart" class="w-4 h-4 text-emerald-600"></i>
                            <span class="text-sm font-bold text-emerald-800">Released Catch</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="pt-4">
                <button type="submit" id="saveCatchBtn" class="w-full py-4 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-base rounded-xl shadow-lg shadow-teal-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    <span>Save Catch Log</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const netStatus = document.getElementById('network-status');
    const updateNetStatus = () => {
        if (navigator.onLine) {
            netStatus.innerHTML = '<i data-lucide="wifi" class="w-3.5 h-3.5 text-emerald-400"></i> Online (Cabin)';
            netStatus.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30';
        } else {
            netStatus.innerHTML = '<i data-lucide="wifi-off" class="w-3.5 h-3.5 text-amber-400"></i> Offline (Boat Mode)';
            netStatus.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30';
        }
        if (window.initLucideIcons) window.initLucideIcons();
    };

    window.addEventListener('online', updateNetStatus);
    window.addEventListener('offline', updateNetStatus);
    updateNetStatus();

    window.acquireQuickGPS = () => {
        const textEl = document.getElementById('gps-status-text');
        if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            if (textEl) {
                textEl.innerHTML = `🔒 GPS blocked by browser: Requires HTTPS or Chrome flag (http://${location.hostname} is HTTP)`;
            }
            return;
        }

        if (!navigator.geolocation) {
            if (textEl) textEl.textContent = 'Device Pinpoint GPS: Hardware Not Supported';
            return;
        }
        if (textEl) textEl.textContent = 'Device Pinpoint GPS: Querying satellite fix...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                if (textEl) textEl.textContent = `Device Pinpoint GPS: Acquired (${lat}, ${lng})`;
            },
            (err) => {
                if (textEl) {
                    if (err.code === 1) {
                        textEl.textContent = `⚠️ GPS Permission Denied (Ensure location permissions & HTTPS)`;
                    } else {
                        textEl.textContent = `Device Pinpoint GPS: Unavailable (${err.message || 'Timeout'})`;
                    }
                }
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    };

    acquireQuickGPS();

    // Restore last selected Angler & Lake preferences from localStorage
    const savedAngler = localStorage.getItem('fishinglog_last_angler');
    const savedLake = localStorage.getItem('fishinglog_last_lake');
    if (savedAngler) document.getElementById('anglers_id').value = savedAngler;
    if (savedLake) document.getElementById('lakes_id').value = savedLake;

    document.getElementById('quickCatchForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const form = e.target;
        const anglerVal = form.anglers_id.value;
        const lakeVal = form.lakes_id.value;

        // Remember Angler and Lake for next catch
        if (anglerVal) localStorage.setItem('fishinglog_last_angler', anglerVal);
        if (lakeVal) localStorage.setItem('fishinglog_last_lake', lakeVal);

        const latVal = form.latitude.value ? parseFloat(form.latitude.value) : null;
        const lngVal = form.longitude.value ? parseFloat(form.longitude.value) : null;

        const catchData = {
            client_id: window.offlineSyncManager ? window.offlineSyncManager.generateUUID() : null,
            anglers_id: form.anglers_id.value,
            lakes_id: form.lakes_id.value,
            fish_breeds_id: form.fish_breeds_id.value,
            lures_id: form.lures_id.value ? form.lures_id.value : null,
            length: parseFloat(form.length.value),
            weight: form.weight.value ? parseFloat(form.weight.value) : null,
            latitude: latVal,
            longitude: lngVal,
            released: form.released.checked ? 1 : 0,
            caught: form.caught.value
        };

        const alertBox = document.getElementById('quick-catch-alert');

        if (navigator.onLine) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('/api/v1/records', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify(catchData)
                });

                if (response.ok) {
                    alertBox.className = 'text-xs font-bold p-4 rounded-xl shadow-sm border transition-all bg-emerald-50 text-emerald-800 border-emerald-200 block';
                    alertBox.textContent = '🎉 Catch successfully logged to server!';
                } else {
                    throw new Error('Server error');
                }
            } catch (err) {
                // Fallback to offline local storage
                await window.offlineSyncManager.saveCatchOffline(catchData);
                alertBox.className = 'text-xs font-bold p-4 rounded-xl shadow-sm border transition-all bg-amber-50 text-amber-800 border-amber-200 block';
                alertBox.textContent = '⛵ Saved locally to boat offline queue!';
            }
        } else {
            // Save offline directly
            await window.offlineSyncManager.saveCatchOffline(catchData);
            alertBox.className = 'text-xs font-bold p-4 rounded-xl shadow-sm border transition-all bg-amber-50 text-amber-800 border-amber-200 block';
            alertBox.textContent = '⛵ Saved locally to boat offline queue! Will auto-sync at cabin.';
        }

        // Reset length and weight for next entry
        form.length.value = '';
        form.weight.value = '';

        setTimeout(() => alertBox.classList.add('hidden'), 3500);
    });
});
</script>
@endsection
