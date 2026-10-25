@php
    $dimClass = match($size) {
        'sm' => 'w-10 h-10',
        'lg' => 'w-16 h-16 md:w-18 md:h-18',
        default => 'w-12 h-12 md:w-14 md:h-14',
    };
    $marginClass = match($size) {
        'sm' => '-ml-3',
        'lg' => '-ml-6 md:-ml-7',
        default => '-ml-4 md:-ml-5',
    };
    $iconDimClass = match($size) {
        'sm' => 'w-5 h-5',
        'lg' => 'w-8 h-8',
        default => 'w-6 h-6',
    };
    $pipDimClass = match($size) {
        'sm' => 'w-2.5 h-2.5',
        'lg' => 'w-3.5 h-3.5',
        default => 'w-3 h-3',
    };
    $shiftPx = match($size) {
        'sm' => 22,
        'lg' => 38,
        default => 30,
    };
    $liftPx = match($size) {
        'sm' => 6,
        'lg' => 10,
        default => 8,
    };
    $scaleVal = match($size) {
        'sm' => 1.22,
        'lg' => 1.3,
        default => 1.28,
    };
    $totalBadges = count($badges);
@endphp

<div 
    wire:ignore.self 
    class="isolate w-full bg-slate-900/90 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-slate-800 shadow-md relative overflow-visible"
    x-data="{
        total: {{ $totalBadges }},
        shift: {{ $shiftPx }},
        scale: {{ $scaleVal }},
        lift: {{ $liftPx }},
        hoveredIndex: null,
        focusedIndex: null,

        get activeIndex() {
            return this.hoveredIndex !== null ? this.hoveredIndex : this.focusedIndex;
        },

        setHover(idx) {
            this.hoveredIndex = idx;
        },

        clearHover() {
            this.hoveredIndex = null;
            this.focusedIndex = null;
        },

        focusPrevious() {
            if (this.total === 0) return;
            if (this.focusedIndex === null) {
                this.focusedIndex = this.total - 1;
            } else {
                this.focusedIndex = (this.focusedIndex - 1 + this.total) % this.total;
            }
        },

        focusNext() {
            if (this.total === 0) return;
            if (this.focusedIndex === null) {
                this.focusedIndex = 0;
            } else {
                this.focusedIndex = (this.focusedIndex + 1) % this.total;
            }
        },

        getItemStyle(idx) {
            const active = this.activeIndex;
            if (active === null) {
                return {
                    transform: 'translate(0px, 0px) scale(1)',
                    zIndex: 10 + idx
                };
            }
            if (idx < active) {
                return {
                    transform: `translate(-${this.shift}px, 0px) scale(1)`,
                    zIndex: 10 + idx
                };
            }
            if (idx === active) {
                return {
                    transform: `translate(0px, -${this.lift}px) scale(${this.scale})`,
                    zIndex: 50
                };
            }
            // idx > active
            return {
                transform: `translate(${this.shift}px, 0px) scale(1)`,
                zIndex: 10 + (this.total - idx)
            };
        }
    }"
    @mouseleave="clearHover()"
    @keydown.escape.window="clearHover()"
    @keydown.arrow-left.prevent="focusPrevious()"
    @keydown.arrow-right.prevent="focusNext()"
