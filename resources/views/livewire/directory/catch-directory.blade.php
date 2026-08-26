<div class="space-y-6">
    <!-- Live Telemetry KPI Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-xs font-semibold uppercase tracking-wider text-teal-400">Total Catches</span>
            <div class="text-3xl font-black text-white font-mono mt-1">{{ number_format($stats->total_catches ?? 0) }}</div>
        </div>
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-xs font-semibold uppercase tracking-wider text-cyan-400">Total Length</span>
            <div class="text-3xl font-black text-white font-mono mt-1">{{ round(($stats->total_inches ?? 0) / 12, 1) }} <span class="text-sm font-normal text-slate-400">ft.</span></div>
        </div>
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Avg. Length</span>
            <div class="text-3xl font-black text-white font-mono mt-1">{{ round($stats->avg_length ?? 0, 1) }} <span class="text-sm font-normal text-slate-400">in.</span></div>
        </div>
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-xs font-semibold uppercase tracking-wider text-amber-400">Released Rate</span>
            <div class="text-3xl font-black text-white font-mono mt-1">
                {{ ($stats->total_catches ?? 0) > 0 ? round((($stats->released_count ?? 0) / $stats->total_catches) * 100) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Filter Control Console -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <!-- Instant Search Input -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search species, lake, angler..." 
                    class="w-full bg-slate-950 border border-slate-800 focus:border-teal-500 rounded-xl py-2.5 px-3.5 text-sm text-white placeholder-slate-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all"
                />
            </div>

            <!-- Species Dropdown -->
            <div>
                <select wire:model.live="speciesId" class="w-full bg-slate-950 border border-slate-800 focus:border-teal-500 rounded-xl py-2.5 px-3.5 text-sm text-white focus:ring-1 focus:ring-teal-500 outline-none">
                    <option value="">All Species</option>
                    @foreach($speciesList as $sp)
                        <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Lake Dropdown -->
            <div>
                <select wire:model.live="lakeId" class="w-full bg-slate-950 border border-slate-800 focus:border-teal-500 rounded-xl py-2.5 px-3.5 text-sm text-white focus:ring-1 focus:ring-teal-500 outline-none">
                    <option value="">All Lakes & Waters</option>
                    @foreach($lakesList as $lk)
                        <option value="{{ $lk->id }}">{{ $lk->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By Dropdown -->
            <div>
                <select wire:model.live="sortBy" class="w-full bg-slate-950 border border-slate-800 focus:border-teal-500 rounded-xl py-2.5 px-3.5 text-sm text-white focus:ring-1 focus:ring-teal-500 outline-none">
                    <option value="caught_desc">Date (Newest First)</option>
                    <option value="caught_asc">Date (Oldest First)</option>
                    <option value="length_desc">Longest Catch</option>
                    <option value="weight_desc">Heaviest Catch</option>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-800/60">
            <label class="inline-flex items-center cursor-pointer gap-2 select-none">
                <input type="checkbox" wire:model.live="releasedOnly" class="rounded border-slate-800 bg-slate-950 text-teal-500 focus:ring-teal-500">
                <span class="text-xs font-semibold text-slate-300">Show Released Catches Only</span>
            </label>

            <button 
                wire:click="resetFilters" 
                type="button" 
                class="text-xs text-slate-400 hover:text-white font-medium transition-colors underline"
            >
                Reset All Filters
            </button>
        </div>
    </div>

    <!-- Catch Records Display Grid -->
    @if($records->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($records as $rec)
                <div class="bg-slate-900 border border-slate-800 hover:border-teal-500/50 rounded-2xl p-4 shadow-lg transition-all space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-white text-base">{{ $rec->fishBreed->name ?? 'Unknown Species' }}</h3>
                            <p class="text-xs text-slate-400 font-mono">{{ $rec->caught ? \Carbon\Carbon::parse($rec->caught)->format('M d, Y') : 'Unscheduled' }}</p>
                        </div>
                        @if($rec->released)
                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">Released</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs font-mono bg-slate-950/60 p-2.5 rounded-xl border border-slate-800/80">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Length</span>
                            <span class="text-white font-bold text-sm">{{ $rec->length ? $rec->length . ' in.' : '—' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Weight</span>
                            <span class="text-white font-bold text-sm">{{ $rec->weight ? $rec->weight . ' lbs.' : '—' }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-xs text-slate-400 pt-1">
                        <span>📍 {{ $rec->lake->name ?? 'Unknown Water' }}</span>
                        <span>🎣 {{ $rec->angler->firstName ?? '' }} {{ $rec->angler->lastName ?? '' }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $records->links() }}
        </div>
    @else
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-12 text-center space-y-3">
            <div class="text-4xl">🎣</div>
            <h3 class="text-lg font-bold text-white">No Catches Found</h3>
            <p class="text-sm text-slate-400 max-w-md mx-auto">No records match your active search filters. Try adjusting your search query or reset filters.</p>
        </div>
    @endif
</div>
