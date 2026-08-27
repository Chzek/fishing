@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ viewMode: 'grid' }">
    <!-- Header Hero Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                <i data-lucide="book-open" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>Fish Species & Taxonomy Guide</span>
                    <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">{{ $totalBreedsCount }} Species</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium pt-0.5">Biological profiles, taxonomic classification, and catch telemetry across fresh & saltwater species</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="/record/quick" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                <span>Log Catch</span>
            </a>
            <a href="/fish/breed/create" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Add Species</span>
            </a>
            <a href="/fish/family/create" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <i data-lucide="folder-plus" class="w-3.5 h-3.5"></i>
                <span>Add Family</span>
            </a>
        </div>
    </div>

    <!-- Taxonomy Overview Telemetry Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                <i data-lucide="fish" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-900 block leading-tight">{{ $totalBreedsCount }}</span>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tracked Species</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-900 block leading-tight">{{ $totalFamiliesCount }}</span>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Taxonomic Families</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                <i data-lucide="award" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-900 block leading-tight">{{ $totalCatchesCount }}</span>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Catches Logged</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                <i data-lucide="flame" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-sm font-bold text-slate-900 block truncate">{{ $topSpecies?->name ?? 'None' }}</span>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">
                    Most Caught ({{ $topSpecies?->records_count ?? 0 }})
                </span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Search Form -->
            <form method="GET" action="/fish" class="flex-1 relative">
                @if($selectedFamilyId)
                    <input type="hidden" name="family" value="{{ $selectedFamilyId }}">
                @endif
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search ?? '' }}" 
                        placeholder="Search species or biological family..."
                        class="w-full h-10 pl-10 pr-10 rounded-xl border border-slate-200 bg-slate-50/70 text-slate-800 text-xs focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors placeholder:text-slate-400"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                    @if(!empty($search))
                        <a href="/fish{{ $selectedFamilyId ? '?family='.$selectedFamilyId : '' }}" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600" title="Clear Search">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>

            <!-- View Mode Switcher -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/80 shrink-0 self-end sm:self-auto">
                <button 
                    type="button" 
                    @click="viewMode = 'grid'" 
                    :class="viewMode === 'grid' ? 'bg-white text-teal-700 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                    class="px-3 py-1.5 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer"
                >
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                    <span>Grid View</span>
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'table'" 
                    :class="viewMode === 'table' ? 'bg-white text-teal-700 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                    class="px-3 py-1.5 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer"
                >
                    <i data-lucide="list" class="w-3.5 h-3.5"></i>
                    <span>Table View</span>
                </button>
            </div>
        </div>

        <!-- Biological Family Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider shrink-0 flex items-center gap-1">
                <i data-lucide="filter" class="w-3 h-3 text-slate-400"></i> Families:
            </span>

            <a 
                href="/fish{{ $search ? '?search='.urlencode($search) : '' }}" 
                class="px-3 py-1 rounded-lg font-semibold transition-colors shrink-0 {{ empty($selectedFamilyId) ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}"
            >
                All Families ({{ $totalBreedsCount }})
            </a>

            @foreach($families as $family)
                <a 
                    href="/fish?family={{ $family->id }}{{ $search ? '&search='.urlencode($search) : '' }}" 
                    class="px-3 py-1 rounded-lg font-semibold transition-colors shrink-0 flex items-center gap-1.5 {{ $selectedFamilyId == $family->id ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}"
                >
                    <span>{{ $family->name }}</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedFamilyId == $family->id ? 'bg-white/20 text-white' : 'bg-slate-200/80 text-slate-600' }}">
                        {{ $family->breeds_count }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Content Area: Grid View -->
    <div x-show="viewMode === 'grid'">
        @if($fishes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach($fishes as $fish)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col justify-between hover:shadow-md hover:border-teal-500/40 transition-all group">
                        <div class="p-4 space-y-3">
                            <div class="w-full h-36 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center p-3 relative overflow-hidden group-hover:bg-teal-50/20 transition-colors">
                                @if($fish->imageUrl)
                                    <img src="{{ $fish->imageUrl }}" alt="{{ $fish->name }}" class="max-h-full max-w-full object-contain filter drop-shadow-sm group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="text-center text-slate-300">
                                        <i data-lucide="fish" class="w-12 h-12 mx-auto stroke-[1.25]"></i>
                                        <span class="text-[10px] font-medium block mt-1">No Illustration</span>
                                    </div>
                                @endif

                                @if($fish->family)
                                    <span class="absolute top-2 left-2 text-[10px] font-bold text-slate-600 bg-white/90 backdrop-blur-xs px-2 py-0.5 rounded-md border border-slate-200/70 shadow-2xs">
                                        {{ $fish->family->name }}
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-black text-slate-900 text-base group-hover:text-teal-700 transition-colors">
                                    <a href="/fish/{{ $fish->id }}">{{ $fish->name }}</a>
                                </h3>
                                <p class="text-xs text-slate-400 italic">Biological Profile & Telemetry</p>
                            </div>

                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100 text-center">
                                <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Catches</span>
                                    <span class="text-sm font-black text-teal-700 font-mono">{{ $fish->records_count }}</span>
                                </div>
                                <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Max Lgth</span>
                                    <span class="text-sm font-black text-slate-900 font-mono">
                                        {{ $fish->longest_record ? $fish->longest_record . '″' : '—' }}
                                    </span>
                                </div>
                                <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Max Weight</span>
                                    <span class="text-sm font-black text-slate-900 font-mono">
                                        {{ $fish->heaviest_record ? $fish->heaviest_record . ' lbs.' : '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions Footer -->
                        <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="/fish/{{ $fish->id }}" class="text-xs font-extrabold text-teal-700 hover:text-teal-800 flex items-center gap-1.5 px-2 py-1 rounded-lg hover:bg-teal-50 transition-colors">
                                <span>View Dossier</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>

                            <a 
                                href="/fish/breed/{{ $fish->id }}/edit" 
                                class="p-1.5 bg-white hover:bg-slate-100 text-slate-600 rounded-xl border border-slate-200 transition-colors flex items-center gap-1 text-xs font-medium" 
                                title="Edit Species"
                            >
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                <span class="text-[11px] font-semibold text-slate-600">Edit</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty Search / Filter State -->
            <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80 shadow-sm space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 text-slate-400 border border-slate-200 flex items-center justify-center mx-auto">
                    <i data-lucide="fish-off" class="w-8 h-8"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-slate-800">No fish species found</h3>
                    <p class="text-xs text-slate-500">No species match your current search or family filter criteria.</p>
                </div>
                <a href="/fish" class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Reset All Filters</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Content Area: Compact Taxonomy Table View with Livewire Search & Multi-Sort -->
    <div x-show="viewMode === 'table'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\FishBreed::class,
            'columns' => [
                ['key' => 'name', 'label' => 'Species & Taxonomy', 'type' => 'species_avatar', 'sortable' => true, 'searchable' => true],
                ['key' => 'family.name', 'label' => 'Family', 'type' => 'family_badge', 'sortable' => true, 'sortKey' => 'family'],
                ['key' => 'records_count', 'label' => 'Total Logged', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'records_count'],
                ['key' => 'longest_record', 'label' => 'Longest Record', 'type' => 'lunker_record', 'align' => 'center', 'sortable' => true, 'sortKey' => 'longest_record'],
                ['key' => 'heaviest_record', 'label' => 'Heaviest Record', 'type' => 'heavy_record', 'align' => 'center', 'sortable' => true, 'sortKey' => 'heaviest_record'],
            ],
            'searchPlaceholder' => 'Quick filter species by name or family...',
            'itemName' => 'species',
            'perPage' => 15,
            'defaultSortBy' => 'name',
            'defaultSortOrder' => 'asc',
        ])
    </div>

    <!-- Pagination -->
    @if($fishes->hasPages())
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <span>Showing {{ $fishes->firstItem() }} to {{ $fishes->lastItem() }} of {{ $fishes->total() }} Species</span>
            <div>{{ $fishes->links() }}</div>
        </div>
    @endif
</div>
@endsection
