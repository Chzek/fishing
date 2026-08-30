<div class="relative w-full" 
     x-data="{ isOpen: @entangle('isOpen') }" 
     x-on:click.outside="isOpen = false" 
     x-on:keydown.escape.window="isOpen = false"
     data-testid="lure-selector-root">

    {{-- Hidden Input for standard form submissions --}}
    <input type="hidden" 
           name="{{ $name }}" 
           id="{{ $name }}" 
           value="{{ $selectedId }}" 
           {{ $required ? 'required' : '' }} 
           data-testid="lure-selector-hidden-input" />

    @if($selectedLure)
        {{-- Selected Lure Active Card Display --}}
        <div class="w-full bg-white rounded-2xl p-3 sm:p-3.5 border border-teal-500/30 bg-gradient-to-r from-teal-50/40 via-white to-sky-50/30 shadow-xs flex items-center justify-between gap-3 transition-all hover:border-teal-500/50"
             data-testid="lure-selected-card">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <x-lucide-fishing-hook class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($selectedLure->brand)
                            <span class="inline-flex items-center text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200/70">
                                {{ $selectedLure->brand }}
                            </span>
                        @endif
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-teal-100/70 text-teal-800 border border-teal-200/50">
                            {{ $selectedLure->category ?: 'Lure' }}
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2 mt-0.5 min-w-0">
                        <span class="font-bold text-slate-900 text-sm truncate" data-testid="lure-selected-name">
                            {{ $selectedLure->name }}
                        </span>
                        @if($selectedLure->color)
                            <span class="text-xs text-slate-600 font-medium truncate">
                                • {{ $selectedLure->color }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-[11px] text-slate-500 font-medium">
                        @if($selectedLure->size || $selectedLure->weight)
                            <span class="font-mono text-slate-600">
                                {{ implode(' • ', array_filter([$selectedLure->size, $selectedLure->weight])) }}
                            </span>
                        @endif
                        @if($selectedLure->depth_range)
                            <span class="inline-flex items-center gap-1 text-sky-700">
                                <x-lucide-arrow-down-to-line class="w-3 h-3 text-sky-500" />
                                {{ $selectedLure->depth_range }}
                            </span>
                        @endif
                        @if($selectedLure->records_count > 0)
                            <span class="inline-flex items-center gap-1 text-teal-700 font-semibold">
                                <x-lucide-fish class="w-3 h-3 text-teal-600" />
                                {{ $selectedLure->records_count }} {{ \Illuminate\Support\Str::plural('catch', $selectedLure->records_count) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" 
                        wire:click="openDropdown"
                        x-on:click="isOpen = true"
                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors flex items-center gap-1 cursor-pointer"
                        data-testid="lure-change-button"
                        title="Change Lure">
                    <x-lucide-search class="w-3.5 h-3.5 text-slate-500" />
                    <span class="hidden sm:inline">Change</span>
                </button>
                @if($allowClear)
                    <button type="button" 
                            wire:click="clearSelection" 
                            class="p-1.5 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-xl transition-colors cursor-pointer"
                            data-testid="lure-clear-button"
                            title="Remove Lure">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                @endif
            </div>
        </div>
    @else
        {{-- Search Input Trigger --}}
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <x-lucide-search class="w-4 h-4" wire:loading.remove wire:target="search" />
                <x-lucide-loader-2 class="w-4 h-4 animate-spin text-teal-600" wire:loading wire:target="search" />
            </div>

            <input type="text"
                   wire:model.live.debounce.250ms="search"
                   x-on:focus="isOpen = true"
                   placeholder="{{ $placeholder }}"
                   class="w-full h-11 pl-10 pr-10 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                   {{ $disabled ? 'disabled' : '' }}
                   data-testid="lure-search-input" />

            @if(trim($search) !== '')
                <button type="button"
                        wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-8 pr-1 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                        title="Clear query">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            @endif

            <button type="button"
                    wire:click="toggleOpen"
                    x-on:click="isOpen = !isOpen"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                    tabindex="-1"
                    data-testid="lure-toggle-dropdown">
                <x-lucide-chevron-down class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': isOpen }" />
            </button>
        </div>
    @endif

    {{-- Dropdown Tray Overlay --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         x-cloak
         class="absolute z-50 left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden max-h-96 flex flex-col"
         data-testid="lure-dropdown-tray">

        {{-- Category Pills Header --}}
        @if(count($categories) > 0)
            <div class="p-2.5 bg-slate-50 border-b border-slate-200/80 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                <button type="button"
                        wire:click="setCategory('all')"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors shrink-0 cursor-pointer {{ $selectedCategory === 'all' ? 'bg-teal-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-200/70 border border-slate-200' }}"
                        data-testid="lure-cat-pill-all">
                    All Categories
                </button>
                @foreach($categories as $category)
                    <button type="button"
                            wire:click="setCategory('{{ $category }}')"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors shrink-0 cursor-pointer {{ $selectedCategory === $category ? 'bg-teal-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-200/70 border border-slate-200' }}"
                            data-testid="lure-cat-pill-{{ \Illuminate\Support\Str::slug($category) }}">
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Search Input Inside Dropdown if Lure Was Already Selected --}}
        @if($selectedLure)
            <div class="p-3 border-b border-slate-100 bg-white">
                <div class="relative">
                    <x-lucide-search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                    <input type="text"
                           wire:model.live.debounce.250ms="search"
                           placeholder="Type to filter other lures..."
                           class="w-full h-9 pl-9 pr-8 rounded-lg border border-slate-200 bg-slate-50 text-slate-800 text-xs focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
                           data-testid="lure-dropdown-search-input" />
                    @if(trim($search) !== '')
                        <button type="button"
                                wire:click="$set('search', '')"
                                class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <x-lucide-x class="w-3.5 h-3.5" />
                        </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Grouped Lures Scrollable Area --}}
        <div class="overflow-y-auto divide-y divide-slate-100 flex-1 overscroll-contain">
            @forelse($groupedLures as $categoryName => $categoryItems)
                <div class="p-2 space-y-1">
                    {{-- 2-Tier Category Header --}}
                    <div class="px-2.5 py-1 bg-slate-100/70 text-[10px] font-black uppercase tracking-wider text-slate-500 rounded-md flex items-center justify-between">
                        <span>{{ $categoryName }}</span>
                        <span class="text-slate-400 font-mono">{{ count($categoryItems) }}</span>
                    </div>

                    {{-- Lure Items Grid/List --}}
                    @foreach($categoryItems as $lure)
                        <div wire:click="selectLure('{{ $lure->id }}')"
                             x-on:click="isOpen = false"
                             wire:key="lure-option-{{ $lure->id }}"
                             class="p-2.5 rounded-xl hover:bg-teal-50/70 active:bg-teal-100/80 cursor-pointer transition-colors flex items-center justify-between gap-3 {{ $selectedId === $lure->id ? 'bg-teal-50/90 ring-1 ring-teal-500/30' : '' }}"
                             data-testid="lure-item-{{ $lure->id }}">
                            <div class="min-w-0 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 {{ $selectedId === $lure->id ? 'bg-teal-600 text-white' : '' }}">
                                    <x-lucide-fishing-hook class="w-4 h-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if($lure->brand)
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded border border-slate-200/60">
                                                {{ $lure->brand }}
                                            </span>
                                        @endif
                                        <span class="text-xs font-bold text-slate-900 truncate">
                                            {{ $lure->name }}
                                        </span>
                                        @if($lure->color)
                                            <span class="text-xs text-slate-600 font-medium">
                                                • {{ $lure->color }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-400 font-medium">
                                        @if($lure->size || $lure->weight)
                                            <span class="font-mono text-slate-500">
                                                {{ implode(' • ', array_filter([$lure->size, $lure->weight])) }}
                                            </span>
                                        @endif
                                        @if($lure->depth_range)
                                            <span class="text-sky-600 font-medium">
                                                {{ $lure->depth_range }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0 flex items-center gap-2">
                                @if($lure->records_count > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-teal-50 text-teal-700 border border-teal-200/60 text-[10px] font-bold" title="{{ $lure->records_count }} verified catches in logbook">
                                        <x-lucide-fish class="w-3 h-3 text-teal-600" />
                                        {{ $lure->records_count }}
                                    </span>
                                @endif

                                @if($selectedId === $lure->id)
                                    <x-lucide-check class="w-4 h-4 text-teal-600 shrink-0" />
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="p-8 text-center space-y-3" data-testid="lure-no-results">
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <x-lucide-package-open class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-700">No lures found</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            @if(trim($search) !== '')
                                No tackle matching "<span class="font-semibold text-slate-600">{{ $search }}</span>"
                            @else
                                No tackle registered in this category
                            @endif
                        </p>
                    </div>
                    @if(trim($search) !== '' || $selectedCategory !== 'all')
                        <button type="button"
                                wire:click="$set('search', ''); $set('selectedCategory', 'all')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                            <x-lucide-rotate-ccw class="w-3 h-3" />
                            Reset Filters
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Footer Controls --}}
        <div class="p-2.5 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-500">
            <span class="text-[11px] font-medium text-slate-400">
                {{ $totalResults }} {{ \Illuminate\Support\Str::plural('lure', $totalResults) }} available
            </span>
            <button type="button"
                    wire:click="closeDropdown"
                    x-on:click="isOpen = false"
                    class="px-2.5 py-1 text-slate-600 hover:text-slate-900 font-bold hover:underline cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>