>
    <!-- Header: Title, Total Points, Count & Sash Toggle Link -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80 mb-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                <x-lucide-award class="w-4 h-4" />
            </div>
            <div>
                <h3 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                    <span>{{ $title ?? 'Merit Badge Carousel' }}</span>
                    @if($showCount && $totalCount > 0)
                        <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded-full bg-teal-500/15 border border-teal-500/30 text-teal-300">
                            {{ $totalCount }} {{ \Illuminate\Support\Str::plural('Earned', $totalCount) }}
                        </span>
                    @endif
                </h3>
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs">
            @if($showPoints && $totalPoints > 0)
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-300 text-[11px]">
                    <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px]">Points:</span>
                    <span class="font-mono font-bold text-amber-300">{{ number_format($totalPoints) }}</span>
                </div>
            @endif

            @if($showSashLink)
                <button 
                    type="button"
                    @click="$dispatch('toggle-merit-sash')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold transition-colors cursor-pointer"
                    title="Toggle Full Merit Badge Sash"
                >
                    <x-lucide-layout-grid class="w-3.5 h-3.5 text-teal-400" />
                    <span>View All</span>
                </button>
            @endif
        </div>
    </div>

    @if($totalBadges === 0)
        <!-- Empty State Ribbon -->
        <div class="py-6 px-4 rounded-xl border border-dashed border-slate-800 bg-slate-950/40 text-center flex flex-col items-center justify-center space-y-1.5">
            <x-lucide-shield-alert class="w-7 h-7 text-slate-600 mb-1" />
            <p class="text-xs font-semibold text-slate-400">No merit badges earned yet</p>
            <p class="text-[11px] text-slate-500 max-w-sm">
                Log expedition catches to unlock career scouting patches, lunker milestones, and conservation trophies.
            </p>
        </div>
    @else
        <!-- Overlapping Merit Badge Track -->
        <div class="relative py-4 px-2 overflow-x-auto overflow-y-visible no-scrollbar">
            <div 
                class="flex items-center"
                role="region" 
                aria-label="Merit Badge Carousel"
            >
                @foreach($badges as $idx => $badge)
                    <!-- Hit-Box Anchor Slot (Fixed Screen Slot - Prevents Hysteresis Loop) -->
                    <div 
                        wire:key="badge-carousel-{{ $badge['id'] }}"
                        class="relative flex-shrink-0 {{ $dimClass }} {{ $idx > 0 ? $marginClass : '' }} focus:outline-none"
                        tabindex="0"
                        role="button"
                        aria-label="{{ $badge['name'] }} Merit Badge - {{ ucfirst($badge['tier']) }} Tier"
                        @mouseenter="setHover({{ $idx }})"
                        @mouseleave="if (hoveredIndex === {{ $idx }}) hoveredIndex = null"
                        @focus="focusedIndex = {{ $idx }}"
                        @blur="if (focusedIndex === {{ $idx }}) focusedIndex = null"
                    >
                        <!-- Inner Visual Presenter (Transforms Dynamically on GPU) -->
                        <div 
                            class="w-full h-full rounded-full transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu will-change-transform flex items-center justify-center pointer-events-none relative"
                            :style="getItemStyle({{ $idx }})"
                            :class="activeIndex === {{ $idx }} ? 'shadow-xl {{ $badge['tier_glow'] }}' : 'shadow-md'"
                        >
                            @if(!empty($badge['image_url']))
                                <img 
                                    src="{{ $badge['image_url'] }}" 
                                    alt="{{ $badge['name'] }}" 
                                    class="w-full h-full rounded-full object-cover border-2 {{ $badge['tier_border'] }} ring-2 ring-black/60"
                                    loading="lazy"
                                />
                            @else
                                <!-- Merrowed Edge Twill Fallback Patch -->
                                <div class="w-full h-full rounded-full flex items-center justify-center border-2 border-dashed {{ $badge['tier_border'] }} bg-gradient-to-br from-slate-800 to-slate-950 ring-2 ring-black/60">
                                    <x-dynamic-component :component="'lucide-' . ($badge['icon'] ?: 'award')" class="{{ $iconDimClass }} text-amber-300 drop-shadow" />
                                </div>
                            @endif

                            <!-- Micro Tier Indicator Pip -->
                            <span 
                                class="absolute -bottom-0.5 -right-0.5 {{ $pipDimClass }} rounded-full border border-black/80 {{ $badge['tier_pip'] }}" 
                                title="{{ ucfirst($badge['tier']) }} Tier"
                            ></span>
                        </div>

                        <!-- Interactive Floating Popover Card on Active Hover/Focus -->
                        <div 
                            x-show="activeIndex === {{ $idx }}" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            class="absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-3.5 w-72 bg-slate-950/95 backdrop-blur-md text-white rounded-xl p-3.5 shadow-2xl border border-slate-700/80 pointer-events-auto text-left"
                        >
                            <!-- Header: Title & Points Pill -->
                            <div class="flex items-start justify-between gap-2 pb-2 border-b border-slate-800">
                                <div class="min-w-0">
                                    <h4 class="text-xs font-extrabold text-white tracking-tight flex items-center gap-1.5 truncate">
                                        <span>{{ $badge['name'] }}</span>
                                    </h4>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border {{ $badge['tier_badge'] }}">
                                            {{ $badge['tier'] }}
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-teal-950 text-teal-300 border border-teal-700/60">
                                            +{{ $badge['points'] }} pts
                                        </span>
                                    </div>
                                </div>

                                @if(!empty($badge['image_url']))
                                    <img src="{{ $badge['image_url'] }}" alt="" class="w-7 h-7 rounded-full border border-slate-700 shrink-0 object-cover" />
                                @endif
                            </div>

                            <!-- Description -->
                            <p class="text-[11px] text-slate-300 mt-2 leading-relaxed">
                                {{ $badge['description'] }}
                            </p>

                            <!-- Award Context & Record Link -->
                            <div class="mt-2.5 pt-2 border-t border-slate-800/80 space-y-1 text-[10.5px]">
                                @if(!empty($badge['awarded_at']))
                                    <div class="flex items-center gap-1 text-slate-400">
                                        <x-lucide-calendar class="w-3 h-3 text-slate-500 shrink-0" />
                                        <span>Awarded {{ $badge['awarded_at'] }}</span>
                                    </div>
                                @endif

                                @if(!empty($badge['angler_name']))
                                    <div class="flex items-center gap-1 text-slate-400">
                                        <x-lucide-user class="w-3 h-3 text-teal-400 shrink-0" />
                                        <span>Angler: <strong class="text-slate-200">{{ $badge['angler_name'] }}</strong></span>
                                    </div>
                                @endif

                                @if(!empty($badge['trigger_summary']))
                                    <div class="text-slate-300 bg-slate-900/90 rounded-md p-1.5 border border-slate-800 text-[10px]">
                                        <span class="text-teal-400 font-semibold block text-[9.5px] uppercase tracking-wide">Trigger:</span>
                                        <span>{{ $badge['trigger_summary'] }}</span>
                                    </div>
                                @endif

                                @if(!empty($badge['record_id']))
                                    <div class="pt-1">
                                        <a href="{{ url('/record/' . $badge['record_id']) }}" class="inline-flex items-center gap-1 text-teal-400 hover:text-teal-300 font-semibold text-[10.5px] transition-colors">
                                            <span>View Catch Record</span>
                                            <x-lucide-arrow-right class="w-3 h-3" />
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Caret / Arrow Pointing Down -->
                            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-950 border-r border-b border-slate-700/80 transform rotate-45"></div>
                        </div>
                    </div>
                @endforeach

                <!-- Overflow Capsule (When badges exceed maxVisible) -->
                @if($overflowCount > 0)
                    <div 
                        class="flex-shrink-0 {{ $dimClass }} {{ $marginClass }} flex items-center justify-center"
                        style="z-index: 25;"
                    >
                        <button 
                            type="button" 
                            @click="$dispatch('toggle-merit-sash')"
                            class="w-full h-full rounded-full bg-slate-800/90 hover:bg-slate-700 border-2 border-dashed border-teal-500/50 hover:border-teal-400 text-teal-300 font-mono text-[11px] font-bold flex flex-col items-center justify-center transition-all duration-200 shadow-md ring-2 ring-black/50 cursor-pointer"
                            title="View all {{ $totalCount }} badges in sash"
                        >
                            <span>+{{ $overflowCount }}</span>
                            <span class="text-[8px] uppercase tracking-tighter text-slate-400">More</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
