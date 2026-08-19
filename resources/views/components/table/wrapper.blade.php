@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'searchPlaceholder' => 'Search database...',
    'showDensity' => true,
    'showColumnPicker' => false,
    'showCounter' => false,
    'totalCount' => null,
    'itemName' => 'rows',
])

<div class="space-y-4" {{ $attributes }}>
    <!-- Table Interactive Toolbar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 p-3 sm:p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
        <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[240px]">
            <!-- Database Search Form -->
            <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-center gap-2 flex-1 min-w-[240px]">
                @if(request('sort_by'))
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}" />
                @endif
                @if(request('sort_order'))
                    <input type="hidden" name="sort_order" value="{{ request('sort_order') }}" />
                @endif

                <div class="relative flex-1 min-w-[180px] max-w-md">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}" 
                        placeholder="{{ $searchPlaceholder }}"
                        class="w-full h-8.5 pl-9 pr-8 text-xs rounded-lg border border-slate-200 bg-white font-medium text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all"
                    />
                    @if(request('search'))
                        <a 
                            href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => 1]) }}" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5"
                            title="Clear search"
                        >
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>

                @if(isset($extraFilters))
                    {{ $extraFilters }}
                @endif
            </form>

            <!-- Active Database Sort & Filter Badges -->
            @if(request('sort_by') || request('search') || request('length') || request('angler'))
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-teal-50 border border-teal-200/80 rounded-lg text-xs font-semibold text-teal-800">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span>
                        @if(request('search')) Search: "{{ request('search') }}" @endif
                        @if(request('length')) | Length {{ request('length_operator', '>') }} {{ request('length') }}" @endif
                        @if(request('sort_by')) | Sorted by: {{ request('sort_by') }} ({{ request('sort_order', 'asc') }}) @endif
                    </span>
                    <a 
                        href="{{ request()->url() }}" 
                        class="ml-1 text-teal-600 hover:text-teal-900 font-bold underline text-[11px]"
                        title="Reset search and sort filters"
                    >
                        Reset All
                    </a>
                </div>
            @endif

        </div>

        <!-- Controls: Column Visibility, Density -->
        <div class="flex items-center justify-between sm:justify-end gap-2.5 shrink-0 text-xs">
            @if($showCounter && $totalCount !== null)
                <!-- Row Counter -->
                <div class="flex items-center gap-1.5 text-slate-500 font-medium font-mono text-[11px] bg-white px-2.5 py-1 rounded-lg border border-slate-200/80 shadow-2xs">
                    <span class="w-2 h-2 rounded-full {{ request('search') ? 'bg-amber-400' : 'bg-teal-500' }}"></span>
                    <span>{{ $totalCount }} {{ $itemName }}</span>
                </div>
            @endif

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
    </div>
</div>

