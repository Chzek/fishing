<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Digital Tackle Box Header & Actions -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <x-lucide-box class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>Digital Tackle Box</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Interactive Telemetry Workstation: Category Trays → Lure Models → Color Variants</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <form action="{{ route('lure.import-catalog') }}" method="POST" onsubmit="return confirm('Import 50+ classic master catalog lures into your tackle box? (Existing lures will not be duplicated)');">
                @csrf
                <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <x-lucide-download-cloud class="w-4 h-4 text-teal-400" />
                    <span>Import Master Catalog</span>
                </button>
            </form>

            <a href="/lure/create" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-plus class="w-4 h-4" />
                <span>Add Tackle Item</span>
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if ($statusMessage)
        <div class="p-4 rounded-xl border bg-emerald-500/15 border-emerald-500/30 text-emerald-300 text-xs font-bold flex items-center justify-between shadow-sm animate-fadeIn" role="alert">
            <div class="flex items-center gap-2">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-400 shrink-0" />
                <span>{{ $statusMessage }}</span>
            </div>
            <button type="button" wire:click="$set('statusMessage', null)" class="text-slate-400 hover:text-white">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <!-- Telemetry Key Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <x-kpiMetric label="Total Tackle Inventory" :value="$totalTackleCount" icon="box" color="teal" subtext="Registered Lures & Variants" />
        <x-kpiMetric label="Top Productive Category" :value="$topCategoryName" icon="target" color="emerald" subtext="Most Populated Tackle Type" />
        <x-kpiMetric label="Catches Landed on Tackle" :value="$totalCatchesOnTackle" icon="fishing-hook" color="sky" subtext="Verified Logbook Catches" />
    </div>

    <!-- Interactive Workstation Control Center -->
    <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl p-5 shadow-lg border border-slate-800 space-y-4 text-slate-200">
        
        <!-- Search, Brand & Master Expand Row -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <x-lucide-search class="w-4 h-4" />
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Search tackle by model name, brand, colorway, depth, or specs..." 
                    class="w-full h-11 pl-10 pr-10 bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-medium"
                >
                @if($search)
                    <button type="button" wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                @endif
            </div>

            <!-- Brand Filter -->
            <div class="w-full md:w-56 shrink-0">
                <select 
                    wire:model.live="selectedBrand" 
                    class="w-full h-11 px-3 bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs text-slate-300 font-medium focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                >
                    <option value="all">All Brands ({{ $brandsList->count() }})</option>
                    @foreach($brandsList as $brand)
                        <option value="{{ $brand }}">{{ $brand }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Expand / Collapse Master Toggle -->
            <button 
                type="button" 
                wire:click="toggleAllTrays" 
                class="h-11 px-4 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center justify-center gap-1.5 shrink-0 cursor-pointer"
            >
                <x-lucide-chevrons-up-down class="w-4 h-4 text-teal-400" />
                <span>{{ $allExpanded ? 'Collapse All Trays' : 'Expand All Trays' }}</span>
            </button>
        </div>

        <!-- Category Tray Filter Pills Carousel -->
        <div class="space-y-2 pt-2 border-t border-slate-800/80">
            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <span class="flex items-center gap-1.5">
                    <x-lucide-layers class="w-3.5 h-3.5 text-teal-400" />
                    <span>Category Trays</span>
                </span>
                <span class="font-mono text-slate-400">Showing {{ $matchedCount }} Tackle Items</span>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                <button 
                    type="button" 
                    wire:click="setCategory('all')" 
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer {{ $selectedCategory === 'all' ? 'bg-teal-500 text-white shadow-xs' : 'bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700/60' }}"
                >
                    All Categories ({{ $totalTackleCount }})
                </button>

                @foreach($this->standardCategories as $cat)
                    @php
                        $catCount = $categoryCounts->get($cat, 0);
                    @endphp
                    <button 
                        type="button" 
                        wire:click="setCategory('{{ $cat }}')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 cursor-pointer {{ $selectedCategory === $cat ? 'bg-teal-500 text-white shadow-xs' : 'bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700/60' }}"
                    >
                        <span>{{ $cat }}</span>
                        @if($catCount > 0)
                            <span class="px-1.5 py-0.2 text-[10px] rounded-full {{ $selectedCategory === $cat ? 'bg-white/20 text-white' : 'bg-slate-900 text-slate-400 border border-slate-700' }} font-mono">{{ $catCount }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Depth-Tier Running Zone Pills -->
        <div class="space-y-2 pt-2 border-t border-slate-800/80">
            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <span class="flex items-center gap-1.5">
                    <x-lucide-gauge class="w-3.5 h-3.5 text-teal-400" />
                    <span>Target Depth Zones</span>
                </span>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                @php
                    $depthTiers = [
                        'all' => 'All Depths',
                        'surface_0' => 'Surface / Topwater (0 ft)',
                        'shallow_1_5' => 'Shallow Running (1–5 ft)',
                        'mid_6_10' => 'Medium Depth (6–10 ft)',
                        'deep_10_20' => 'Deep Diving (10–20 ft)',
                        'deep_20_plus' => 'Trolling / Extreme (20+ ft)',
                    ];
                @endphp

                @foreach($depthTiers as $key => $label)
                    <button 
                        type="button" 
                        wire:click="setDepth('{{ $key }}')" 
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer {{ $selectedDepth === $key ? 'bg-teal-500/20 text-teal-300 border border-teal-500/40' : 'bg-slate-950/60 hover:bg-slate-800 text-slate-400 border border-slate-800' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Active Filter Reset Pill Bar (When filters are active) -->
        @if($search || $selectedCategory !== 'all' || $selectedDepth !== 'all' || $selectedBrand !== 'all')
            <div class="flex items-center justify-between pt-2 border-t border-slate-800/80 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Filtered By:</span>
                    @if($search)
                        <span class="px-2 py-0.5 bg-teal-500/15 border border-teal-500/30 text-teal-300 rounded text-[11px] font-mono">
                            Search: "{{ $search }}"
                        </span>
                    @endif
                    @if($selectedCategory !== 'all')
                        <span class="px-2 py-0.5 bg-teal-500/15 border border-teal-500/30 text-teal-300 rounded text-[11px] font-mono">
                            Category: {{ $selectedCategory }}
                        </span>
                    @endif
                    @if($selectedBrand !== 'all')
                        <span class="px-2 py-0.5 bg-teal-500/15 border border-teal-500/30 text-teal-300 rounded text-[11px] font-mono">
                            Brand: {{ $selectedBrand }}
                        </span>
                    @endif
                    @if($selectedDepth !== 'all')
                        <span class="px-2 py-0.5 bg-teal-500/15 border border-teal-500/30 text-teal-300 rounded text-[11px] font-mono">
                            Depth: {{ $depthTiers[$selectedDepth] ?? $selectedDepth }}
                        </span>
                    @endif
                </div>

                <button 
                    type="button" 
                    wire:click="resetFilters" 
                    class="text-[11px] font-bold text-rose-400 hover:text-rose-300 hover:underline cursor-pointer"
                >
                    Reset All Filters
                </button>
            </div>
        @endif
    </div>

    <!-- 2-Tier Nested Category Tray Accordions -->
    <div class="space-y-5">
        @if($nestedTackle->count() > 0)
            @foreach($nestedTackle as $categoryName => $modelsGroup)
                @php
                    $isCategoryOpen = $openCategories[$categoryName] ?? true;
                    $categoryTotalVariants = $modelsGroup->flatten(1)->count();
                    $categoryTotalCatches = $modelsGroup->flatten(1)->sum('records_count');

                    $catIcon = match(strtolower((string) $categoryName)) {
                        'crankbait', 'jerkbait' => 'target',
                        'soft plastic', 'swimbait' => 'disc',
                        'inline spinner', 'spinnerbait' => 'sun',
                        'jig' => 'anchor',
                        'spoon' => 'sparkles',
                        'topwater' => 'cloud-sun',
                        default => 'box',
                    };
                @endphp

                <!-- TIER 1: CATEGORY TRAY CONTAINER -->
                <div class="bg-slate-900 rounded-2xl border border-slate-800 shadow-md overflow-hidden transition-all">
                    <!-- Category Header Banner -->
                    <button 
                        type="button" 
                        wire:click="toggleCategory('{{ $categoryName }}')" 
                        class="w-full px-6 py-4 bg-slate-950/80 hover:bg-slate-800/80 text-white flex items-center justify-between text-left transition-colors cursor-pointer border-b border-slate-800/80"
                    >
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 text-teal-300 flex items-center justify-center shrink-0">
                                <x-dynamic-component :component="'lucide-' . $catIcon" class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-white tracking-tight flex items-center gap-2.5">
                                    <span>{{ $categoryName }} Tray</span>
                                </h2>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 font-mono mt-0.5">
                                    <span>{{ $modelsGroup->count() }} Lure Model{{ $modelsGroup->count() === 1 ? '' : 's' }}</span>
                                    <span>•</span>
                                    <span class="text-teal-300 font-bold">{{ $categoryTotalVariants }} Variant{{ $categoryTotalVariants === 1 ? '' : 's' }}</span>
                                    @if($categoryTotalCatches > 0)
                                        <span>•</span>
                                        <span class="text-amber-300 font-bold">🔥 {{ $categoryTotalCatches }} Verified Catch{{ $categoryTotalCatches === 1 ? '' : 'es' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <a 
                                href="/lure/category/{{ urlencode($categoryName) }}" 
                                @click.stop 
                                class="px-2.5 py-1 bg-slate-800 hover:bg-teal-600 hover:text-white text-teal-300 font-bold text-xs rounded-lg border border-slate-700 transition-colors flex items-center gap-1 cursor-pointer"
                            >
                                <span class="hidden sm:inline">Category Telemetry</span>
                                <x-lucide-arrow-right class="w-3.5 h-3.5" />
                            </a>
                            <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 transition-transform duration-200 {{ $isCategoryOpen ? 'rotate-180 bg-teal-500/20 text-teal-300 border-teal-500/40' : '' }}">
                                <x-lucide-chevron-down class="w-4 h-4" />
                            </div>
                        </div>
                    </button>

                    <!-- TIER 2: LURE MODELS LIST INSIDE CATEGORY TRAY -->
                    @if($isCategoryOpen)
                        <div class="divide-y divide-slate-800/60 bg-slate-900/50">
                            @foreach($modelsGroup as $modelName => $variants)
                                @php
                                    $firstVariant = $variants->first();
                                    $modelBrand = $firstVariant->brand;
                                    $modelDepth = $firstVariant->depth_range;
                                    $modelSize = $firstVariant->size ?: $firstVariant->weight;
                                    $modelCatches = $variants->sum('records_count');
                                    $modelKey = ($modelBrand ? trim($modelBrand) . ' ' : '') . trim($modelName);
                                    $isModelOpen = $openModels[$modelKey] ?? true;
                                @endphp

                                <div class="p-5 space-y-4 hover:bg-slate-800/30 transition-colors">
                                    <!-- Model Header & Meta Bar -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-700/80 text-teal-400 flex items-center justify-center shrink-0 font-mono font-bold text-xs shadow-inner">
                                                {{ $variants->count() }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h3 class="font-bold text-white text-base tracking-tight">{{ $modelName }}</h3>
                                                    @if($modelBrand)
                                                        <span class="px-2 py-0.5 bg-slate-800 text-teal-300 font-bold text-[10px] uppercase tracking-wider rounded border border-slate-700 font-mono">
                                                            {{ $modelBrand }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 font-medium mt-0.5">
                                                    <span class="font-mono text-slate-300 font-bold">{{ $variants->count() }} Colorway{{ $variants->count() === 1 ? '' : 's' }}</span>
                                                    @if($modelDepth)
                                                        <span>•</span>
                                                        <span class="text-teal-300 font-mono text-[11px] flex items-center gap-1">
                                                            <x-lucide-arrow-down-up class="w-3 h-3 text-teal-400" />
                                                            {{ $modelDepth }}
                                                        </span>
                                                    @endif
                                                    @if($modelCatches > 0)
                                                        <span>•</span>
                                                        <span class="text-amber-300 font-mono font-bold">🔥 {{ $modelCatches }} Landed</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick Model Actions -->
                                        <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">
                                            <button 
                                                type="button" 
                                                wire:click="openAddVariantModal('{{ addslashes($modelBrand ?? '') }}', '{{ addslashes($modelName) }}', '{{ addslashes($categoryName) }}', '{{ addslashes($modelDepth ?? '') }}', '{{ addslashes($modelSize ?? '') }}')"
                                                class="px-2.5 py-1.5 bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 font-bold text-xs rounded-xl border border-teal-500/30 transition-colors flex items-center gap-1.5 cursor-pointer"
                                                title="Add Color Variant to {{ $modelName }}"
                                            >
                                                <x-lucide-plus class="w-3.5 h-3.5" />
                                                <span>Add Colorway</span>
                                            </button>

                                            <a 
                                                href="/lure/model/{{ urlencode($modelName) }}" 
                                                class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1 cursor-pointer"
                                            >
                                                <span>Telemetry</span>
                                                <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Interactive Colorway Variant Grid & Swatches -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 pt-1">
                                        @foreach($variants as $variant)
                                            @php
                                                $variantCatches = $variant->records_count;
                                                $hasCatches = $variantCatches > 0;
                                            @endphp

                                            <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col justify-between gap-2.5 shadow-xs group">
                                                <!-- Variant Title & Color -->
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-1.5">
                                                            <div class="w-2.5 h-2.5 rounded-full bg-teal-400 ring-2 ring-teal-400/20 shrink-0"></div>
                                                            <h4 class="font-bold text-xs text-white truncate tracking-tight" title="{{ $variant->color ?: 'Standard' }}">
                                                                {{ $variant->color ?: 'Standard Finish' }}
                                                            </h4>
                                                        </div>
                                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate">
                                                            {{ $variant->size ?: ($variant->weight ?: '—') }} 
                                                            @if($variant->depth_range)
                                                                • {{ $variant->depth_range }}
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Catch Counter Badge -->
                                                    @if($hasCatches)
                                                        <span class="px-2 py-0.5 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-300 font-mono font-bold text-[10px] shrink-0 flex items-center gap-1">
                                                            <x-lucide-flame class="w-3 h-3 text-amber-400" />
                                                            <span>{{ $variantCatches }}</span>
                                                        </span>
                                                    @else
                                                        <span class="px-1.5 py-0.5 rounded bg-slate-900 text-slate-400 text-[9px] font-mono shrink-0">
                                                            Ready
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Action Buttons Row: 1-Click Catch Logger + Details -->
                                                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-900">
                                                    <!-- 1-Click Quick Catch Modal Trigger -->
                                                    <button 
                                                        type="button" 
                                                        wire:click="logCatchWithLure('{{ $variant->id }}')" 
                                                        class="flex-1 py-1.5 px-2 bg-teal-600/20 hover:bg-teal-600 text-teal-300 hover:text-white font-bold text-[11px] rounded-lg border border-teal-500/30 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-xs active:scale-95"
                                                        title="Log a catch using this {{ $variant->displayName }}"
                                                    >
                                                        <x-lucide-zap class="w-3 h-3 text-teal-300 group-hover:text-white" />
                                                        <span>Log Catch</span>
                                                    </button>

                                                    <a 
                                                        href="/lure/{{ $variant->id }}" 
                                                        class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg border border-slate-700 transition-colors cursor-pointer"
                                                        title="View Lure Dossier"
                                                    >
                                                        <x-lucide-external-link class="w-3.5 h-3.5" />
                                                    </a>

                                                    <a 
                                                        href="/lure/{{ $variant->id }}/edit" 
                                                        class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg border border-slate-700 transition-colors cursor-pointer"
                                                        title="Edit Lure Specs"
                                                    >
                                                        <x-lucide-edit-3 class="w-3.5 h-3.5" />
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-12 text-center text-slate-400 space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-800 border border-slate-700 text-slate-400 flex items-center justify-center mx-auto">
                    <x-lucide-package-search class="w-8 h-8 text-teal-400" />
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-white">No Tackle Found</h3>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">
                        No lure models match your active search filters or depth criteria. Try adjusting your search query or reset your filters.
                    </p>
                </div>
                <button type="button" wire:click="resetFilters" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-teal-300 font-bold text-xs rounded-xl border border-slate-700 transition-colors cursor-pointer">
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>

    <!-- Inline Add Colorway Variant Modal -->
    @if($showAddVariantModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-slate-900 border border-slate-800 text-slate-200 rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-5">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/20 border border-teal-500/30 text-teal-300 flex items-center justify-center">
                            <x-lucide-palette class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white leading-tight">Add Colorway Variant</h3>
                            <p class="text-[11px] text-teal-400 font-mono">{{ $targetModelBrand ? $targetModelBrand . ' ' : '' }}{{ $targetModelName }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeAddVariantModal" class="text-slate-400 hover:text-white cursor-pointer">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="saveVariant" class="space-y-4">
                    <div class="space-y-1.5">
                        <label for="newVariantColors" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Color Pattern(s) *
                        </label>
                        <input 
                            type="text" 
                            id="newVariantColors" 
                            wire:model="newVariantColors" 
                            placeholder="e.g. Firetiger, Bleeding Olive Flash, Perch" 
                            class="w-full h-11 px-3.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            autofocus
                        >
                        <span class="text-[10px] text-slate-400 block">Enter one color, or multiple comma-separated colors to batch create variants.</span>
                        @error('newVariantColors') <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label for="newVariantSize" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Size / Weight (Optional)
                            </label>
                            <input 
                                type="text" 
                                id="newVariantSize" 
                                wire:model="newVariantSize" 
                                placeholder="e.g. 3/16 oz, 2.5 in" 
                                class="w-full h-11 px-3.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Running Depth
                            </label>
                            <input 
                                type="text" 
                                value="{{ $targetModelDepth ?: 'Standard Depth' }}" 
                                disabled 
                                class="w-full h-11 px-3.5 bg-slate-950/60 border border-slate-800 rounded-xl text-xs text-slate-400 font-mono"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
                        <button 
                            type="button" 
                            wire:click="closeAddVariantModal" 
                            class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="px-5 py-2.5 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer"
                        >
                            <x-lucide-save class="w-4 h-4" />
                            <span>Save Color Variant(s)</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
