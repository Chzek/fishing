@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'searchPlaceholder' => 'Quick filter loaded rows...',
    'showDensity' => true,
    'showColumnPicker' => false,
    'totalCount' => null,
    'itemName' => 'rows',
])

<div class="space-y-4" {{ $attributes }}>
    <!-- Table Interactive Toolbar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 p-3 sm:p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
        <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[240px]">
            <!-- Instant Search Input -->
            <div class="relative flex-1 min-w-[180px] max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full h-8.5 pl-9 pr-8 text-xs rounded-lg border border-slate-200 bg-white font-medium text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all"
                />
                <button 
                    type="button" 
                    x-show="search" 
                    @click="clearSearch()" 
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5"
                    title="Clear filter"
                    style="display: none;"
                >
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            <!-- Active Sort Badges & Reset -->
            <template x-if="sortQueue.length > 0">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-teal-50 border border-teal-200/80 rounded-lg text-xs font-semibold text-teal-800 animate-fadeIn">
                    <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span x-text="sortQueue.length === 1 ? 'Sorted: ' + sortQueue[0].col : sortQueue.length + ' Sort Rules'"></span>
                    <button 
                        type="button" 
                        @click="clearSort()" 
                        class="ml-1 text-teal-600 hover:text-teal-900 font-bold underline text-[11px]"
                        title="Reset column sorting"
                    >
                        Reset
                    </button>
                </div>
            </template>
        </div>

        <!-- Controls: Match Counter, Column Visibility, Density -->
        <div class="flex items-center justify-between sm:justify-end gap-2.5 shrink-0 text-xs">
            <!-- Row Counter -->
            <div class="flex items-center gap-1.5 text-slate-500 font-medium font-mono text-[11px] bg-white px-2.5 py-1 rounded-lg border border-slate-200/80 shadow-2xs">
                <span class="w-2 h-2 rounded-full" :class="search ? 'bg-amber-400' : 'bg-teal-500'"></span>
                <span x-text="visibleRowCount + (totalRows ? ' of ' + totalRows : '') + ' {{ $itemName }}'"></span>
            </div>

            <!-- Column Visibility Picker -->
            @if($showColumnPicker)
                <div class="relative" x-data="{ open: false }">
                    <button 
                        type="button" 
                        @click="open = !open" 
                        class="h-8 px-2.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold rounded-lg border border-slate-200/80 shadow-2xs flex items-center gap-1.5 transition-colors"
                        title="Toggle columns"
                    >
                        <i data-lucide="columns-3" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span class="hidden md:inline">Columns</span>
                    </button>

                    <div 
                        x-show="open" 
                        @click.outside="open = false" 
                        x-transition 
                        class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-lg border border-slate-200 p-2 z-30 space-y-1"
                        style="display: none;"
                    >
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-2 py-1 border-b border-slate-100">
                            Visible Columns
                        </div>
                        <template x-for="col in columns" :key="col.key">
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 text-xs font-medium text-slate-700 cursor-pointer select-none">
                                <input 
                                    type="checkbox" 
                                    :checked="isColumnVisible(col.key)" 
                                    @change="toggleColumn(col.key)" 
                                    class="rounded text-teal-600 focus:ring-teal-500 border-slate-300 w-3.5 h-3.5"
                                />
                                <span x-text="col.label"></span>
                            </label>
                        </template>
                    </div>
                </div>
            @endif

            <!-- Density Toggle -->
            @if($showDensity)
                <div class="inline-flex rounded-lg border border-slate-200/80 bg-white p-0.5 shadow-2xs">
                    <button 
                        type="button" 
                        @click="setDensity('compact')" 
                        :class="density === 'compact' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500 hover:text-slate-800'" 
                        class="px-2 py-1 rounded text-[11px] font-medium transition-colors"
                        title="Compact density"
                    >
                        Compact
                    </button>
                    <button 
                        type="button" 
                        @click="setDensity('normal')" 
                        :class="density === 'normal' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500 hover:text-slate-800'" 
                        class="px-2 py-1 rounded text-[11px] font-medium transition-colors"
                        title="Comfortable density"
                    >
                        Comfortable
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-xl border border-slate-200/80 shadow-2xs bg-white">
        {{ $slot }}

        <!-- Dynamic Empty State when 0 rows match search -->
        <div data-table-empty class="p-8 text-center bg-slate-50/50 border-t border-slate-100" style="display: none;">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                <i data-lucide="filter-x" class="w-6 h-6"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-800">No matching records found</h4>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                No rows match "<span class="font-semibold text-slate-700" x-text="search"></span>". Try adjusting your search term.
            </p>
            <button 
                type="button" 
                @click="clearSearch()" 
                class="mt-3 px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg border border-slate-200 shadow-2xs transition-colors"
            >
                Clear Search Filter
            </button>
        </div>
    </div>
</div>
