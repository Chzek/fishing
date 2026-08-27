<div x-data="dataTable({ defaultDensity: 'normal' })" class="space-y-4">
    <!-- Livewire Interactive Toolbar styled with x-table.wrapper design system -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 p-3 sm:p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
        <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[240px]">
            <!-- Database Search Input -->
            <div class="inline-flex items-center flex-1 min-w-[200px] max-w-md shadow-2xs rounded-lg">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="{{ $searchPlaceholder }}" 
                        class="w-full h-8.5 pl-9 pr-8 text-xs rounded-lg border border-slate-200 bg-white font-medium text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all"
                    />
                    @if($search)
                        <button 
                            wire:click="$set('search', '')" 
                            type="button"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer"
                            title="Clear search"
                        >
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    @endif
                </div>
            </div>

            @if($search)
                <button 
                    wire:click="resetFilters" 
                    type="button" 
                    class="text-xs text-slate-500 hover:text-slate-800 font-medium transition-colors underline cursor-pointer"
                >
                    Reset Search
                </button>
            @endif
        </div>

        <!-- Right Side Controls: Row Counter & Column Visibility Picker -->
        <div class="flex items-center justify-between sm:justify-end gap-2.5 shrink-0 text-xs">
            <!-- Row Counter -->
            <div class="flex items-center gap-1.5 text-slate-500 font-medium font-mono text-[11px] bg-white px-2.5 py-1 rounded-lg border border-slate-200/80 shadow-2xs">
                <span class="w-2 h-2 rounded-full {{ $search ? 'bg-amber-400' : 'bg-teal-500' }}"></span>
                <span>{{ number_format($totalCount) }} {{ $itemName }}</span>
            </div>

            <!-- Column Visibility Picker -->
            <div class="relative" x-data="{ open: false }">
                <button 
                    type="button" 
                    @click="open = !open" 
                    class="h-8 px-2.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold rounded-lg border border-slate-200/80 shadow-2xs flex items-center gap-1.5 transition-colors cursor-pointer"
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
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="overflow-x-auto rounded-xl border border-slate-200/80 shadow-2xs bg-white">
        <table class="w-full text-left text-sm text-slate-700">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    @foreach($columns as $col)
                        @php
                            $align = $col['align'] ?? 'left';
                            $alignClass = $align === 'center' ? 'text-center' : ($align === 'right' ? 'text-right' : 'text-left');
                            $isSortable = $col['sortable'] ?? true;
                            $colKey = $col['key'];
                        @endphp
                        <th 
                            scope="col" 
                            data-col="{{ $colKey }}" 
                            data-col-label="{{ $col['label'] }}" 
                            x-show="isColumnVisible('{{ $colKey }}')" 
                            class="py-3 px-4 {{ $alignClass }} {{ $isSortable ? 'select-none cursor-pointer group' : '' }}"
                            @if($isSortable) wire:click="sortByColumn('{{ $colKey }}')" @endif
                        >
                            <div class="inline-flex items-center gap-1.5 {{ $align === 'center' ? 'justify-center' : ($align === 'right' ? 'justify-end' : 'justify-start') }} hover:text-teal-600">
                                <span>{{ $col['label'] }}</span>
                                @if($isSortable)
                                    @if($sortBy === $colKey)
                                        <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                                    @else
                                        <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                                    @endif
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                @forelse($records as $record)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        @foreach($columns as $col)
                            @php
                                $colKey = $col['key'];
                                $type = $col['type'] ?? 'text';
                                $align = $col['align'] ?? 'left';
                                $alignClass = $align === 'center' ? 'text-center' : ($align === 'right' ? 'text-right' : 'text-left');
                                $val = data_get($record, $colKey);
                            @endphp
                            <td 
                                data-col="{{ $colKey }}" 
                                x-show="isColumnVisible('{{ $colKey }}')" 
                                :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" 
                                class="{{ $alignClass }} whitespace-nowrap text-xs"
                            >
                                @if($type === 'link')
                                    @php
                                        $urlPath = isset($col['urlPrefix']) ? $col['urlPrefix'] . '/' . data_get($record, $col['urlParam'] ?? 'id') : '#';
                                    @endphp
                                    <a href="{{ url($urlPath) }}" class="font-semibold text-slate-900 hover:text-teal-600 hover:underline">
                                        {{ $val ?? '—' }}
                                    </a>
                                @elseif($type === 'badge')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-50 text-teal-700 border border-teal-200/80">
                                        {{ $val ?? '—' }}
                                    </span>
                                @elseif($type === 'count')
                                    <span class="font-mono font-bold text-slate-800">
                                        {{ number_format((int) ($val ?? 0)) }}
                                    </span>
                                @elseif($type === 'date')
                                    <span class="font-mono text-slate-600">
                                        {{ $val ?? '—' }}
                                    </span>
                                @else
                                    <span class="font-medium text-slate-700">
                                        {{ $val ?? '—' }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right whitespace-nowrap font-medium text-xs">
                            @if(method_exists($record, 'getTable'))
                                @php
                                    $tbl = $record->getTable();
                                    $detailUrl = $tbl === 'lakes' ? url('/lake/' . $record->id) : ($tbl === 'anglers' ? url('/angler/' . $record->id . '/profile') : url('#'));
                                @endphp
                                <a href="{{ $detailUrl }}" class="text-teal-600 hover:text-teal-900 font-semibold hover:underline">
                                    View Details →
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="py-8 px-4 text-center">
                            <x-emptyState icon="database-off" title="No Records Found" description="No entries match your current search and filter criteria." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Bottom Toolbar Container with Pagination & Relocated Density Toggle -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-3 sm:p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
        <div class="flex-1">
            {{ $records->links('livewire.pagination.tailwind') }}
        </div>

        <!-- Relocated Density Toggle -->
        <div class="flex items-center justify-end shrink-0">
            <div class="inline-flex rounded-lg border border-slate-200/80 bg-white p-0.5 shadow-2xs">
                <button 
                    type="button" 
                    @click="setDensity('compact')" 
                    :class="density === 'compact' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-2.5 py-1 rounded text-[11px] font-medium transition-colors cursor-pointer"
                    title="Compact density"
                >
                    Compact
                </button>
                <button 
                    type="button" 
                    @click="setDensity('normal')" 
                    :class="density === 'normal' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-2.5 py-1 rounded text-[11px] font-medium transition-colors cursor-pointer"
                    title="Comfortable density"
                >
                    Comfortable
                </button>
            </div>
        </div>
    </div>
</div>
