@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ viewMode: 'nested', openCategories: {}, openModels: {} }">
    <!-- Digital Tackle Box Header & Actions -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>Digital Tackle Box</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Nested tackle box hierarchy: Category Trays → Lure Models → Color Variants</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <form action="{{ route('lure.import-catalog') }}" method="POST" onsubmit="return confirm('Import 50+ classic master catalog lures into your tackle box? (Existing lures will not be duplicated)');">
                @csrf
                <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="download-cloud" class="w-4 h-4 text-teal-400"></i>
                    <span>Import Master Catalog</span>
                </button>
            </form>

            <a href="/lure/create" class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Tackle Item</span>
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-xl shadow-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Telemetry Key Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <x-kpiMetric label="Total Tackle Inventory" :value="$totalTackleCount" icon="box" color="teal" subtext="Registered Lures & Variants" />
        <x-kpiMetric label="Top Productive Category" :value="$topCategoryName" icon="target" color="emerald" subtext="Most Populated Tackle Type" />
        <x-kpiMetric label="Catches Landed on Tackle" :value="$totalCatchesOnTackle" icon="hook" color="sky" subtext="Verified Logbook Catches" />
    </div>

    <!-- Category Filter & View Mode Controls -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span>Tackle Hierarchy Navigation</span>
                </span>
                <span class="text-xs text-slate-400 font-mono">({{ $nestedTackle->count() }} Categories / {{ $allLures->count() }} Variants)</span>
            </div>

            <!-- View Switcher & Expand All Controls -->
            <div class="flex items-center gap-2">
                <button type="button" @click="
                    const allCatKeys = @js($nestedTackle->keys()->toArray());
                    const areAllOpen = allCatKeys.every(k => openCategories[k]);
                    allCatKeys.forEach(k => openCategories[k] = !areAllOpen);
                " class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition-colors flex items-center gap-1">
                    <i data-lucide="chevrons-up-down" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Toggle Category Trays</span>
                </button>

                <div class="flex items-center p-0.5 bg-slate-100 rounded-xl border border-slate-200/80 text-xs font-bold">
                    <button type="button" @click="viewMode = 'nested'" :class="viewMode === 'nested' ? 'bg-white text-teal-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1 rounded-lg transition-all flex items-center gap-1">
                        <i data-lucide="list-tree" class="w-3.5 h-3.5"></i>
                        <span>Nested Trays</span>
                    </button>
                    <button type="button" @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-teal-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1 rounded-lg transition-all flex items-center gap-1">
                        <i data-lucide="grid" class="w-3.5 h-3.5"></i>
                        <span>Variant Cards</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-1.5">
            <a href="/lure?category=all" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $activeCategory === 'all' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                All Categories ({{ $totalTackleCount }})
            </a>

            @foreach($categoriesList as $cat)
                @php
                    $catCount = $categoryCounts->get($cat, 0);
                @endphp
                <a href="/lure?category={{ urlencode($cat) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 {{ $activeCategory === $cat ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    <span>{{ $cat }}</span>
                    @if($catCount > 0)
                        <span class="px-1.5 py-0.2 text-[10px] rounded-full {{ $activeCategory === $cat ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-800' }} font-mono">{{ $catCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- VIEW 1: 2-Tier Nested Category Accordion Architecture (Default View) -->
    <div x-show="viewMode === 'nested'" class="space-y-4">
        @if($nestedTackle->count() > 0)
            @foreach($nestedTackle as $categoryName => $modelsGroup)
                @php
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
                <div x-data="{ catOpen: true }" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                    <!-- Category Header Banner -->
                    <button 
                        type="button" 
                        @click="catOpen = !catOpen; openCategories['{{ $categoryName }}'] = catOpen" 
                        class="w-full px-6 py-4 bg-slate-900 text-white flex items-center justify-between text-left hover:bg-slate-800 transition-colors cursor-pointer"
                    >
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 text-teal-300 flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $catIcon }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-white tracking-tight flex items-center gap-2.5">
                                    <span>{{ $categoryName }} Tray</span>
                                </h2>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 font-mono mt-0.5">
                                    <span>{{ $modelsGroup->count() }} Lure Model{{ $modelsGroup->count() === 1 ? '' : 's' }}</span>
                                    <span>•</span>
                                    <span class="text-teal-300 font-bold">{{ $categoryTotalVariants }} Total Variant{{ $categoryTotalVariants === 1 ? '' : 's' }}</span>
                                    @if($categoryTotalCatches > 0)
                                        <span>•</span>
                                        <span class="text-amber-300 font-bold">{{ $categoryTotalCatches }} Catches Landed</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-xs text-slate-400 font-medium hidden sm:inline" x-text="catOpen ? 'Collapse Tray' : 'Open Tray'"></span>
                            <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 transition-transform duration-200" :class="catOpen ? 'rotate-180 bg-teal-500/20 text-teal-300 border-teal-500/40' : ''">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </button>

                    <!-- TIER 2: LURE MODELS LIST INSIDE CATEGORY TRAY -->
                    <div x-show="catOpen || openCategories['{{ $categoryName }}']" x-collapse class="divide-y divide-slate-100 bg-slate-50/30">
                        @foreach($modelsGroup as $modelName => $variants)
                            @php
                                $firstVariant = $variants->first();
                                $modelBrand = $firstVariant->brand;
                                $modelCatches = $variants->sum('records_count');
                            @endphp

                            <div x-data="{ modelOpen: false }" class="transition-colors">
                                <!-- Model Accordion Header -->
                                <button 
                                    type="button" 
                                    @click="modelOpen = !modelOpen; openModels['{{ $modelName }}'] = modelOpen" 
                                    class="w-full px-6 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-left hover:bg-slate-100/70 transition-colors cursor-pointer"
                                >
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-teal-600 flex items-center justify-center shrink-0 shadow-2xs font-mono font-bold text-xs">
                                            {{ $variants->count() }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-bold text-slate-900 text-sm tracking-tight">{{ $modelName }}</h3>
                                                @if($modelBrand)
                                                    <span class="px-2 py-0.5 bg-slate-200/80 text-slate-700 font-bold text-[10px] uppercase tracking-wider rounded border border-slate-300 font-mono">{{ $modelBrand }}</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium mt-0.5">
                                                <span class="font-mono text-slate-700 font-bold">{{ $variants->count() }} Color Variant{{ $variants->count() === 1 ? '' : 's' }}</span>
                                                @if($modelCatches > 0)
                                                    <span>•</span>
                                                    <span class="text-emerald-700 font-mono font-bold">{{ $modelCatches }} Verified Catch{{ $modelCatches === 1 ? '' : 'es' }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                        <span class="text-[11px] text-slate-400 font-medium" x-text="modelOpen ? 'Hide Colors' : 'Show Colors'"></span>
                                        <div class="w-7 h-7 rounded-md bg-white border border-slate-200 flex items-center justify-center text-slate-600 transition-transform duration-200" :class="modelOpen ? 'rotate-180 bg-teal-50 border-teal-300 text-teal-700' : ''">
                                            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                                        </div>
                                    </div>
                                </button>

                                <!-- TIER 3: COLOR PATTERNS & SPECS TABLE -->
                                <div x-show="modelOpen || openModels['{{ $modelName }}']" x-collapse class="bg-white border-t border-slate-100 p-4">
                                    <div class="overflow-x-auto rounded-xl border border-slate-200/80 shadow-2xs">
                                        <table class="w-full text-left text-xs text-slate-700">
                                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200/80">
                                                <tr>
                                                    <th scope="col" class="py-3 px-4">Color Pattern</th>
                                                    <th scope="col" class="py-3 px-4">Size / Weight</th>
                                                    <th scope="col" class="py-3 px-4">Running Depth</th>
                                                    <th scope="col" class="py-3 px-4 text-center">Catches Logged</th>
                                                    <th scope="col" class="py-3 px-4 text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($variants as $variant)
                                                    <tr class="hover:bg-slate-50 transition-colors">
                                                        <td class="py-3 px-4 font-bold text-slate-900 flex items-center gap-2">
                                                            <div class="w-2.5 h-2.5 rounded-full bg-amber-400 border border-amber-500 shrink-0"></div>
                                                            <span>{{ $variant->color ?: 'Standard Color' }}</span>
                                                        </td>
                                                        <td class="py-3 px-4 font-mono text-slate-700 font-semibold">
                                                            {{ $variant->size ?: ($variant->weight ?: '—') }}
                                                        </td>
                                                        <td class="py-3 px-4 font-mono text-sky-700 font-semibold">
                                                            {{ $variant->depth_range ?: '—' }}
                                                        </td>
                                                        <td class="py-3 px-4 text-center font-mono font-bold text-teal-700">
                                                            {{ $variant->records_count }}
                                                        </td>
                                                        <td class="py-3 px-4 text-right">
                                                            <div class="flex items-center justify-end gap-1.5">
                                                                <a href="/lure/{{ $variant->id }}" class="px-2.5 py-1 bg-slate-100 hover:bg-teal-50 hover:text-teal-700 text-slate-700 font-semibold text-[11px] rounded-lg border border-slate-200 transition-colors">
                                                                    Telemetry →
                                                                </a>
                                                                <x-tableOptions name="lure" identifier="{{ $variant->id }}" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <x-emptyState icon="box" title="No Tackle Items Found" description="No lures match your active category filter. Add a new lure or import the Master Tackle Catalog." actionUrl="/lure/create" actionLabel="Add First Tackle Item" />
        @endif
    </div>

    <!-- VIEW 2: Individual Variant Cards Grid -->
    <div x-show="viewMode === 'grid'" class="space-y-4" x-cloak>
        @if($allLures->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($allLures as $lure)
                    @php
                        $catIcon = match(strtolower((string) $lure->category)) {
                            'crankbait', 'jerkbait' => 'target',
                            'soft plastic', 'swimbait' => 'disc',
                            'inline spinner', 'spinnerbait' => 'sun',
                            'jig' => 'anchor',
                            'spoon' => 'sparkles',
                            'topwater' => 'cloud-sun',
                            default => 'hook',
                        };
                    @endphp
                    <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-teal-300 transition-all duration-200 p-5 flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center shrink-0 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-200 shadow-2xs">
                                        <i data-lucide="{{ $catIcon }}" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-slate-900 text-sm tracking-tight group-hover:text-teal-600 transition-colors truncate">
                                            <a href="/lure/{{ $lure->id }}">
                                                {{ $lure->name }}
                                            </a>
                                        </h3>
                                        @if($lure->brand)
                                            <span class="text-[11px] font-bold text-teal-700 block truncate">{{ $lure->brand }}</span>
                                        @endif
                                    </div>
                                </div>
                                <x-tableOptions name="lure" identifier="{{ $lure->id }}" />
                            </div>

                            <div class="space-y-1.5 pt-1">
                                <div class="flex flex-wrap items-center gap-1 text-[11px]">
                                    @if($lure->category)
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold rounded-md border border-slate-200">{{ $lure->category }}</span>
                                    @endif
                                    @if($lure->color)
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-800 font-bold rounded-md border border-amber-200">{{ $lure->color }}</span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500 font-mono">
                                    @if($lure->size || $lure->weight)
                                        <span>Weight: <strong class="text-slate-800">{{ $lure->size ?: $lure->weight }}</strong></span>
                                    @endif
                                    @if($lure->depth_range)
                                        <span>•</span>
                                        <span>Depth: <strong class="text-sky-700">{{ $lure->depth_range }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-mono font-bold text-teal-700">
                                {{ $lure->records_count }} catch{{ $lure->records_count === 1 ? '' : 'es' }}
                            </span>
                            <a href="/lure/{{ $lure->id }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline flex items-center gap-1">
                                <span>Telemetry →</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
