@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
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
                <p class="text-xs text-slate-400 mt-0.5">Manage lures, color variants, running depths, and tackle telemetry</p>
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

    <!-- Category Filter Navigation Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                <i data-lucide="filter" class="w-3.5 h-3.5 text-teal-600"></i>
                <span>Tackle Category Filters</span>
            </span>
            <span class="text-xs text-slate-400 font-mono">Showing {{ $lures->total() }} Lure(s)</span>
        </div>

        <div class="flex flex-wrap items-center gap-1.5">
            <a href="/lure?category=all" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $activeCategory === 'all' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                All Tackle ({{ $totalTackleCount }})
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

    <!-- Digital Tackle Box Grid -->
    @if($lures->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($lures as $lure)
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

                        <!-- Specs Badges Grid -->
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

                    <!-- Card Footer: Catch Telemetry & Action -->
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

        @if($lures->hasPages())
            <div class="pt-4 flex items-center justify-between border-t border-slate-200/80">
                <span class="text-xs text-slate-500">Showing {{ $lures->firstItem() }} to {{ $lures->lastItem() }} of {{ $lures->total() }} Tackle Items</span>
                <div>{{ $lures->links() }}</div>
            </div>
        @endif
    @else
        <x-emptyState icon="box" title="No Tackle Items Found" description="No lures match your active category filter. Add a new lure or import the Master Tackle Catalog." actionUrl="/lure/create" actionLabel="Add First Tackle Item" />
    @endif
</div>
@endsection
