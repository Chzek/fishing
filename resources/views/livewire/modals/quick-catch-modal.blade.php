<div x-data="{ 
        isOpen: @entangle('isOpen'),
        gpsStatus: 'Search GPS...',
        localSuccessMessage: null,
        localErrorMessage: null,
        selectedLureId: @entangle('lures_id'),
        selectedLure: null,
        lures: @js($lures),
        lureSearch: '',
        selectedCategory: 'all',
        lureDropdownOpen: false,

        init() {
            if (!this.lures || this.lures.length === 0) {
                const ref = window.offlineSyncManager ? window.offlineSyncManager.getCachedReferenceData() : null;
                if (ref && ref.lures) this.lures = ref.lures;
            }
            this.$watch('isOpen', (val) => {
                if (val) {
                    this.localSuccessMessage = null;
                    this.localErrorMessage = null;
                    this.rehydrateSelects();
                }
            });
        },

        get categories() {
            const set = new Set();
            (this.lures || []).forEach(l => { if (l.category) set.add(l.category); });
            return Array.from(set).sort();
        },

        get filteredLures() {
            let list = this.lures || [];
            if (this.selectedCategory !== 'all') {
                list = list.filter(l => (l.category || 'Other') === this.selectedCategory);
            }
            const q = (this.lureSearch || '').trim().toLowerCase();
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
            $wire.set('lures_id', this.selectedLureId);
            this.lureDropdownOpen = false;
        },

        clearLure() {
            this.selectedLureId = '';
            this.selectedLure = null;
            this.lureSearch = '';
            $wire.set('lures_id', null);
            this.lureDropdownOpen = false;
        },

        rehydrateSelects() {
            const refData = window.offlineSyncManager ? window.offlineSyncManager.getCachedReferenceData() : null;
            if (!refData) return;

            const anglerSelect = document.getElementById('modal_anglers_id');
            if (anglerSelect && anglerSelect.options.length <= 1 && refData.anglers) {
                refData.anglers.forEach(a => {
                    const opt = document.createElement('option');
                    opt.value = a.id;
                    opt.textContent = a.full_name || `${a.firstName || ''} ${a.lastName || ''}`.trim() || `Angler #${a.id}`;
                    anglerSelect.appendChild(opt);
                });
            }

            const lakeSelect = document.getElementById('modal_lakes_id');
            if (lakeSelect && lakeSelect.options.length <= 1 && refData.lakes) {
                refData.lakes.forEach(l => {
                    const opt = document.createElement('option');
                    opt.value = l.id;
                    opt.textContent = l.name;
                    lakeSelect.appendChild(opt);
                });
            }

            const breedSelect = document.getElementById('modal_fish_breeds_id');
            if (breedSelect && breedSelect.options.length <= 1 && refData.fish_breeds) {
                refData.fish_breeds.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = b.name;
                    breedSelect.appendChild(opt);
                });
            }
        },

        acquireGps() {
            if (!navigator.geolocation) {
                this.gpsStatus = 'GPS Not Supported';
                return;
            }
            this.gpsStatus = 'Querying fix...';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = parseFloat(pos.coords.latitude.toFixed(6));
                    const lng = parseFloat(pos.coords.longitude.toFixed(6));
                    $wire.set('latitude', lat);
                    $wire.set('longitude', lng);
                    this.gpsStatus = `Fixed (${lat}, ${lng})`;
                },
                (err) => {
                    this.gpsStatus = 'GPS Unavailable';
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
            );
        },

        async handleFormSubmit(e) {
            this.localSuccessMessage = null;
            this.localErrorMessage = null;

            if (!navigator.onLine) {
                const anglerVal = document.getElementById('modal_anglers_id')?.value;
                const lakeVal = document.getElementById('modal_lakes_id')?.value;
                const breedVal = document.getElementById('modal_fish_breeds_id')?.value;
                const lengthVal = document.getElementById('modal_length')?.value;
                const weightVal = document.getElementById('modal_weight')?.value;
                const caughtVal = document.getElementById('modal_caught')?.value;
                const releasedVal = document.getElementById('modal_released')?.checked;

                if (!anglerVal || !lakeVal || !breedVal || !lengthVal || !caughtVal) {
                    this.localErrorMessage = 'Please fill in all required fields (Angler, Lake, Species, Length, Date).';
                    return;
                }

                const catchData = {
                    client_id: window.offlineSyncManager ? window.offlineSyncManager.generateUUID() : null,
                    anglers_id: anglerVal,
                    lakes_id: lakeVal,
                    fish_breeds_id: breedVal,
                    lures_id: this.selectedLureId || null,
                    length: parseFloat(lengthVal),
                    weight: weightVal ? parseFloat(weightVal) : null,
                    caught: caughtVal,
                    released: releasedVal ? 1 : 0
                };

                if (window.offlineSyncManager) {
                    await window.offlineSyncManager.saveCatchOffline(catchData);
                }

                this.localSuccessMessage = '⛵ Saved locally to boat offline queue! Will auto-sync when online.';
                if (document.getElementById('modal_length')) document.getElementById('modal_length').value = '';
                if (document.getElementById('modal_weight')) document.getElementById('modal_weight').value = '';
                $wire.set('length', null);
                $wire.set('weight', null);
                return;
            }

            $wire.save();
        }
     }"
     @keydown.escape.window="if(isOpen) $wire.closeQuickCatch()"
     class="relative z-50">

    <!-- Slide-over Backdrop -->
    <div x-show="isOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$wire.closeQuickCatch()"
         class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-40"
         style="display: none;"
         x-cloak>
    </div>

    <!-- Slide-over Drawer Panel -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 max-w-lg w-full bg-slate-900 border-l border-slate-800 text-slate-200 shadow-2xl z-50 flex flex-col"
         style="display: none;"
         x-cloak>

        <!-- Drawer Header -->
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/60 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-teal-500/15 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                    <x-lucide-zap class="w-5 h-5 text-teal-400" />
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-white tracking-tight leading-tight">Quick Catch Logger</h2>
                    <p class="text-[11px] text-slate-400">1-Tap Field Catch Telemetry</p>
                </div>
            </div>

            <button type="button"
                    @click="$wire.closeQuickCatch()"
                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
                    title="Close Drawer (Esc)">
                <x-lucide-x class="w-5 h-5" />
            </button>
        </div>

        <!-- Scrollable Form Body -->
        @if ($isOpen)
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            
            <!-- Success Status Notification Toast (Server or Offline) -->
            <template x-if="localSuccessMessage">
                <div class="p-4 rounded-xl border bg-amber-500/15 border-amber-500/30 text-amber-300 text-xs flex items-center gap-2 font-bold animate-fadeIn" role="alert">
                    <x-lucide-check-circle class="w-4 h-4 text-amber-400 shrink-0" />
                    <span x-text="localSuccessMessage"></span>
                </div>
            </template>

            @if ($statusMessage)
                <div class="p-4 rounded-xl border bg-emerald-500/15 border-emerald-500/30 text-emerald-300 text-xs space-y-2 animate-fadeIn" role="alert">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 font-bold">
                            <x-lucide-check-circle class="w-4 h-4 text-emerald-400 shrink-0" />
                            <span>{{ $statusMessage }}</span>
                        </div>
                        @if ($createdRecordId)
                            <a href="{{ url('/record/' . $createdRecordId) }}" class="font-bold underline text-emerald-300 hover:text-white text-[11px]">
                                View Dossier →
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Offline Validation Error Banner -->
            <template x-if="localErrorMessage">
                <div class="p-4 rounded-xl border bg-rose-500/15 border-rose-500/30 text-rose-300 text-xs flex items-center gap-2 font-bold animate-fadeIn" role="alert">
                    <x-lucide-alert-triangle class="w-4 h-4 text-rose-400 shrink-0" />
                    <span x-text="localErrorMessage"></span>
                </div>
            </template>

            <!-- Trophy Celebration Banner in Modal -->
            @if ($trophyMilestone)
                <div class="p-4 rounded-xl bg-gradient-to-r from-amber-500/20 via-yellow-500/20 to-amber-500/10 border border-amber-400/40 text-amber-200 text-xs space-y-1">
                    <div class="font-black text-amber-300 flex items-center gap-2 text-sm">
                        <x-lucide-trophy class="w-4 h-4 text-amber-400" />
                        <span>🏆 {{ $trophyMilestone['title'] ?? 'Personal Best Milestone!' }}</span>
                    </div>
                    <p class="text-[11px] text-amber-200/90 font-medium">
                        {{ $trophyMilestone['species_name'] ?? 'Species' }} ({{ $trophyMilestone['length'] ?? '—' }}") logged at {{ $trophyMilestone['lake_name'] ?? 'Waterbody' }}!
                    </p>
                </div>
            @endif

            <form @submit.prevent="handleFormSubmit($event)" id="quickCatchModalForm" class="space-y-4">
                
                <!-- GPS & Geolocation Quick Action -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs">
                    <div class="flex items-center gap-2 text-slate-300 truncate">
                        <x-lucide-map-pin class="w-4 h-4 text-teal-400 shrink-0" />
                        <span x-text="gpsStatus" class="truncate font-mono text-[11px]">Search GPS...</span>
                    </div>
                    <button type="button" 
                            @click="acquireGps()"
                            class="px-2.5 py-1 text-[11px] font-bold text-teal-300 bg-teal-500/15 hover:bg-teal-500/25 rounded-lg border border-teal-500/30 transition-colors shrink-0 cursor-pointer">
                        Acquire GPS
                    </button>
                </div>

                <!-- Angler & Lake Selectors -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="modal_anglers_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Angler *</label>
                        <select id="modal_anglers_id" wire:model="anglers_id" class="w-full h-11 px-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required>
                            <option value="">Select Angler...</option>
                            @foreach($anglers as $angler)
                                <option value="{{ $angler->id }}">{{ $angler->fullName }}</option>
                            @endforeach
                        </select>
                        @error('anglers_id') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="modal_lakes_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Lake / Water *</label>
                        <select id="modal_lakes_id" wire:model="lakes_id" class="w-full h-11 px-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required>
                            <option value="">Select Lake...</option>
                            @foreach($lakes as $lake)
                                <option value="{{ $lake->id }}">{{ $lake->name }}</option>
                            @endforeach
                        </select>
                        @error('lakes_id') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Species & Offline Lure Selector -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="modal_fish_breeds_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Fish Species *</label>
                        <select id="modal_fish_breeds_id" wire:model="fish_breeds_id" class="w-full h-11 px-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500" required>
                            <option value="">Select Species...</option>
                            @foreach($fishBreeds as $breed)
                                <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                            @endforeach
                        </select>
                        @error('fish_breeds_id') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Lure / Bait</label>
                        <div class="relative" @click.outside="lureDropdownOpen = false">
                            <template x-if="selectedLure">
                                <div class="w-full h-11 px-3 rounded-xl border border-teal-500/40 bg-slate-950 text-slate-200 flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <x-lucide-fishing-hook class="w-3.5 h-3.5 text-teal-400 shrink-0" />
                                        <span class="text-xs font-bold truncate text-white" x-text="selectedLure.name"></span>
                                    </div>
                                    <button type="button" @click="clearLure()" class="text-slate-400 hover:text-red-400 p-1 cursor-pointer">
                                        <x-lucide-x class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </template>

                            <template x-if="!selectedLure">
                                <div class="relative">
                                    <input type="text"
                                           x-model="lureSearch"
                                           @focus="lureDropdownOpen = true"
                                           placeholder="Search lure..."
                                           class="w-full h-11 pl-3 pr-8 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 placeholder-slate-500 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    <button type="button" @click="lureDropdownOpen = !lureDropdownOpen" class="absolute right-2.5 top-3 text-slate-400">
                                        <span :class="{ 'rotate-180': lureDropdownOpen }" class="inline-flex transition-transform">
                                            <x-lucide-chevron-down class="w-4 h-4" />
                                        </span>
                                    </button>
                                </div>
                            </template>

                            <!-- Dropdown Tray -->
                            <div x-show="lureDropdownOpen"
                                 x-transition
                                 class="absolute z-50 left-0 right-0 mt-1 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl max-h-56 overflow-y-auto divide-y divide-slate-800"
                                 style="display: none;">
                                <div class="p-1.5 bg-slate-950 flex items-center gap-1 overflow-x-auto no-scrollbar">
                                    <button type="button" @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-teal-600 text-white' : 'text-slate-400 bg-slate-900'" class="px-2 py-0.5 rounded text-[10px] font-bold">All</button>
                                    <template x-for="cat in categories" :key="cat">
                                        <button type="button" @click="selectedCategory = cat" :class="selectedCategory === cat ? 'bg-teal-600 text-white' : 'text-slate-400 bg-slate-900'" class="px-2 py-0.5 rounded text-[10px] font-bold whitespace-nowrap" x-text="cat"></button>
                                    </template>
                                </div>
                                <template x-for="l in filteredLures" :key="l.id">
                                    <div @click="selectLure(l)" class="p-2 hover:bg-slate-800 cursor-pointer flex items-center justify-between text-xs">
                                        <div class="min-w-0">
                                            <span class="font-bold text-white block truncate" x-text="l.name"></span>
                                            <span class="text-[10px] text-slate-400" x-text="(l.brand ? l.brand + ' • ' : '') + (l.color || l.category || '')"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Length & Weight -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="modal_length" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Length (Inches) *</label>
                        <div class="relative">
                            <input type="number" step="0.25" id="modal_length" wire:model="length" placeholder="e.g. 19.5" required
                                   class="w-full h-11 pl-3 pr-10 rounded-xl border border-slate-700 bg-slate-950 text-white font-mono font-bold text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <span class="absolute right-3 top-3 text-xs font-bold text-slate-400">in.</span>
                        </div>
                        @error('length') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="modal_weight" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Weight (Lbs - Optional)</label>
                        <div class="relative">
                            <input type="number" step="0.1" id="modal_weight" wire:model="weight" placeholder="e.g. 4.2"
                                   class="w-full h-11 pl-3 pr-10 rounded-xl border border-slate-700 bg-slate-950 text-white font-mono font-bold text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <span class="absolute right-3 top-3 text-xs font-bold text-slate-400">lbs.</span>
                        </div>
                        @error('weight') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Date & Catch & Release Checkbox -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1 items-center">
                    <div class="space-y-1.5">
                        <label for="modal_caught" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Date Caught *</label>
                        <input type="date" id="modal_caught" wire:model="caught" required
                               class="w-full h-11 px-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('caught') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 sm:pt-6">
                        <label class="flex items-center gap-3 cursor-pointer p-2.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/15 transition-colors">
                            <input type="checkbox" id="modal_released" wire:model="released" class="w-5 h-5 rounded text-emerald-500 focus:ring-emerald-500 bg-slate-950 border-slate-700">
                            <div class="flex items-center gap-2">
                                <x-lucide-heart class="w-4 h-4 text-emerald-400" />
                                <span class="text-xs font-bold text-emerald-300">Released (C&R)</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-not-allowed"
                            class="w-full py-3.5 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-950/50 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <x-lucide-save class="w-4 h-4 text-teal-200" />
                        <span>Log Catch Immediately</span>
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
