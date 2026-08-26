<div x-data="dataTable({ defaultDensity: 'normal' })" class="space-y-4">
    <!-- Livewire Interactive Toolbar styled with x-table.wrapper design system -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 p-3 sm:p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
        <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[240px]">
            <!-- Database Search Input -->
            <div class="inline-flex items-center flex-1 min-w-[200px] shadow-2xs rounded-lg">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search species, lake, angler, lure..." 
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

            <!-- Species Dropdown -->
            <select wire:model.live="species" class="h-8.5 px-3 text-xs rounded-lg border border-slate-200 bg-white font-semibold text-slate-700 focus:ring-2 focus:ring-teal-500/20 cursor-pointer">
                <option value="">All Species</option>
                @foreach($speciesList as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                @endforeach
            </select>

            <!-- Lake Dropdown -->
            <select wire:model.live="lake" class="h-8.5 px-3 text-xs rounded-lg border border-slate-200 bg-white font-semibold text-slate-700 focus:ring-2 focus:ring-teal-500/20 cursor-pointer">
                <option value="">All Lakes & Waters</option>
                @foreach($lakesList as $lk)
                    <option value="{{ $lk->id }}">{{ $lk->name }}</option>
                @endforeach
            </select>

            <!-- Length Operator & Value Filter -->
            <div class="flex items-center gap-1.5 shrink-0">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Length</span>
                <select wire:model.live="lengthOperator" class="h-8.5 px-2 text-xs rounded-lg border border-slate-200 bg-white font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 cursor-pointer">
                    <option value=">">&gt;</option>
                    <option value="=">=</option>
                    <option value="<">&lt;</option>
                </select>
                <input 
                    type="number" 
                    step="0.25" 
                    wire:model.live.debounce.300ms="length" 
                    placeholder="Inches..." 
                    class="h-8.5 px-2.5 w-20 text-xs rounded-lg border border-slate-200 bg-white font-mono text-slate-800 focus:ring-2 focus:ring-teal-500/20"
                />
            </div>

            @if($search || $species || $lake || $length !== '' || $angler)
                <button 
                    wire:click="resetFilters" 
                    type="button" 
                    class="text-xs text-slate-500 hover:text-slate-800 font-medium transition-colors underline cursor-pointer"
                >
                    Reset Filters
                </button>
            @endif
        </div>

        <!-- Controls: Row Counter, Column Visibility, Density -->
        <div class="flex items-center justify-between sm:justify-end gap-2.5 shrink-0 text-xs">
            <!-- Row Counter -->
            <div class="flex items-center gap-1.5 text-slate-500 font-medium font-mono text-[11px] bg-white px-2.5 py-1 rounded-lg border border-slate-200/80 shadow-2xs">
                <span class="w-2 h-2 rounded-full {{ $search || $species || $lake || $length !== '' ? 'bg-amber-400' : 'bg-teal-500' }}"></span>
                <span>{{ number_format($totalCount) }} catches</span>
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

            <!-- Density Toggle -->
            <div class="inline-flex rounded-lg border border-slate-200/80 bg-white p-0.5 shadow-2xs">
                <button 
                    type="button" 
                    @click="setDensity('compact')" 
                    :class="density === 'compact' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-2 py-1 rounded text-[11px] font-medium transition-colors cursor-pointer"
                    title="Compact density"
                >
                    Compact
                </button>
                <button 
                    type="button" 
                    @click="setDensity('normal')" 
                    :class="density === 'normal' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-2 py-1 rounded text-[11px] font-medium transition-colors cursor-pointer"
                    title="Comfortable density"
                >
                    Comfortable
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Container matching x-table.wrapper design system -->
    <div class="overflow-x-auto rounded-xl border border-slate-200/80 shadow-2xs bg-white">
        <table class="w-full text-left text-sm text-slate-700">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th scope="col" data-col="date" data-col-label="Date" x-show="isColumnVisible('date')" class="py-3 px-4 text-left select-none cursor-pointer group" wire:click="sortByColumn('date')">
                        <div class="inline-flex items-center gap-1.5 hover:text-teal-600">
                            <span>Date</span>
                            @if($sortBy === 'date' || $sortBy === 'caught')
                                <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" data-col="angler" data-col-label="Angler" x-show="isColumnVisible('angler')" class="py-3 px-4 text-left select-none cursor-pointer group" wire:click="sortByColumn('angler')">
                        <div class="inline-flex items-center gap-1.5 hover:text-teal-600">
                            <span>Angler</span>
                            @if($sortBy === 'angler')
                                <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" data-col="lake" data-col-label="Lake / Water" x-show="isColumnVisible('lake')" class="py-3 px-4 text-left select-none cursor-pointer group" wire:click="sortByColumn('lake')">
                        <div class="inline-flex items-center gap-1.5 hover:text-teal-600">
                            <span>Lake / Water</span>
                            @if($sortBy === 'lake')
                                <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" data-col="species" data-col-label="Fish Species" x-show="isColumnVisible('species')" class="py-3 px-4 text-left select-none cursor-pointer group" wire:click="sortByColumn('species')">
                        <div class="inline-flex items-center gap-1.5 hover:text-teal-600">
                            <span>Fish Species</span>
                            @if($sortBy === 'species')
                                <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" data-col="lure" data-col-label="Lure / Bait" x-show="isColumnVisible('lure')" class="py-3 px-4 text-left select-none cursor-pointer group" wire:click="sortByColumn('lure')">
                        <div class="inline-flex items-center gap-1.5 hover:text-teal-600">
                            <span>Lure / Bait</span>
                            @if($sortBy === 'lure')
                                <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" data-col="weight" data-col-label="Weight" x-show="isColumnVisible('weight')" class="py-3 px-4 text-center select-none cursor-pointer group" wire:click="sortByColumn('weight')">
                        <div class="inline-flex items-center justify-center gap-1.5 hover:text-teal-600">
                            <span>Weight (lbs)</span>
                            @if($sortBy === 'weight')
                                <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" data-col="length" data-col-label="Length" x-show="isColumnVisible('length')" class="py-3 px-4 text-center select-none cursor-pointer group" wire:click="sortByColumn('length')">
                        <div class="inline-flex items-center justify-center gap-1.5 hover:text-teal-600">
                            <span>Length (in)</span>
                            @if($sortBy === 'length')
                                <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" data-col="status" data-col-label="Status" x-show="isColumnVisible('status')" class="py-3 px-4 text-center select-none cursor-pointer group" wire:click="sortByColumn('status')">
                        <div class="inline-flex items-center justify-center gap-1.5 hover:text-teal-600">
                            <span>Status</span>
                            @if($sortBy === 'status')
                                <span class="text-teal-600 font-bold text-[10px] bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">{{ strtolower($sortOrder) === 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">⇅</span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                @forelse($records as $record)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td data-col="date" x-show="isColumnVisible('date')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-medium text-slate-900 whitespace-nowrap font-mono text-xs">{{ $record->caught }}</td>
                        <td data-col="angler" x-show="isColumnVisible('angler')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-semibold text-slate-800 whitespace-nowrap">
                            @if($record->angler)
                                <a href="{{ url('/angler/' . $record->angler->id . '/profile') }}" class="hover:text-teal-600 hover:underline">
                                    {{ $record->angler->full_name }}
                                </a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td data-col="lake" x-show="isColumnVisible('lake')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-slate-700 whitespace-nowrap">
                            @if($record->lake)
                                <a href="{{ url('/lake/' . $record->lake->id) }}" class="hover:text-teal-600 hover:underline">
                                    {{ $record->lake->name }}
                                </a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td data-col="species" x-show="isColumnVisible('species')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-bold text-teal-700 whitespace-nowrap">{{ $record->fishBreed->name ?? 'Unknown' }}</td>
                        <td data-col="lure" x-show="isColumnVisible('lure')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-xs text-slate-600">
                            @if($record->lure)
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium border border-slate-200">
                                    {{ $record->lure->name }}
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td data-col="weight" x-show="isColumnVisible('weight')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-800 whitespace-nowrap">
                            {{ $record->weight ? number_format($record->weight, 2) : '—' }}
                        </td>
                        <td data-col="length" x-show="isColumnVisible('length')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-900 whitespace-nowrap">
                            {{ $record->length ? number_format($record->length, 1) : '—' }}
                        </td>
                        <td data-col="status" x-show="isColumnVisible('status')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center whitespace-nowrap">
                            <x-statusBadge :type="$record->released ? 'released' : 'kept'" />
                        </td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right whitespace-nowrap font-medium text-xs">
                            <a href="{{ route('record.show', $record->id) }}" class="text-teal-600 hover:text-teal-900 font-semibold hover:underline">
                                View Details →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-8 px-4 text-center">
                            <x-emptyState icon="fish-off" title="No Catch Records Found" description="No catch entries match your current search and filter criteria." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="pt-2">
            {{ $records->links('livewire.pagination.tailwind') }}
        </div>
    @endif
</div>
