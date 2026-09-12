@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Title & Network Mode Bar -->
    <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-md border border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <x-lucide-zap class="w-5 h-5" />
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
                    <x-lucide-map-pin class="w-4 h-4 text-teal-600 shrink-0" />
                    <span id="gps-status-text" class="font-medium">Device Pinpoint GPS: Searching...</span>
                </div>
                <button type="button" onclick="acquireQuickGPS()" class="text-[11px] font-bold text-teal-600 hover:text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200 cursor-pointer">Re-query</button>
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
                            <option value="{{ $lake->id }}" {{ request('lakes_id') == $lake->id ? 'selected' : '' }}>{{ $lake->name }}</option>
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
                            <option value="{{ $breed->id }}" {{ (request('fish_breed_id') == $breed->id || request('fish_breeds_id') == $breed->id) ? 'selected' : '' }}>{{ $breed->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 100% Offline-Ready Tacklebox Lure Selector -->
                <div class="space-y-1.5" x-data="offlineLureSelector(@js($lures))" x-init="init()">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lure / Bait (Optional)</label>
                    
                    {{-- Hidden input for form submission --}}
                    <input type="hidden" id="lures_id" name="lures_id" x-model="selectedLureId">

                    <div class="relative w-full" @click.outside="isOpen = false" @keydown.escape.window="isOpen = false">
                        <!-- Active Selected Lure Card -->
                        <template x-if="selectedLure">
                            <div class="w-full bg-white rounded-xl p-2.5 sm:p-3 border border-teal-500/30 bg-gradient-to-r from-teal-50/40 via-white to-sky-50/30 shadow-xs flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center shrink-0">
                                        <x-lucide-fishing-hook class="w-4 h-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span x-show="selectedLure.brand" class="text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded" x-text="selectedLure.brand"></span>
                                            <span class="font-bold text-slate-900 text-xs truncate" x-text="selectedLure.name"></span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 truncate" x-text="selectedLure.color || selectedLure.category || 'Tacklebox Item'"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" @click="isOpen = !isOpen" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg cursor-pointer">Change</button>
                                    <button type="button" @click="clearSelection()" class="p-1 text-slate-400 hover:text-red-500 rounded-lg cursor-pointer">
                                        <x-lucide-x class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Search Input Trigger -->
                        <template x-if="!selectedLure">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <x-lucide-search class="w-4 h-4" />
                                </div>
                                <input type="text"
                                       x-model="search"
                                       @focus="isOpen = true"
                                       placeholder="Tap to search tacklebox lures..."
                                       class="w-full h-11 pl-10 pr-10 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                                <button type="button"
                                        @click="isOpen = !isOpen"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <span :class="{ 'rotate-180': isOpen }" class="inline-flex transition-transform duration-200">
                                        <x-lucide-chevron-down class="w-4 h-4" />
                                    </span>
                                </button>
                            </div>
                        </template>

                        <!-- Offline Dropdown Tray -->
                        <div x-show="isOpen"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute z-50 left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden max-h-80 flex flex-col"
                             style="display: none;">

                            <!-- Category Filter Chips -->
                            <div class="p-2 bg-slate-50 border-b border-slate-200/80 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                                <button type="button"
                                        @click="selectedCategory = 'all'"
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors shrink-0 cursor-pointer"
                                        :class="selectedCategory === 'all' ? 'bg-teal-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-200/70 border border-slate-200'">
                                    All Categories
                                </button>
                                <template x-for="cat in categories" :key="cat">
                                    <button type="button"
                                            @click="selectedCategory = cat"
                                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors shrink-0 cursor-pointer"
                                            :class="selectedCategory === cat ? 'bg-teal-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-200/70 border border-slate-200'"
                                            x-text="cat">
                                    </button>
                                </template>
                            </div>

                            <!-- Lures List -->
                            <div class="overflow-y-auto divide-y divide-slate-100 flex-1 overscroll-contain">
                                <template x-for="lure in filteredLures" :key="lure.id">
                                    <div @click="selectLure(lure)"
                                         class="p-2.5 rounded-xl hover:bg-teal-50/70 active:bg-teal-100/80 cursor-pointer transition-colors flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                                <x-lucide-fishing-hook class="w-3.5 h-3.5" />
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span x-show="lure.brand" class="text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded" x-text="lure.brand"></span>
                                                    <span class="text-xs font-bold text-slate-900 truncate" x-text="lure.name"></span>
                                                    <span x-show="lure.color" class="text-xs text-slate-500 font-medium" x-text="'• ' + lure.color"></span>
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-medium" x-text="lure.category || 'Tackle'"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="filteredLures.length === 0">
                                    <div class="p-6 text-center space-y-2">
                                        <p class="text-xs font-bold text-slate-700">No lures found</p>
                                        <p class="text-[11px] text-slate-400">Try adjusting your search or category filter.</p>
                                    </div>
                                </template>
                            </div>

                            <!-- Footer -->
                            <div class="p-2 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-500">
                                <span class="text-[11px] font-medium text-slate-400" x-text="filteredLures.length + ' lures found'"></span>
                                <button type="button" @click="isOpen = false" class="text-slate-600 hover:text-slate-900 font-bold cursor-pointer">Close</button>
                            </div>
                        </div>
                    </div>
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
                            <x-lucide-heart class="w-4 h-4 text-emerald-600" />
                            <span class="text-sm font-bold text-emerald-800">Released Catch</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="pt-4">
                <button type="submit" id="saveCatchBtn" class="w-full py-4 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-base rounded-xl shadow-lg shadow-teal-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <x-lucide-save class="w-5 h-5" />
                    <span>Save Catch Log</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function offlineLureSelector(initialLures) {
    return {
        lures: initialLures || [],
        selectedLureId: '',
        selectedLure: null,
        search: '',
        selectedCategory: 'all',
        isOpen: false,

        init() {
            // If initial lures are empty (e.g. loaded offline from cached shell), rehydrate from localStorage
            if (!this.lures || this.lures.length === 0) {
                const refData = window.offlineSyncManager ? window.offlineSyncManager.getCachedReferenceData() : null;
                if (refData && refData.lures) {
                    this.lures = refData.lures;
                }
            }
        },

        get categories() {
            const set = new Set();
            (this.lures || []).forEach(l => {
                if (l.category) set.add(l.category);
            });
            return Array.from(set).sort();
        },

        get filteredLures() {
            let list = this.lures || [];
            if (this.selectedCategory !== 'all') {
                list = list.filter(l => (l.category || 'Other') === this.selectedCategory);
            }
            const q = (this.search || '').trim().toLowerCase();
            if (q !== '') {
                list = list.filter(l =>
                    (l.name && l.name.toLowerCase().includes(q)) ||
                    (l.brand && l.brand.toLowerCase().includes(q)) ||
                    (l.color && l.color.toLowerCase().includes(q)) ||
                    (l.category && l.category.toLowerCase().includes(q))
                );
            }
            return list;
        },

        selectLure(lure) {
            this.selectedLureId = String(lure.id);
            this.selectedLure = lure;
            this.isOpen = false;
        },

        clearSelection() {
            this.selectedLureId = '';
            this.selectedLure = null;
            this.search = '';
            this.isOpen = false;
        }
    };
}

function rehydrateOfflineSelects() {
    const refData = window.offlineSyncManager ? window.offlineSyncManager.getCachedReferenceData() : null;
    if (!refData) return;

    // 1. Anglers Dropdown Rehydration
    const anglerSelect = document.getElementById('anglers_id');
    if (anglerSelect && anglerSelect.options.length <= 1 && refData.anglers && refData.anglers.length > 0) {
        const currentVal = anglerSelect.value;
        refData.anglers.forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = a.full_name || `${a.firstName || ''} ${a.lastName || ''}`.trim() || `Angler #${a.id}`;
            anglerSelect.appendChild(opt);
        });
        if (currentVal) anglerSelect.value = currentVal;
    }

    // 2. Lakes Dropdown Rehydration
    const lakeSelect = document.getElementById('lakes_id');
    if (lakeSelect && lakeSelect.options.length <= 1 && refData.lakes && refData.lakes.length > 0) {
        const currentVal = lakeSelect.value;
        refData.lakes.forEach(l => {
            const opt = document.createElement('option');
            opt.value = l.id;
            opt.textContent = l.name;
            lakeSelect.appendChild(opt);
        });
        if (currentVal) lakeSelect.value = currentVal;
    }

    // 3. Fish Species Dropdown Rehydration
    const breedSelect = document.getElementById('fish_breeds_id');
    if (breedSelect && breedSelect.options.length <= 1 && refData.fish_breeds && refData.fish_breeds.length > 0) {
        const currentVal = breedSelect.value;
        refData.fish_breeds.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.id;
            opt.textContent = b.name;
            breedSelect.appendChild(opt);
        });
        if (currentVal) breedSelect.value = currentVal;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const netStatus = document.getElementById('network-status');
    const updateNetStatus = () => {
        if (navigator.onLine) {
            netStatus.innerHTML = '<x-lucide-wifi class="w-3.5 h-3.5 text-emerald-400" /> Online (Cabin)';
            netStatus.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30';
        } else {
            netStatus.innerHTML = '<x-lucide-wifi-off class="w-3.5 h-3.5 text-amber-400" /> Offline (Boat Mode)';
            netStatus.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30';
        }
        if (window.initLucideIcons) window.initLucideIcons();
    };

    window.addEventListener('online', () => {
        updateNetStatus();
        if (window.offlineSyncManager) window.offlineSyncManager.fetchAndCacheReferenceData();
    });
    window.addEventListener('offline', updateNetStatus);
    updateNetStatus();

    // Rehydrate any missing options from localStorage cache if offline
    rehydrateOfflineSelects();

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
    if (savedAngler && !document.getElementById('anglers_id').value) {
        document.getElementById('anglers_id').value = savedAngler;
    }
    if (savedLake && !document.getElementById('lakes_id').value) {
        document.getElementById('lakes_id').value = savedLake;
    }

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
