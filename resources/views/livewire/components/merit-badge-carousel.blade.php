@php
    $dimClass = match($size) {
        'sm' => 'w-10 h-10',
        'lg' => 'w-16 h-16 md:w-20 md:h-20 lg:w-[86px] lg:h-[86px]',
        default => 'w-12 h-12 md:w-14 md:h-14',
    };
    $marginClass = match($size) {
        'sm' => '-ml-3',
        'lg' => '-ml-5 md:-ml-7 lg:-ml-8',
        default => '-ml-4 md:-ml-5',
    };
    $iconDimClass = match($size) {
        'sm' => 'w-5 h-5',
        'lg' => 'w-9 h-9 md:w-11 md:h-11',
        default => 'w-6 h-6',
    };
    $shiftPx = match($size) {
        'sm' => 22,
        'lg' => 48,
        default => 30,
    };
    $liftPx = match($size) {
        'sm' => 6,
        'lg' => 12,
        default => 8,
    };
    $scaleVal = match($size) {
        'sm' => 1.22,
        'lg' => 1.25,
        default => 1.28,
    };
    $totalBadges = count($badges);
@endphp

<script>
if (!window.meritBadgeCarousel) {
    window.meritBadgeCarousel = function(badges, total, shift, scale, lift) {
        return {
            badges: Array.isArray(badges) ? badges : [],
            total: total,
            shift: shift,
            scale: scale,
            lift: lift,
            hoveredIndex: null,
            focusedIndex: null,
            autoScrollRaf: null,
            scrollSpeed: 0,
            isDragging: false,
            dragStartX: 0,
            dragScrollLeft: 0,

            get activeIndex() {
                return this.hoveredIndex !== null ? this.hoveredIndex : this.focusedIndex;
            },

            get activeBadge() {
                const idx = this.activeIndex;
                if (idx !== null && this.badges && this.badges[idx]) {
                    return this.badges[idx];
                }
                return (this.badges && this.badges[0]) ? this.badges[0] : null;
            },

            setHover(idx) {
                this.hoveredIndex = idx;
            },

            clearHover() {
                this.hoveredIndex = null;
                this.focusedIndex = null;
                this.stopAutoScroll();
            },

            handleWheel(e) {
                if (this.$refs.scrollTrack) {
                    this.$refs.scrollTrack.scrollLeft += e.deltaY * 0.9;
                }
            },

            handleMouseMove(e) {
                const track = this.$refs.scrollTrack;
                if (!track || this.isDragging) return;
                const rect = track.getBoundingClientRect();
                const mouseX = e.clientX - rect.left;
                const width = rect.width;
                const edgeZone = Math.min(130, width * 0.22);

                if (mouseX >= 0 && mouseX < edgeZone) {
                    const intensity = (edgeZone - mouseX) / edgeZone;
                    this.startAutoScroll(-intensity * 16);
                } else if (mouseX <= width && mouseX > width - edgeZone) {
                    const intensity = (mouseX - (width - edgeZone)) / edgeZone;
                    this.startAutoScroll(intensity * 16);
                } else {
                    this.stopAutoScroll();
                }
            },

            startAutoScroll(speed) {
                this.scrollSpeed = speed;
                if (!this.autoScrollRaf) {
                    const step = () => {
                        if (this.$refs.scrollTrack && this.scrollSpeed !== 0) {
                            this.$refs.scrollTrack.scrollLeft += this.scrollSpeed;
                            this.autoScrollRaf = requestAnimationFrame(step);
                        } else {
                            this.autoScrollRaf = null;
                        }
                    };
                    this.autoScrollRaf = requestAnimationFrame(step);
                }
            },

            stopAutoScroll() {
                this.scrollSpeed = 0;
                if (this.autoScrollRaf) {
                    cancelAnimationFrame(this.autoScrollRaf);
                    this.autoScrollRaf = null;
                }
            },

            startDrag(e) {
                this.isDragging = true;
                this.stopAutoScroll();
                this.dragStartX = e.pageX - (this.$refs.scrollTrack?.offsetLeft || 0);
                this.dragScrollLeft = this.$refs.scrollTrack?.scrollLeft || 0;
            },

            doDrag(e) {
                if (!this.isDragging || !this.$refs.scrollTrack) return;
                e.preventDefault();
                const x = e.pageX - this.$refs.scrollTrack.offsetLeft;
                const walk = (x - this.dragStartX) * 1.5;
                this.$refs.scrollTrack.scrollLeft = this.dragScrollLeft - walk;
            },

            stopDrag() {
                this.isDragging = false;
            },

            focusPrevious() {
                if (this.total === 0) return;
                if (this.focusedIndex === null) {
                    this.focusedIndex = this.total - 1;
                } else {
                    this.focusedIndex = (this.focusedIndex - 1 + this.total) % this.total;
                }
                this.scrollToActive();
            },

            focusNext() {
                if (this.total === 0) return;
                if (this.focusedIndex === null) {
                    this.focusedIndex = 0;
                } else {
                    this.focusedIndex = (this.focusedIndex + 1) % this.total;
                }
                this.scrollToActive();
            },

            scrollToActive() {
                if (this.focusedIndex === null || !this.$refs.scrollTrack) return;
                const item = this.$refs.scrollTrack.querySelector(`[data-index="${this.focusedIndex}"]`);
                if (item) {
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
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
                return {
                    transform: `translate(${this.shift}px, 0px) scale(1)`,
                    zIndex: 10 + (this.total - idx)
                };
            }
        };
    };
}
</script>

<div 
    wire:ignore.self 
    class="isolate w-full relative overflow-visible {{ ($boxed ?? true) ? 'bg-slate-900/90 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-slate-800 shadow-md' : 'p-0' }}"
    x-data="meritBadgeCarousel({{ json_encode($badges, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}, {{ $totalBadges }}, {{ $shiftPx }}, {{ $scaleVal }}, {{ $liftPx }})"
    @mouseleave="clearHover()"
    @keydown.escape.window="clearHover()"
    @keydown.arrow-left.prevent="focusPrevious()"
    @keydown.arrow-right.prevent="focusNext()"
>
    @if($showHeader ?? true)
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
    @endif

    @if($totalBadges === 0)
        @if($boxed ?? true)
            <!-- Empty State Ribbon -->
            <div class="py-6 px-4 rounded-xl border border-dashed border-slate-800 bg-slate-950/40 text-center flex flex-col items-center justify-center space-y-1.5">
                <x-lucide-shield-alert class="w-7 h-7 text-slate-600 mb-1" />
                <p class="text-xs font-semibold text-slate-400">No merit badges earned yet</p>
                <p class="text-[11px] text-slate-500 max-w-sm">
                    Log catches to unlock career scouting patches, lunker milestones, and conservation trophies.
                </p>
            </div>
        @endif
    @else
        <style>
            .merit-scroll-track::-webkit-scrollbar {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }
            .merit-scroll-track {
                -ms-overflow-style: none !important;
                scrollbar-width: none !important;
            }
        </style>

        <!-- Overlapping Merit Badge Track with Continuous Wheel/Edge Scrolling -->
        <div 
            x-ref="scrollTrack"
            class="merit-scroll-track relative py-4 px-2 overflow-x-auto overflow-y-visible no-scrollbar cursor-grab active:cursor-grabbing select-none scroll-smooth"
            style="scrollbar-width: none; -ms-overflow-style: none;"
            @wheel.prevent="handleWheel($event)"
            @mousemove="handleMouseMove($event)"
            @mouseleave="stopAutoScroll()"
            @mousedown="startDrag($event)"
            @mousemove.window="if (isDragging) doDrag($event)"
            @mouseup.window="if (isDragging) stopDrag()"
        >
            <div 
                class="flex items-center min-w-max"
                role="region" 
                aria-label="Merit Badge Carousel"
            >
                @foreach($badges as $idx => $badge)
                    <!-- Hit-Box Anchor Slot (Fixed Screen Slot - Prevents Hysteresis Loop) -->
                    <div 
                        wire:key="badge-carousel-{{ $badge['id'] }}"
                        data-index="{{ $idx }}"
                        class="relative flex-shrink-0 {{ $dimClass }} {{ $idx > 0 ? $marginClass : '' }} focus:outline-none"
                        tabindex="0"
                        role="button"
                        aria-label="{{ $badge['name'] }} Merit Badge - {{ ucfirst($badge['tier']) }} Tier"
                        title="{{ $badge['name'] }} (+{{ $badge['points'] }} pts) - {{ ucfirst($badge['tier']) }}"
                        @mouseenter="setHover({{ $idx }})"
                        @mouseleave="if (hoveredIndex === {{ $idx }}) hoveredIndex = null"
                        @focus="focusedIndex = {{ $idx }}"
                        @blur="if (focusedIndex === {{ $idx }}) focusedIndex = null"
                    >
                        <!-- Inner Visual Presenter (Transforms Dynamically on GPU) -->
                        <div 
                            class="w-full h-full rounded-full transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu will-change-transform flex items-center justify-center pointer-events-none relative"
                            :style="getItemStyle({{ $idx }})"
                            :class="activeIndex === {{ $idx }} ? 'drop-shadow-2xl brightness-110 filter' : 'drop-shadow-md'"
                        >
                            @if(!empty($badge['image_url']))
                                <img 
                                    src="{{ $badge['image_url'] }}" 
                                    alt="{{ $badge['name'] }}" 
                                    class="w-full h-full rounded-full object-cover"
                                    loading="lazy"
                                />
                            @else
                                <!-- Merrowed Edge Twill Fallback Patch -->
                                <div class="w-full h-full rounded-full flex items-center justify-center border-2 border-dashed border-slate-700 bg-gradient-to-br from-slate-800 to-slate-950">
                                    <x-dynamic-component :component="'lucide-' . ($badge['icon'] ?: 'award')" class="{{ $iconDimClass }} text-amber-300 drop-shadow" />
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Optional Overflow Capsule (Only if maxVisible is actively limited) -->
                @if($overflowCount > 0)
                    <div 
                        class="flex-shrink-0 {{ $dimClass }} {{ $marginClass }} flex items-center justify-center"
                        style="z-index: 25;"
                    >
                        <button 
                            type="button" 
                            @click="$dispatch('toggle-merit-sash')"
                            class="w-full h-full rounded-full bg-slate-950/80 hover:bg-slate-900 border-2 border-dashed border-teal-500/60 hover:border-teal-400 text-teal-300 font-mono text-xs md:text-sm font-bold flex flex-col items-center justify-center transition-all duration-200 shadow-lg cursor-pointer"
                            title="View all {{ $totalCount }} badges in sash"
                        >
                            <span class="leading-none text-teal-300 font-extrabold">+{{ $overflowCount }}</span>
                            <span class="text-[9px] md:text-[10px] uppercase tracking-wider text-slate-400 font-semibold mt-0.5">More</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @if($showInspector && $totalBadges > 0)
            <!-- Dynamic Merit Badge Telemetry Inspector Strip -->
            <div 
                class="mt-2.5 bg-slate-950/70 border rounded-xl p-3.5 backdrop-blur-md transition-all duration-200"
                :class="hoveredIndex !== null ? 'border-teal-500/50 bg-slate-950/90 shadow-lg shadow-teal-950/30 ring-1 ring-teal-500/30' : 'border-slate-800/80'"
            >
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <!-- Left: Badge Identity, Tier & Criteria Description -->
                    <div class="space-y-1 min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-black text-white tracking-tight truncate" x-text="activeBadge?.name">{{ $badges[0]['name'] ?? '' }}</span>

                            <!-- Tier Pill -->
                            <span 
                                class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded border"
                                :class="activeBadge?.tier_badge"
                                x-text="activeBadge?.tier"
                            >{{ $badges[0]['tier'] ?? '' }}</span>

                            <!-- Points Pill -->
                            <span 
                                class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded border border-teal-700/50 bg-teal-950/80 text-teal-300"
                                x-text="'+' + (activeBadge?.points || 0) + ' pts'"
                            >+{{ $badges[0]['points'] ?? 0 }} pts</span>

                            <!-- Category -->
                            <span 
                                class="text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-800 text-slate-400"
                                x-text="activeBadge?.category"
                            >{{ $badges[0]['category'] ?? '' }}</span>

                            <!-- Idle State Indicator -->
                            <span 
                                x-show="activeIndex === null"
                                class="text-[9px] font-mono font-medium text-amber-400/80 bg-amber-950/40 px-1.5 py-0.5 rounded border border-amber-800/30"
                            >
                                Latest Milestone
                            </span>
                        </div>

                        <!-- Short Description -->
                        <p class="text-[11.5px] text-slate-300 leading-snug line-clamp-2" x-text="activeBadge?.description">{{ $badges[0]['description'] ?? '' }}</p>
                    </div>

                    <!-- Right: Telemetry (Awarded Date, Trigger Context & Catch Record Link) -->
                    <div class="flex sm:flex-col items-start sm:items-end justify-between sm:justify-center gap-1 shrink-0 text-[10.5px] text-slate-400 pt-1.5 sm:pt-0 border-t sm:border-t-0 border-slate-800/60">
                        <div x-show="activeBadge?.awarded_at" class="flex items-center gap-1 font-mono text-slate-400">
                            <x-lucide-calendar class="w-3 h-3 text-slate-500" />
                            <span x-text="'Awarded ' + (activeBadge?.awarded_at || '')">@if(!empty($badges[0]['awarded_at'])) Awarded {{ $badges[0]['awarded_at'] }} @endif</span>
                        </div>
                        <div x-show="activeBadge?.trigger_summary" class="text-[10px] text-teal-300/90 max-w-xs text-left sm:text-right truncate" :title="activeBadge?.trigger_summary">
                            <span class="text-slate-500">Trigger:</span>
                            <span x-text="activeBadge?.trigger_summary">{{ $badges[0]['trigger_summary'] ?? '' }}</span>
                        </div>
                        <div x-show="activeBadge?.record_id">
                            <a 
                                :href="'/record/' + (activeBadge?.record_id || '')" 
                                class="inline-flex items-center gap-1 text-teal-400 hover:text-teal-300 font-semibold text-[10.5px] transition-colors"
                            >
                                <span>View Catch Record</span>
                                <x-lucide-arrow-right class="w-3 h-3" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
