<div class="space-y-4" x-data="dataTable({ defaultDensity: 'normal' })">
    <!-- Livewire Interactive Toolbar styled with x-table.wrapper design system -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 shadow-2xs">
        <div class="flex flex-wrap items-center gap-2.5 flex-1">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[200px]">
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

            <!-- Species Dropdown -->
            <select wire:model.live="speciesId" class="h-8.5 px-3 text-xs rounded-lg border border-slate-200 bg-white font-semibold text-slate-700 focus:ring-2 focus:ring-teal-500/20 cursor-pointer">
                <option value="">All Species</option>
                @foreach($speciesList as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                @endforeach
            </select>

            <!-- Lake Dropdown -->
            <select wire:model.live="lakeId" class="h-8.5 px-3 text-xs rounded-lg border border-slate-200 bg-white font-semibold text-slate-700 focus:ring-2 focus:ring-teal-500/20 cursor-pointer">
                <option value="">All Lakes & Waters</option>
                @foreach($lakesList as $lk)
                    <option value="{{ $lk->id }}">{{ $lk->name }}</option>
                @endforeach
            </select>

            <!-- Sort By Dropdown -->
            <select wire:model.live="sortBy" class="h-8.5 px-3 text-xs rounded-lg border border-slate-200 bg-white font-semibold text-slate-700 focus:ring-2 focus:ring-teal-500/20 cursor-pointer">
                <option value="caught_desc">Date (Newest First)</option>
                <option value="caught_asc">Date (Oldest First)</option>
                <option value="length_desc">Longest Catch</option>
                <option value="weight_desc">Heaviest Catch</option>
            </select>
        </div>

        <div class="flex items-center justify-between md:justify-end gap-3 shrink-0 text-xs">
            <label class="inline-flex items-center cursor-pointer gap-2 select-none">
                <input type="checkbox" wire:model.live="releasedOnly" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 w-3.5 h-3.5">
                <span class="text-xs font-semibold text-slate-600">Released Only</span>
            </label>

            <button 
                wire:click="resetFilters" 
                type="button" 
                class="text-xs text-slate-500 hover:text-slate-800 font-medium transition-colors underline cursor-pointer"
            >
                Reset Filters
            </button>

            <!-- Row Counter Badge -->
            <div class="flex items-center gap-1.5 text-slate-500 font-medium font-mono text-[11px] bg-white px-2.5 py-1 rounded-lg border border-slate-200/80 shadow-2xs">
                <span class="w-2 h-2 rounded-full {{ $search || $speciesId || $lakeId ? 'bg-amber-400' : 'bg-teal-500' }}"></span>
                <span>{{ $records->total() }} Catches</span>
            </div>

            <!-- Density Toggle -->
            <div class="inline-flex rounded-lg border border-slate-200/80 bg-white p-0.5 shadow-2xs">
                <button 
                    type="button" 
                    @click="setDensity('compact')" 
                    :class="density === 'compact' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-2 py-1 rounded text-[11px] font-medium transition-colors cursor-pointer"
                >
                    Compact
                </button>
                <button 
                    type="button" 
                    @click="setDensity('normal')" 
                    :class="density === 'normal' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-2 py-1 rounded text-[11px] font-medium transition-colors cursor-pointer"
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
                    <th scope="col" class="py-3 px-4">Date</th>
                    <th scope="col" class="py-3 px-4">Angler</th>
                    <th scope="col" class="py-3 px-4">Lake / Water</th>
                    <th scope="col" class="py-3 px-4">Fish Species</th>
                    <th scope="col" class="py-3 px-4">Lure / Bait</th>
                    <th scope="col" class="py-3 px-4 text-center">Weight (lbs)</th>
                    <th scope="col" class="py-3 px-4 text-center">Length (in)</th>
                    <th scope="col" class="py-3 px-4 text-center">Status</th>
                    <th scope="col" class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($records as $rec)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-medium text-slate-900 whitespace-nowrap font-mono text-xs">{{ $rec->caught }}</td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-semibold text-slate-800 whitespace-nowrap">
                            @if($rec->angler)
                                <a href="{{ url('/angler/' . $rec->angler->id . '/profile') }}" class="hover:text-teal-600 hover:underline">
                                    {{ $rec->angler->full_name }}
                                </a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-slate-700 whitespace-nowrap">
                            @if($rec->lake)
                                <a href="{{ url('/lake/' . $rec->lake->id) }}" class="hover:text-teal-600 hover:underline">
                                    {{ $rec->lake->name }}
                                </a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-bold text-teal-700 whitespace-nowrap">{{ $rec->fishBreed->name ?? 'Unknown' }}</td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-xs text-slate-600">
                            @if($rec->lure)
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium border border-slate-200">
                                    {{ $rec->lure->name }}
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-800 whitespace-nowrap">
                            {{ $rec->weight ? number_format($rec->weight, 2) : '—' }}
                        </td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-900 whitespace-nowrap">
                            {{ $rec->length ? number_format($rec->length, 1) : '—' }}
                        </td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center whitespace-nowrap">
                            <x-statusBadge :type="$rec->released ? 'released' : 'kept'" />
                        </td>
                        <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right whitespace-nowrap font-medium text-xs">
                            <a href="{{ route('record.show', $rec->id) }}" class="text-teal-600 hover:text-teal-900 font-semibold hover:underline">
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
            {{ $records->links() }}
        </div>
    @endif
</div>
