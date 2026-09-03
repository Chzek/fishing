<div x-data="{ 
        isOpen: @entangle('isOpen'),
        gpsStatus: 'Search GPS...',
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
            
            <!-- Success Status Notification Toast -->
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

            <form wire:submit="save" id="quickCatchModalForm" class="space-y-4">
                
                <!-- GPS & Geolocation Quick Action -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs">
                    <div class="flex items-center gap-2 text-slate-300 truncate">
                        <x-lucide-map-pin class="w-4 h-4 text-teal-400 shrink-0" />
                        <span x-text="gpsStatus" class="truncate font-mono text-[11px]">Search GPS...</span>
                    </div>
                    <button type="button" 
                            @click="acquireGps()"
                            class="px-2.5 py-1 text-[11px] font-bold text-teal-300 bg-teal-500/15 hover:bg-teal-500/25 rounded-lg border border-teal-500/30 transition-colors shrink-0">
                        Acquire GPS
                    </button>
                </div>

                <!-- Angler & Lake Selectors -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="modal_anglers_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Angler *</label>
                        <select id="modal_anglers_id" wire:model="anglers_id" class="w-full h-11 px-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select Angler...</option>
                            @foreach($anglers as $angler)
                                <option value="{{ $angler->id }}">{{ $angler->fullName }}</option>
                            @endforeach
                        </select>
                        @error('anglers_id') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="modal_lakes_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Lake / Water *</label>
                        <select id="modal_lakes_id" wire:model="lakes_id" class="w-full h-11 px-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select Lake...</option>
                            @foreach($lakes as $lake)
                                <option value="{{ $lake->id }}">{{ $lake->name }}</option>
                            @endforeach
                        </select>
                        @error('lakes_id') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Species & Lure -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="modal_fish_breeds_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Fish Species *</label>
                        <select id="modal_fish_breeds_id" wire:model="fish_breeds_id" class="w-full h-11 px-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select Species...</option>
                            @foreach($fishBreeds as $breed)
                                <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                            @endforeach
                        </select>
                        @error('fish_breeds_id') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="modal_lures_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Lure / Bait</label>
                        <livewire:ui.lure-selector name="modal_lures_id" :selected-id="$lures_id" placeholder="Search lure..." />
                        @error('lures_id') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Length & Weight -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="modal_length" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Length (Inches) *</label>
                        <div class="relative">
                            <input type="number" step="0.25" id="modal_length" wire:model="length" placeholder="e.g. 19.5"
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
                        <input type="date" id="modal_caught" wire:model="caught"
                               class="w-full h-11 px-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 font-medium text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('caught') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 sm:pt-6">
                        <label class="flex items-center gap-3 cursor-pointer p-2.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/15 transition-colors">
                            <input type="checkbox" wire:model="released" class="w-5 h-5 rounded text-emerald-500 focus:ring-emerald-500 bg-slate-950 border-slate-700">
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
