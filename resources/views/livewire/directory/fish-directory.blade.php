<div class="space-y-6">
    <!-- Search & Filter Controls -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search species or biological family..."
                        class="w-full h-10 pl-10 pr-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors placeholder:text-slate-400 dark:placeholder:text-slate-500"
                    >
                    <x-lucide-search class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3.5 top-3" />
                    @if(!empty($search))
                        <button 
                            type="button" 
                            wire:click="$set('search', '')" 
                            class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer" 
                            title="Clear Search"
                        >
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    @endif
                </div>
            </div>

            <!-- View Mode Switcher & Active Filter Reset -->
            <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                @if(!empty($search) || !empty($family))
                    <button 
                        type="button" 
                        wire:click="clearFilters" 
                        class="px-3 py-1.5 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl border border-rose-200 dark:border-rose-900/60 font-semibold transition-colors flex items-center gap-1.5 cursor-pointer"
                        title="Reset all search and family filters"
                    >
                        <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                        <span>Reset Filters</span>
                    </button>
                @endif

                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200/80 dark:border-slate-700 shrink-0">
                    <button 
                        type="button" 
                        wire:click="setViewMode('grid')" 
                        class="px-3 py-1.5 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer {{ $viewMode === 'grid' ? 'bg-white dark:bg-slate-700 text-teal-700 dark:text-teal-400 shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium' }}"
                    >
                        <x-lucide-layout-grid class="w-3.5 h-3.5" />
                        <span>Grid View</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="setViewMode('table')" 
                        class="px-3 py-1.5 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer {{ $viewMode === 'table' ? 'bg-white dark:bg-slate-700 text-teal-700 dark:text-teal-400 shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium' }}"
                    >
                        <x-lucide-list class="w-3.5 h-3.5" />
                        <span>Table View</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Biological Family Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
            <span class="text-slate-400 dark:text-slate-500 font-bold uppercase text-[10px] tracking-wider shrink-0 flex items-center gap-1">
                <x-lucide-filter class="w-3 h-3 text-slate-400 dark:text-slate-500" /> Families:
            </span>

            <button 
                type="button" 
                wire:click="selectFamily('')" 
                class="px-3 py-1 rounded-lg font-semibold transition-colors shrink-0 cursor-pointer {{ empty($family) ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' }}"
            >
                All Families ({{ $totalBreedsCount }})
            </button>

            @foreach($families as $fam)
                <button 
                    type="button" 
                    wire:click="selectFamily('{{ $fam->id }}')" 
                    class="px-3 py-1 rounded-lg font-semibold transition-colors shrink-0 flex items-center gap-1.5 cursor-pointer {{ $family == $fam->id ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' }}"
                >
                    <span>{{ $fam->name }}</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $family == $fam->id ? 'bg-white/20 text-white' : 'bg-slate-200/80 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                        {{ $fam->breeds_count }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Content Area with Loading Mask -->
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity duration-150">
        @if($fishes->count() > 0)
            @if($viewMode === 'grid')
                <!-- Grid View -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($fishes as $fish)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden flex flex-col justify-between hover:shadow-md hover:border-teal-500/40 dark:hover:border-teal-500/40 transition-all group">
                            <div class="p-4 space-y-3">
                                <div class="w-full h-36 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 flex items-center justify-center p-3 relative overflow-hidden group-hover:bg-teal-50/20 dark:group-hover:bg-teal-950/20 transition-colors">
                                    @if($fish->imageUrl)
                                        <img src="{{ $fish->imageUrl }}" alt="{{ $fish->name }}" class="max-h-full max-w-full object-contain filter drop-shadow-sm group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="text-center text-slate-300 dark:text-slate-600">
                                            <x-lucide-fish class="w-12 h-12 mx-auto stroke-[1.25]" />
                                            <span class="text-[10px] font-medium block mt-1">No Illustration</span>
                                        </div>
                                    @endif

                                    @if($fish->family)
                                        <span class="absolute top-2 left-2 text-[10px] font-bold text-slate-600 dark:text-slate-300 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xs px-2 py-0.5 rounded-md border border-slate-200/70 dark:border-slate-700/70 shadow-2xs">
                                            {{ $fish->family->name }}
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <h3 class="font-black text-slate-900 dark:text-white text-base group-hover:text-teal-700 dark:group-hover:text-teal-400 transition-colors">
                                        <a href="/fish/{{ $fish->id }}">{{ $fish->name }}</a>
                                    </h3>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 italic">Biological Profile & Telemetry</p>
                                </div>

                                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-center">
                                    <div class="p-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Catches</span>
                                        <span class="text-sm font-black text-teal-700 dark:text-teal-400 font-mono">{{ $fish->records_count }}</span>
                                    </div>
                                    <div class="p-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Max Lgth</span>
                                        <span class="text-sm font-black text-slate-900 dark:text-white font-mono">
                                            {{ $fish->longest_record ? ($fish->longest_record + 0) . '″' : '—' }}
                                        </span>
                                    </div>
                                    <div class="p-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Max Weight</span>
                                        <span class="text-sm font-black text-slate-900 dark:text-white font-mono">
                                            {{ $fish->heaviest_record ? ($fish->heaviest_record + 0) . ' lbs.' : '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Actions Footer -->
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                                <a href="/fish/{{ $fish->id }}" class="text-xs font-extrabold text-teal-700 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 flex items-center gap-1.5 px-2 py-1 rounded-lg hover:bg-teal-50 dark:hover:bg-teal-950/50 transition-colors">
                                    <span>View Dossier</span>
                                    <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                </a>

                                <a 
                                    href="/fish/breed/{{ $fish->id }}/edit" 
                                    class="p-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors flex items-center gap-1 text-xs font-medium" 
                                    title="Edit Species"
                                >
                                    <x-lucide-edit-3 class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
                                    <span class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Edit</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Table View -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-950 text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                                <tr>
                                    <th scope="col" wire:click="sortByColumn('name')" class="py-3.5 px-4 text-left select-none cursor-pointer group hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span>Species & Taxonomy</span>
                                            @if($sortBy === 'name')
                                                <x-lucide-chevron-up class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400 {{ $sortOrder === 'desc' ? 'rotate-180' : '' }} transition-transform" />
                                            @else
                                                <x-lucide-chevrons-up-down class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover:text-slate-400" />
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" wire:click="sortByColumn('family')" class="py-3.5 px-4 text-left select-none cursor-pointer group hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span>Family</span>
                                            @if($sortBy === 'family' || $sortBy === 'family.name')
                                                <x-lucide-chevron-up class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400 {{ $sortOrder === 'desc' ? 'rotate-180' : '' }} transition-transform" />
                                            @else
                                                <x-lucide-chevrons-up-down class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover:text-slate-400" />
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" wire:click="sortByColumn('records_count')" class="py-3.5 px-4 text-center select-none cursor-pointer group hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <span>Total Logged</span>
                                            @if($sortBy === 'records_count')
                                                <x-lucide-chevron-up class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400 {{ $sortOrder === 'desc' ? 'rotate-180' : '' }} transition-transform" />
                                            @else
                                                <x-lucide-chevrons-up-down class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover:text-slate-400" />
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" wire:click="sortByColumn('longest_record')" class="py-3.5 px-4 text-center select-none cursor-pointer group hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <span>Longest Record</span>
                                            @if($sortBy === 'longest_record')
                                                <x-lucide-chevron-up class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400 {{ $sortOrder === 'desc' ? 'rotate-180' : '' }} transition-transform" />
                                            @else
                                                <x-lucide-chevrons-up-down class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover:text-slate-400" />
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" wire:click="sortByColumn('heaviest_record')" class="py-3.5 px-4 text-center select-none cursor-pointer group hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <span>Heaviest Record</span>
                                            @if($sortBy === 'heaviest_record')
                                                <x-lucide-chevron-up class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400 {{ $sortOrder === 'desc' ? 'rotate-180' : '' }} transition-transform" />
                                            @else
                                                <x-lucide-chevrons-up-down class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover:text-slate-400" />
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="py-3.5 px-4 text-right">
                                        <span>Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($fishes as $fish)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors group">
                                        <!-- Species & Taxonomy Avatar Column -->
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <x-fishAvatar :breed="$fish" size="sm" />
                                                <div>
                                                    <a href="{{ url('/fish/' . $fish->id) }}" class="font-bold text-slate-900 dark:text-slate-100 hover:text-teal-600 dark:hover:text-teal-400 hover:underline text-xs sm:text-sm">
                                                        {{ $fish->name }}
                                                    </a>
                                                    @if($fish->family)
                                                        <span class="block sm:hidden text-[11px] text-slate-400 dark:text-slate-500">
                                                            {{ $fish->family->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Family Badge Column -->
                                        <td class="py-3 px-4">
                                            @if($fish->family)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-transparent dark:border-slate-700/80">
                                                    {{ $fish->family->name }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs italic">N/A</span>
                                            @endif
                                        </td>

                                        <!-- Total Logged Column -->
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold font-mono {{ $fish->records_count > 0 ? 'bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500' }}">
                                                {{ $fish->records_count }}
                                            </span>
                                        </td>

                                        <!-- Longest Record Column -->
                                        <td class="py-3 px-4 text-center font-mono text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $fish->longest_record ? ($fish->longest_record + 0) . ' in.' : '—' }}
                                        </td>

                                        <!-- Heaviest Record Column -->
                                        <td class="py-3 px-4 text-center font-mono text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $fish->heaviest_record ? ($fish->heaviest_record + 0) . ' lbs.' : '—' }}
                                        </td>

                                        <!-- Actions Column -->
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ url('/fish/' . $fish->id) }}" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-950/50 rounded-lg transition-colors" title="View Dossier">
                                                    <x-lucide-eye class="w-4 h-4" />
                                                </a>
                                                <a href="{{ url('/fish/breed/' . $fish->id . '/edit') }}" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Edit Species">
                                                    <x-lucide-edit-3 class="w-4 h-4" />
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-12 text-center border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 flex items-center justify-center mx-auto">
                    <x-lucide-fish-off class="w-8 h-8" />
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">No fish species found</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">No species match your current search or family filter criteria.</p>
                </div>
                <button type="button" wire:click="clearFilters" class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors cursor-pointer">
                    <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                    <span>Reset All Filters</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Unified Livewire Pagination Bar -->
    @if($fishes->hasPages())
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-3 sm:p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 transition-colors text-xs">
            <div class="flex-1">
                {{ $fishes->links('livewire.pagination.tailwind') }}
            </div>

            <!-- Per-Page Selector & Counter -->
            <div class="flex items-center justify-between md:justify-end gap-3 shrink-0 text-slate-600 dark:text-slate-400 font-medium">
                <span>Showing {{ $fishes->firstItem() }} to {{ $fishes->lastItem() }} of {{ $fishes->total() }} Species</span>

                <div class="flex items-center gap-1.5">
                    <span>Show:</span>
                    <select 
                        wire:model.live="perPage" 
                        class="h-8 px-2 rounded-lg border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-800/90 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-2xs cursor-pointer transition-colors"
                    >
                        @if($viewMode === 'grid')
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                        @else
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        @endif
                    </select>
                    <span class="text-slate-400">/ page</span>
                </div>
            </div>
        </div>
    @endif
</div>
