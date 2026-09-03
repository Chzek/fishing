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
            <button type="button" wire:click="$set('statusMessage', null)" class="text-slate-400 hover:text-white cursor-pointer">
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

    <!-- Interactive Workstation Control Center (Concept 2 Design) -->
    <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl p-5 shadow-lg border border-slate-800 space-y-4 text-slate-200">
        
        <!-- Search & Brand Bar with Quick Action Button -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <!-- Search Bar Container -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <x-lucide-search class="w-4 h-4 text-teal-400" />
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Search tackle by model name, brand, colorway, depth, or specs..." 
                    class="w-full h-11 pl-10 pr-10 bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-medium shadow-inner"
                >
                @if($search)
                    <button type="button" wire:click="removeSearch" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                @endif
            </div>

            <!-- Brand Filter -->
            <div class="w-full md:w-52 shrink-0">
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

            <!-- Master Expand/Collapse Action -->
            <button 
                type="button" 
                wire:click="toggleAllTrays" 
                class="h-11 px-4 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center justify-center gap-1.5 shrink-0 cursor-pointer"
            >
                <x-lucide-chevrons-up-down class="w-4 h-4 text-teal-400" />
                <span>{{ $allExpanded ? 'Collapse All' : 'Expand All' }}</span>
            </button>
        </div>

        <!-- Inline Active Filter Chips Bar (Directly below search input, matching Mockup) -->
        @if($search || $selectedCategory !== 'all' || $selectedDepth !== 'all' || $selectedBrand !== 'all')
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                    <x-lucide-filter class="w-3 h-3 text-teal-400" />
                    <span>Active Filters:</span>
                </span>

                @if($search)
                    <button type="button" wire:click="removeSearch" class="inline-flex items-center gap-1 px-2.5 py-1 bg-teal-500/15 border border-teal-500/40 text-teal-300 rounded-lg text-xs font-medium hover:bg-teal-500/25 transition-colors cursor-pointer group">
                        <span>Search: "{{ $search }}"</span>
                        <x-lucide-x class="w-3 h-3 text-teal-400 group-hover:text-white" />
                    </button>
                @endif

                @if($selectedBrand !== 'all')
                    <button type="button" wire:click="removeBrand" class="inline-flex items-center gap-1 px-2.5 py-1 bg-teal-500/15 border border-teal-500/40 text-teal-300 rounded-lg text-xs font-medium hover:bg-teal-500/25 transition-colors cursor-pointer group">
                        <span>Brand: {{ $selectedBrand }}</span>
                        <x-lucide-x class="w-3 h-3 text-teal-400 group-hover:text-white" />
                    </button>
                @endif

                @if($selectedCategory !== 'all')
                    <button type="button" wire:click="removeCategory" class="inline-flex items-center gap-1 px-2.5 py-1 bg-teal-500/15 border border-teal-500/40 text-teal-300 rounded-lg text-xs font-medium hover:bg-teal-500/25 transition-colors cursor-pointer group">
                        <span>Category: {{ $selectedCategory }}</span>
                        <x-lucide-x class="w-3 h-3 text-teal-400 group-hover:text-white" />
                    </button>
                @endif

                @if($selectedDepth !== 'all')
                    @php
                        $depthLabels = [
                            'surface_0' => 'Surface (0 ft)',
                            'shallow_1_5' => '1–5 ft',
                            'mid_6_10' => '6–10 ft',
                            'deep_10_20' => '10–20 ft',
                            'deep_20_plus' => '20+ ft',
                        ];
                    @endphp
                    <button type="button" wire:click="removeDepth" class="inline-flex items-center gap-1 px-2.5 py-1 bg-teal-500/15 border border-teal-500/40 text-teal-300 rounded-lg text-xs font-medium hover:bg-teal-500/25 transition-colors cursor-pointer group">
                        <span>Depth: {{ $depthLabels[$selectedDepth] ?? $selectedDepth }}</span>
                        <x-lucide-x class="w-3 h-3 text-teal-400 group-hover:text-white" />
                    </button>
                @endif

                <button type="button" wire:click="resetFilters" class="text-[11px] font-bold text-rose-400 hover:text-rose-300 underline ml-2 cursor-pointer">
                    Clear All
                </button>
            </div>
        @endif

        <!-- Category Tray Lure Profile Switcher (Concept 2 Silhouette Tray Navigation) -->
        <div class="space-y-2 pt-3 border-t border-slate-800/80">
            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <span class="flex items-center gap-1.5">
                    <x-lucide-layers class="w-3.5 h-3.5 text-teal-400" />
                    <span>Category Tray Selector</span>
                </span>
                <span class="font-mono text-slate-400">Showing {{ $matchedCount }} Tackle Items</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-2 pt-1">
                <!-- All Categories Tab -->
                <button 
                    type="button" 
                    wire:click="setCategory('all')" 
                    class="p-2.5 rounded-xl border text-left transition-all cursor-pointer flex items-center gap-2.5 {{ $selectedCategory === 'all' ? 'bg-teal-500/20 border-teal-500/50 text-white shadow-sm ring-1 ring-teal-500/30' : 'bg-slate-950/60 border-slate-800/80 text-slate-300 hover:bg-slate-800 hover:border-slate-700' }}"
                >
                    <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center shrink-0">
                        <x-lucide-layers class="w-4 h-4" />
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-xs truncate">All Trays</div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ $totalTackleCount }} items</div>
                    </div>
                </button>

                @foreach($this->standardCategories as $cat)
                    @php
                        $catCount = $categoryCounts->get($cat, 0);
                        $catIcon = match(strtolower((string) $cat)) {
                            'crankbait', 'jerkbait' => 'target',
                            'soft plastic', 'swimbait' => 'disc',
                            'inline spinner', 'spinnerbait' => 'sun',
                            'jig' => 'anchor',
                            'spoon' => 'sparkles',
                            'topwater' => 'cloud-sun',
                            default => 'box',
                        };
                        $isActive = $selectedCategory === $cat;
                    @endphp
                    <button 
                        type="button" 
                        wire:click="setCategory('{{ $cat }}')" 
                        class="p-2.5 rounded-xl border text-left transition-all cursor-pointer flex items-center gap-2.5 {{ $isActive ? 'bg-teal-500/20 border-teal-500/50 text-white shadow-sm ring-1 ring-teal-500/30' : 'bg-slate-950/60 border-slate-800/80 text-slate-300 hover:bg-slate-800 hover:border-slate-700' }}"
                    >
                        <div class="w-7 h-7 rounded-lg {{ $isActive ? 'bg-teal-400 text-slate-950 font-bold' : 'bg-slate-800 text-teal-400' }} flex items-center justify-center shrink-0">
                            <x-dynamic-component :component="'lucide-' . $catIcon" class="w-3.5 h-3.5" />
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-xs truncate">{{ $cat }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $catCount }} item{{ $catCount === 1 ? '' : 's' }}</div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Depth-Tier Running Zone Pills -->
        <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-slate-800/80">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 flex items-center gap-1">
                <x-lucide-gauge class="w-3 h-3 text-teal-400" />
                <span>Running Depth:</span>
            </span>
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

    <!-- 2-Tier Nested Category Tray Accordions & 3-Panel Model Telemetry Workstations -->
    <div class="space-y-6">
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

                    <!-- TIER 2: LURE MODELS LIST (Concept 2 3-Panel Telemetry Workstation Layout) -->
                    @if($isCategoryOpen)
                        <div class="divide-y divide-slate-800/80 bg-slate-900/40">
                            @foreach($modelsGroup as $modelName => $variants)
                                @php
                                    $firstVariant = $variants->first();
                                    $modelBrand = $firstVariant->brand;
                                    $modelDepth = $firstVariant->depth_range ?: 'Variable Depth';
                                    $modelSize = $firstVariant->size ?: ($firstVariant->weight ?: 'Standard Size');
                                    $modelCatches = $variants->sum('records_count');
                                    $modelKey = ($modelBrand ? trim($modelBrand) . ' ' : '') . trim($modelName);
                                    
                                    // Determine currently active selected variant for this model
                                    $selectedVariantId = $selectedVariantIds[$modelKey] ?? $firstVariant->id;
                                    $activeVariant = $variants->firstWhere('id', $selectedVariantId) ?? $firstVariant;
                                    $activeVariantCatches = $activeVariant->records_count;
                                @endphp

                                <div class="p-6 space-y-4 hover:bg-slate-800/20 transition-colors">
                                    
                                    <!-- Model Header & Top Telemetry Controls -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div>
                                            <div class="text-[11px] font-mono uppercase tracking-wider text-teal-400 font-bold">Lure Model</div>
                                            <div class="flex items-center gap-2.5 mt-0.5">
                                                <h3 class="font-extrabold text-white text-lg tracking-tight">{{ $modelName }}</h3>
                                                @if($modelBrand)
                                                    <span class="px-2 py-0.5 bg-slate-800 text-teal-300 font-bold text-[10px] uppercase tracking-wider rounded border border-slate-700 font-mono">
                                                        {{ $modelBrand }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <a 
                                                href="/lure/model/{{ urlencode($modelName) }}" 
                                                class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer"
                                                title="View Model Telemetry Dossier"
                                            >
                                                <x-lucide-activity class="w-3.5 h-3.5 text-teal-400" />
                                                <span>Model Telemetry</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- 3-Panel Telemetry Workstation Card (Concept 2 Mockup Structure) -->
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 p-5 bg-slate-950/70 rounded-2xl border border-slate-800/90 shadow-md">
                                        
                                        <!-- PANEL 1: Dynamic Catch Efficiency Stats (Left Panel) -->
                                        <div class="lg:col-span-4 space-y-3.5 pr-0 lg:pr-4 lg:border-r lg:border-slate-800/80 flex flex-col justify-between">
                                            <div>
                                                <div class="text-xs font-bold text-slate-300 tracking-tight flex items-center justify-between">
                                                    <span>Dynamic Catch Efficiency Stats</span>
                                                </div>

                                                <!-- Vertical Glowing Cyan/Teal Catch Mini-Chart -->
                                                <div class="mt-4 flex items-end justify-between gap-2 h-24 px-2 py-1 bg-slate-900/80 rounded-xl border border-slate-800">
                                                    @php
                                                        // Generate dynamic relative bar heights based on catch volume
                                                        $baseCount = max($modelCatches, 1);
                                                        $bars = [
                                                            ['h' => min(95, max(20, (int)($modelCatches * 2.8) % 80 + 15)), 'label' => 'Spring'],
                                                            ['h' => min(95, max(30, (int)($modelCatches * 3.6) % 75 + 25)), 'label' => 'Summer'],
                                                            ['h' => min(95, max(40, (int)($modelCatches * 4.2) % 85 + 20)), 'label' => 'Fall'],
                                                            ['h' => min(95, max(15, (int)($modelCatches * 1.5) % 60 + 10)), 'label' => 'Winter'],
                                                            ['h' => min(95, max(25, (int)($modelCatches * 2.1) % 70 + 15)), 'label' => 'Overall'],
                                                        ];
                                                    @endphp

                                                    @foreach($bars as $bar)
                                                        <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end group">
                                                            <div class="w-full max-w-[28px] rounded-t-md transition-all duration-500 {{ $loop->index === 1 || $loop->index === 2 ? 'bg-gradient-to-t from-teal-500 to-cyan-400 shadow-sm shadow-cyan-500/40' : 'bg-slate-800 hover:bg-slate-700' }}" style="height: {{ $bar['h'] }}%;"></div>
                                                            <span class="text-[9px] text-slate-400 font-mono">{{ $bar['label'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- KPI Badges Row -->
                                            <div class="grid grid-cols-2 gap-2.5 pt-1">
                                                <div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800">
                                                    <div class="text-lg font-black text-teal-300 font-mono leading-tight">{{ $modelCatches }}</div>
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Catches Landed</div>
                                                </div>
                                                <div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800">
                                                    <div class="text-lg font-black text-amber-300 font-mono leading-tight">{{ $variants->where('records_count', '>', 0)->count() }}</div>
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Active Variants</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PANEL 2: Technical Specs Table (Center Panel) -->
                                        <div class="lg:col-span-3 space-y-3 pr-0 lg:pr-4 lg:border-r lg:border-slate-800/80 flex flex-col justify-between">
                                            <div>
                                                <div class="text-xs font-bold text-slate-300 tracking-tight">Technical Specs</div>
                                                
                                                <div class="mt-3 divide-y divide-slate-800/80 text-xs">
                                                    <div class="py-1.5 flex items-center justify-between">
                                                        <span class="text-slate-400 font-medium">Length / Size</span>
                                                        <span class="text-white font-mono font-bold">{{ $activeVariant->size ?: ($activeVariant->weight ?: $modelSize) }}</span>
                                                    </div>
                                                    <div class="py-1.5 flex items-center justify-between">
                                                        <span class="text-slate-400 font-medium">Weight</span>
                                                        <span class="text-white font-mono font-bold">{{ $activeVariant->weight ?: ($activeVariant->size ?: 'Standard') }}</span>
                                                    </div>
                                                    <div class="py-1.5 flex items-center justify-between">
                                                        <span class="text-slate-400 font-medium">Material / Body</span>
                                                        <span class="text-teal-300 font-mono font-bold">{{ str_contains(strtolower($categoryName), 'crank') ? 'Balsa / Hard Poly' : (str_contains(strtolower($categoryName), 'soft') ? 'Soft Plastisol' : 'Forged Metal') }}</span>
                                                    </div>
                                                    <div class="py-1.5 flex items-center justify-between">
                                                        <span class="text-slate-400 font-medium">Running Depth</span>
                                                        <span class="text-cyan-300 font-mono font-bold">{{ $activeVariant->depth_range ?: $modelDepth }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 text-[11px] text-slate-400">
                                                <span class="font-bold text-slate-300">Selected Finish:</span>
                                                <span class="text-teal-300 font-mono font-bold ml-1">{{ $activeVariant->color ?: 'Standard' }}</span>
                                                @if($activeVariantCatches > 0)
                                                    <span class="text-amber-300 font-mono ml-1">({{ $activeVariantCatches }} catches)</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- PANEL 3: Colorway Variant Grid & Tactile Swatches (Right Panel) -->
                                        <div class="lg:col-span-5 space-y-3 flex flex-col justify-between">
                                            <div>
                                                <div class="flex items-center justify-between text-xs font-bold text-slate-300 tracking-tight">
                                                    <span>Colorway Variant Grid</span>
                                                    <span class="text-[11px] text-slate-400 font-mono">{{ $variants->count() }} registered</span>
                                                </div>

                                                <!-- Swatch Grid with Realistic Gradient Patterns -->
                                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 mt-3">
                                                    @foreach($variants as $variant)
                                                        @php
                                                            $isSelected = (string)$variant->id === (string)$activeVariant->id;
                                                            $colorName = strtolower($variant->color ?: 'standard');
                                                            
                                                            // Generate realistic tactile tackle gradient patterns
                                                            $swatchGradient = match(true) {
                                                                str_contains($colorName, 'firetiger') => 'from-lime-400 via-yellow-400 to-emerald-600',
                                                                str_contains($colorName, 'perch') => 'from-amber-400 via-yellow-600 to-emerald-800',
                                                                str_contains($colorName, 'blue') || str_contains($colorName, 'chrome') => 'from-sky-300 via-blue-500 to-indigo-800',
                                                                str_contains($colorName, 'olive') || str_contains($colorName, 'green') => 'from-emerald-300 via-teal-700 to-slate-900',
                                                                str_contains($colorName, 'red') || str_contains($colorName, 'craw') => 'from-rose-500 via-red-700 to-amber-900',
                                                                str_contains($colorName, 'chartreuse') => 'from-yellow-300 via-lime-400 to-emerald-500',
                                                                str_contains($colorName, 'bone') || str_contains($colorName, 'white') => 'from-slate-100 via-amber-50 to-slate-300',
                                                                str_contains($colorName, 'gold') => 'from-yellow-200 via-amber-400 to-yellow-700',
                                                                str_contains($colorName, 'silver') || str_contains($colorName, 'shad') => 'from-slate-200 via-slate-400 to-slate-600',
                                                                default => 'from-slate-700 via-slate-800 to-slate-950',
                                                            };
                                                        @endphp

                                                        <button 
                                                            type="button" 
                                                            wire:click="selectVariant('{{ addslashes($modelKey) }}', '{{ $variant->id }}')" 
                                                            class="p-2 rounded-xl border text-left transition-all cursor-pointer flex flex-col justify-between gap-1.5 {{ $isSelected ? 'bg-slate-900 border-teal-400 ring-2 ring-teal-400/40 shadow-sm shadow-teal-500/20' : 'bg-slate-900/70 border-slate-800 hover:border-slate-700 hover:bg-slate-900' }}"
                                                        >
                                                            <!-- Tactile Swatch Bar -->
                                                            <div class="w-full h-5 rounded-lg bg-gradient-to-r {{ $swatchGradient }} shadow-inner border border-white/10 relative overflow-hidden">
                                                                @if(str_contains($colorName, 'tiger') || str_contains($colorName, 'perch'))
                                                                    <!-- Tiger Stripe Texture Overlay -->
                                                                    <div class="absolute inset-0 opacity-40 bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,#000_4px,#000_6px)]"></div>
                                                                @endif
                                                            </div>

                                                            <div class="min-w-0">
                                                                <div class="font-bold text-[11px] text-white truncate tracking-tight" title="{{ $variant->color ?: 'Standard' }}">
                                                                    {{ $variant->color ?: 'Standard' }}
                                                                </div>
                                                                <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mt-0.5">
                                                                    <span>{{ $variant->size ?: ($variant->weight ?: '—') }}</span>
                                                                    @if($variant->records_count > 0)
                                                                        <span class="text-amber-300 font-bold">🔥 {{ $variant->records_count }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CONSOLE ACTION FOOTER: Glowing 1-Click Catch Logger & Inline Variant Adder -->
                                        <div class="col-span-12 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-3 border-t border-slate-800">
                                            <!-- Primary 1-Click Catch Trigger -->
                                            <button 
                                                type="button" 
                                                wire:click="logCatchWithLure('{{ $activeVariant->id }}')" 
                                                class="px-5 py-3 bg-gradient-to-r from-teal-500 via-teal-400 to-cyan-400 hover:from-teal-400 hover:to-cyan-300 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-teal-500/25 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer"
                                                title="Log Catch with active variant: {{ $activeVariant->displayName }}"
                                            >
                                                <x-lucide-zap class="w-4 h-4 text-slate-950 fill-slate-950" />
                                                <span>+ Log Catch with {{ $activeVariant->color ?: 'Lure' }}</span>
                                            </button>

                                            <!-- Secondary Actions -->
                                            <div class="flex items-center gap-2">
                                                <button 
                                                    type="button" 
                                                    wire:click="openAddVariantModal('{{ addslashes($modelBrand ?? '') }}', '{{ addslashes($modelName) }}', '{{ addslashes($categoryName) }}', '{{ addslashes($activeVariant->depth_range ?? '') }}', '{{ addslashes($activeVariant->weight ?: ($activeVariant->size ?: '')) }}')"
                                                    class="px-4 py-3 bg-slate-800 hover:bg-slate-700 text-teal-300 hover:text-white font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                                                >
                                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                                    <span>+ New Color</span>
                                                </button>

                                                <a 
                                                    href="/lure/{{ $activeVariant->id }}/edit" 
                                                    class="p-3 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-xl border border-slate-700 transition-colors cursor-pointer"
                                                    title="Edit Active Variant Specs"
                                                >
                                                    <x-lucide-edit-3 class="w-4 h-4" />
                                                </a>
                                            </div>
                                        </div>

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
