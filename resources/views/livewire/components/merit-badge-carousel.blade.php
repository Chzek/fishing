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
        dragDistance: 0,
        modalOpen: false,
        selectedModalIndex: null,

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

        get selectedModalBadge() {
            if (this.selectedModalIndex !== null && this.badges && this.badges[this.selectedModalIndex]) {
                return this.badges[this.selectedModalIndex];
            }
            return null;
        },

        openModal(idx) {
            if (this.dragDistance > 6) return;
            if (idx < 0 || idx >= this.total) return;
            this.selectedModalIndex = idx;
            this.modalOpen = true;
            this.stopAutoScroll();
        },

        closeModal() {
            this.modalOpen = false;
            this.selectedModalIndex = null;
        },

        nextModalBadge() {
            if (this.total === 0) return;
            this.selectedModalIndex = (this.selectedModalIndex + 1) % this.total;
        },

        prevModalBadge() {
            if (this.total === 0) return;
            this.selectedModalIndex = (this.selectedModalIndex - 1 + this.total) % this.total;
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
            this.dragDistance = 0;
            this.stopAutoScroll();
            this.dragStartX = e.pageX - (this.$refs.scrollTrack?.offsetLeft || 0);
            this.dragScrollLeft = this.$refs.scrollTrack?.scrollLeft || 0;
        },

        doDrag(e) {
            if (!this.isDragging || !this.$refs.scrollTrack) return;
            e.preventDefault();
            const x = e.pageX - this.$refs.scrollTrack.offsetLeft;
            const walk = (x - this.dragStartX) * 1.5;
            this.dragDistance = Math.abs(walk);
            this.$refs.scrollTrack.scrollLeft = this.dragScrollLeft - walk;
        },

        stopDrag() {
            setTimeout(() => {
                this.isDragging = false;
                this.dragDistance = 0;
            }, 60);
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
</script>

<div 
    wire:ignore.self 
    class="isolate w-full relative overflow-visible {{ ($boxed ?? true) ? 'bg-slate-900/90 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-slate-800 shadow-md' : 'p-0' }}"
    x-data="meritBadgeCarousel({{ json_encode($badges, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}, {{ $totalBadges }}, {{ $shiftPx }}, {{ $scaleVal }}, {{ $liftPx }})"
    @mouseleave="clearHover()"
    @keydown.escape.window="if (!modalOpen) clearHover()"
    @keydown.arrow-left.prevent="if (!modalOpen) focusPrevious()"
    @keydown.arrow-right.prevent="if (!modalOpen) focusNext()"
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
            class="merit-scroll-track relative pt-2.5 pb-0.5 px-2 overflow-x-auto overflow-y-visible no-scrollbar cursor-grab active:cursor-grabbing select-none scroll-smooth"
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
                        class="relative flex-shrink-0 {{ $dimClass }} {{ $idx > 0 ? $marginClass : '' }} focus:outline-none cursor-pointer"
                        tabindex="0"
                        role="button"
                        aria-label="{{ $badge['name'] }} Merit Badge - {{ ucfirst($badge['tier']) }} Tier"
                        title="{{ $badge['name'] }} (+{{ $badge['points'] }} pts) - Click for full details"
                        @click="openModal({{ $idx }})"
                        @keydown.enter.prevent="openModal({{ $idx }})"
                        @keydown.space.prevent="openModal({{ $idx }})"
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
            <!-- Sleek Single-Line Merit Badge Telemetry Inspector Strip -->
            <div 
                class="mt-1 bg-slate-950/70 border rounded-lg py-1.5 px-3 backdrop-blur-md transition-all duration-200 cursor-pointer select-none group/inspector"
                :class="hoveredIndex !== null ? 'border-teal-500/50 bg-slate-950/90 shadow-md shadow-teal-950/30 ring-1 ring-teal-500/30' : 'border-slate-800/80 hover:border-slate-700'"
                @click="openModal(activeIndex !== null ? activeIndex : 0)"
                title="Click to view full merit badge details & catch telemetry"
            >
                <div class="flex items-center justify-between gap-3 text-xs">
                    <!-- Left: Title & Points Pill -->
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="font-bold text-white group-hover/inspector:text-teal-300 transition-colors tracking-tight truncate text-xs sm:text-[13px]" x-text="activeBadge?.name">{{ $badges[0]['name'] ?? '' }}</span>

                        <!-- Points Pill -->
                        <span 
                            class="text-[9.5px] font-mono font-bold px-1.5 py-0.5 rounded border border-teal-700/50 bg-teal-950/80 text-teal-300 shrink-0"
                            x-text="'+' + (activeBadge?.points || 0) + ' pts'"
                        >+{{ $badges[0]['points'] ?? 0 }} pts</span>
                    </div>

                    <!-- Right: Awarded Date & Details Affordance -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <div x-show="activeBadge?.awarded_at" class="flex items-center gap-1.5 font-mono text-[11px] text-slate-400">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-slate-500" />
                            <span x-text="'Awarded ' + (activeBadge?.awarded_at || '')">@if(!empty($badges[0]['awarded_at'])) Awarded {{ $badges[0]['awarded_at'] }} @endif</span>
                        </div>
                        <span class="hidden sm:inline-flex items-center gap-1 text-[10.5px] text-teal-400/90 group-hover/inspector:text-teal-300 font-semibold transition-colors">
                            <span>Details</span>
                            <x-lucide-info class="w-3 h-3" />
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Full Merit Badge & Catch Record Modal Window -->
        <template x-teleport="body">
            <div 
                x-show="modalOpen" 
                x-cloak
                @keydown.escape.window="if (modalOpen) closeModal()"
                @keydown.arrow-left.window="if (modalOpen) prevModalBadge()"
                @keydown.arrow-right.window="if (modalOpen) nextModalBadge()"
                class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
                role="dialog"
                aria-modal="true"
                :aria-label="(selectedModalBadge?.name || 'Merit Badge') + ' Details'"
            >
                <!-- Modal Backdrop Overlay -->
                <div 
                    x-show="modalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"
                    @click="closeModal()"
                    aria-hidden="true"
                ></div>

                <!-- Modal Window Card -->
                <div 
                    x-show="modalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative w-full max-w-lg bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border border-slate-700/80 rounded-3xl p-5 sm:p-6 shadow-2xl text-white overflow-hidden my-6 z-10 max-h-[92vh] flex flex-col"
                    @click.stop
                >
                    <!-- Tier Glow Ambient Blur in Background -->
                    <div 
                        class="absolute -top-16 left-1/2 -translate-x-1/2 w-64 h-64 rounded-full blur-3xl opacity-20 pointer-events-none transition-all duration-300"
                        :class="{
                            'bg-cyan-400': selectedModalBadge?.tier === 'platinum',
                            'bg-amber-400': selectedModalBadge?.tier === 'gold',
                            'bg-slate-300': selectedModalBadge?.tier === 'silver',
                            'bg-amber-700': selectedModalBadge?.tier === 'bronze' || !selectedModalBadge?.tier
                        }"
                    ></div>

                    <!-- Top Bar: Category Chip, Index Counter & Close Button -->
                    <div class="flex items-center justify-between gap-3 relative z-10 pb-3 border-b border-slate-800/80 shrink-0">
                        <div class="flex items-center gap-2">
                            <span 
                                class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700/60"
                                x-text="selectedModalBadge?.category || 'Milestone'"
                            ></span>
                            <span class="text-[11px] font-mono font-medium text-slate-400" x-text="'#' + ((selectedModalIndex || 0) + 1) + ' of ' + total"></span>
                        </div>

                        <button 
                            type="button" 
                            @click="closeModal()"
                            class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer border border-slate-700/60 focus:outline-none focus:ring-2 focus:ring-teal-400"
                            title="Close modal (Esc)"
                        >
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Modal Scrollable Content Area -->
                    <div class="overflow-y-auto pr-1 -mr-1 space-y-4 pt-4 relative z-10 no-scrollbar">
                        <!-- Hero Presenter: Full Size Badge Patch -->
                        <div class="flex flex-col items-center text-center">
                            <div class="relative group">
                                <!-- Visual Badge Image or Fallback Patch -->
                                <template x-if="selectedModalBadge?.image_url">
                                    <img 
                                        :src="selectedModalBadge.image_url" 
                                        :alt="selectedModalBadge.name" 
                                        class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover shadow-2xl border-2 ring-4 ring-black/40 transition-transform duration-300 group-hover:scale-105"
                                        :class="selectedModalBadge?.tier_border || 'border-slate-700'"
                                    />
                                </template>
                                <template x-if="!selectedModalBadge?.image_url">
                                    <div 
                                        class="w-24 h-24 sm:w-28 sm:h-28 rounded-full flex items-center justify-center border-2 border-dashed bg-gradient-to-br from-slate-800 to-slate-950 shadow-2xl ring-4 ring-black/40"
                                        :class="selectedModalBadge?.tier_border || 'border-slate-700'"
                                    >
                                        <x-lucide-award class="w-12 h-12 text-amber-300 drop-shadow" />
                                    </div>
                                </template>
                            </div>

                            <!-- Badge Name -->
                            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight mt-3.5" x-text="selectedModalBadge?.name"></h3>

                            <!-- Badges/Chips Row -->
                            <div class="flex items-center justify-center gap-2 mt-2 flex-wrap">
                                <!-- Tier Pill -->
                                <span 
                                    class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border"
                                    :class="selectedModalBadge?.tier_badge"
                                    x-text="(selectedModalBadge?.tier || 'bronze') + ' tier'"
                                ></span>

                                <!-- Points Pill -->
                                <span 
                                    class="text-[10px] font-mono font-bold px-2 py-0.5 rounded border border-teal-700/50 bg-teal-950/80 text-teal-300"
                                    x-text="'+' + (selectedModalBadge?.points || 0) + ' pts'"
                                ></span>
                            </div>

                            <!-- Full Description Lore -->
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mt-3 px-2 sm:px-4" x-text="selectedModalBadge?.description"></p>
                        </div>

                        <!-- Scouting Criteria & Award Telemetry Card -->
                        <div class="bg-slate-950/70 border border-slate-800/90 rounded-2xl p-4 space-y-3 text-xs">
                            <!-- Milestone Trigger Summary -->
                            <template x-if="selectedModalBadge?.trigger_summary">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-400 flex items-center gap-1.5">
                                        <x-lucide-target class="w-3.5 h-3.5 text-teal-400" />
                                        <span>Milestone Criteria / Trigger</span>
                                    </span>
                                    <div class="bg-slate-900/90 rounded-xl p-2.5 border border-slate-800 text-slate-200 font-medium leading-relaxed" x-text="selectedModalBadge.trigger_summary"></div>
                                </div>
                            </template>

                            <!-- Awarded Date & Angler Attribution -->
                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-800/80 text-slate-400 text-xs">
                                <div class="flex items-center gap-1.5 font-mono">
                                    <x-lucide-calendar class="w-3.5 h-3.5 text-slate-500" />
                                    <span x-text="'Awarded ' + (selectedModalBadge?.awarded_at || '—')"></span>
                                </div>

                                <template x-if="selectedModalBadge?.angler_name">
                                    <div class="flex items-center gap-1.5 font-medium text-slate-300 truncate">
                                        <x-lucide-anchor class="w-3.5 h-3.5 text-teal-400 shrink-0" />
                                        <span class="truncate" x-text="selectedModalBadge.angler_name"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Qualifying Catch Photo Showcase -->
                            <template x-if="selectedModalBadge?.catch_photo_url">
                                <div class="pt-3 border-t border-slate-800/80 space-y-2">
                                    <div class="flex items-center justify-between text-[10.5px]">
                                        <span class="font-bold text-amber-300 flex items-center gap-1.5 uppercase tracking-wider">
                                            <x-lucide-camera class="w-3.5 h-3.5 text-amber-400" />
                                            <span>Qualifying Catch Photo</span>
                                        </span>
                                        <span class="text-slate-400 font-mono" x-text="selectedModalBadge?.catch_date || ''"></span>
                                    </div>
                                    <div class="relative rounded-xl overflow-hidden border border-slate-800 bg-slate-900 group/catchphoto shadow-inner">
                                        <img 
                                            :src="selectedModalBadge.catch_photo_url" 
                                            :alt="selectedModalBadge?.catch_species || 'Qualifying Catch'" 
                                            class="w-full max-h-56 object-cover object-center group-hover/catchphoto:scale-105 transition-transform duration-300"
                                        />
                                        <!-- Photo Overlay Telemetry Pill -->
                                        <div class="absolute bottom-2 left-2 right-2 bg-slate-950/85 backdrop-blur-md px-3 py-1.5 rounded-lg border border-slate-800/80 flex items-center justify-between text-xs">
                                            <div class="flex items-center gap-1.5 font-bold text-white truncate">
                                                <x-lucide-fish class="w-3.5 h-3.5 text-teal-400 shrink-0" />
                                                <span class="truncate" x-text="selectedModalBadge?.catch_species || 'Catch'"></span>
                                            </div>
                                            <div class="flex items-center gap-2 font-mono text-[11px] shrink-0 text-slate-300">
                                                <template x-if="selectedModalBadge?.catch_length">
                                                    <span class="text-teal-300 font-bold" x-text="selectedModalBadge.catch_length + '&quot;'"></span>
                                                </template>
                                                <template x-if="selectedModalBadge?.catch_lake">
                                                    <span class="text-slate-400 truncate max-w-[110px]" x-text="selectedModalBadge.catch_lake"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Catch Record Link Action -->
                            <template x-if="selectedModalBadge?.record_id">
                                <div class="pt-2">
                                    <a 
                                        :href="'/record/' + selectedModalBadge.record_id" 
                                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-teal-950/40 transition-all cursor-pointer"
                                    >
                                        <x-lucide-fish class="w-4 h-4 text-teal-200" />
                                        <span>View Associated Catch Record</span>
                                        <x-lucide-arrow-right class="w-3.5 h-3.5 text-teal-100" />
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Navigation Footer: Prev / Done / Next -->
                    <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-800/80 text-xs relative z-10 shrink-0">
                        <button 
                            type="button" 
                            @click="prevModalBadge()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold transition-colors cursor-pointer"
                            title="Previous badge (Left Arrow)"
                        >
                            <x-lucide-chevron-left class="w-3.5 h-3.5" />
                            <span>Previous</span>
                        </button>

                        <button 
                            type="button" 
                            @click="closeModal()" 
                            class="px-4 py-1.5 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition-colors cursor-pointer border border-slate-700/60"
                        >
                            Done
                        </button>

                        <button 
                            type="button" 
                            @click="nextModalBadge()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold transition-colors cursor-pointer"
                            title="Next badge (Right Arrow)"
                        >
                            <span>Next</span>
                            <x-lucide-chevron-right class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
