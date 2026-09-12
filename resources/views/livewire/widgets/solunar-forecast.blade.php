<div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 sm:p-5 transition-all">
    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pb-3 mb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                Solunar & Moon Phase Feeding Forecast
            </h3>
            <span class="hidden sm:inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-lg font-mono">
                <x-lucide-map-pin class="w-3 h-3 text-teal-600 shrink-0" />
                <span>{{ $lakeName }}</span>
                <span class="text-slate-300">·</span>
                <span class="text-teal-700 font-bold bg-teal-50 border border-teal-200/60 px-1 py-0.2 rounded text-[10px]">{{ $solunar['timezoneAbbr'] }}</span>
            </span>
        </div>

        <!-- Date Controls & Day Rating -->
        <div class="flex items-center gap-2 self-stretch sm:self-auto justify-between sm:justify-end">
            <!-- Reactive Date Navigator -->
            <div class="inline-flex items-center bg-slate-50 border border-slate-200/80 rounded-xl p-1 text-xs font-semibold">
                <button type="button" wire:click="previousDay" class="p-1 text-slate-500 hover:text-slate-900 hover:bg-white rounded-lg transition-colors cursor-pointer" title="Previous Day">
                    <x-lucide-chevron-left class="w-4 h-4" />
                </button>
                <button type="button" wire:click="today" class="px-2 py-0.5 text-xs font-bold rounded-lg transition-colors cursor-pointer {{ $isToday ? 'bg-white text-teal-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Today
                </button>
                <button type="button" wire:click="nextDay" class="p-1 text-slate-500 hover:text-slate-900 hover:bg-white rounded-lg transition-colors cursor-pointer" title="Next Day">
                    <x-lucide-chevron-right class="w-4 h-4" />
                </button>
                <input type="date" wire:model.live="date" class="ml-1 text-[11px] font-mono bg-white border border-slate-200 rounded-lg px-2 py-0.5 text-slate-700 focus:ring-1 focus:ring-teal-500 focus:border-teal-500" />
            </div>

            <!-- Day Rating Badge -->
            <div class="flex items-center gap-1.5 bg-slate-900 text-white px-2.5 py-1 rounded-xl text-xs font-bold border border-slate-800 shrink-0">
                <span class="text-amber-400 font-mono">{{ $solunar['rating']['score'] }}/5</span>
                <span class="text-[10px] uppercase font-black text-emerald-400 tracking-wider hidden sm:inline">{{ $solunar['rating']['label'] }}</span>
            </div>
        </div>
    </div>

    <!-- Main 4-Column Layout -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 lg:gap-6 items-center">
        <!-- 1. Moon Phase Card (Col Span 4) -->
        <div class="md:col-span-4 bg-slate-50/80 border border-slate-200/80 rounded-xl p-3 flex items-center gap-3.5">
            <div class="relative w-10 h-10 shrink-0 flex items-center justify-center">
                <svg viewBox="0 0 32 32" class="w-9 h-9 drop-shadow-xs">
                    <!-- Dark moon disc base -->
                    <circle cx="16" cy="16" r="14" fill="#2d3748" />
                    <!-- Illuminated lunar phase -->
                    @if($solunar['moon']['svgPath']['type'] === 'full')
                        <circle cx="16" cy="16" r="14" fill="#e2e8f0" />
                    @elseif(!empty($solunar['moon']['svgPath']['path']))
                        <path d="{{ $solunar['moon']['svgPath']['path'] }}" fill="#e2e8f0" />
                    @endif
                </svg>
            </div>
            <div class="min-w-0">
                <h4 class="text-xs font-bold text-slate-900">Moon Phase</h4>
                <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">
                    {{ $solunar['moon']['phase'] }} &ndash; {{ $solunar['moon']['illumination'] }}% Illuminated
                </p>
            </div>
        </div>

        <!-- 2. Major Feed (Col Span 2) -->
        <div class="md:col-span-2">
            <h4 class="text-xs font-bold text-slate-900">Major Feed</h4>
            <div class="mt-1 space-y-0.5 text-xs text-slate-700 font-mono font-medium">
                @foreach($solunar['majorWindows'] as $maj)
                    <div>{{ $maj['start'] }} - {{ $maj['end'] }}</div>
                @endforeach
            </div>
        </div>

        <!-- 3. Minor Feed (Col Span 2) -->
        <div class="md:col-span-2">
            <h4 class="text-xs font-bold text-slate-900">Minor Feed</h4>
            <div class="mt-1 space-y-0.5 text-xs text-slate-700 font-mono font-medium">
                @foreach($solunar['minorWindows'] as $min)
                    <div>{{ $min['start'] }} - {{ $min['end'] }}</div>
                @endforeach
            </div>
        </div>

        <!-- 4. 24-hour Bite Timeline (Col Span 4) -->
        <div 
            x-data="{
                hovered: false,
                hoverPercent: 0,
                hoverTime: '',
                hoverStatus: '',
                hoverStatusClass: '',
                hourlyData: @js($solunar['hourlyIntensity']),
                onMouseMove(e) {
                    const rect = this.$refs.chartContainer.getBoundingClientRect();
                    if (!rect.width) return;
                    const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
                    const x = Math.max(0, Math.min(clientX - rect.left, rect.width));
                    this.hoverPercent = (x / rect.width) * 100;
                    const decimalHour = (x / rect.width) * 24.0;
                    
                    let h = Math.floor(decimalHour);
                    let m = Math.round((decimalHour - h) * 60);
                    if (m === 60) {
                        h = (h + 1) % 24;
                        m = 0;
                    }
                    const period = h >= 12 ? 'PM' : 'AM';
                    const h12 = h % 12 || 12;
                    this.hoverTime = `${String(h12).padStart(2, '0')}:${String(m).padStart(2, '0')} ${period}`;
                    
                    const hourIdx = Math.min(23, Math.max(0, Math.floor(decimalHour)));
                    const data = this.hourlyData[hourIdx] || {};
                    if (data.status === 'major') {
                        this.hoverStatus = 'Major Peak (2h)';
                        this.hoverStatusClass = 'text-amber-400 font-bold';
                    } else if (data.status === 'minor') {
                        this.hoverStatus = 'Minor Window (1h)';
                        this.hoverStatusClass = 'text-teal-400 font-bold';
                    } else {
                        this.hoverStatus = 'Normal Activity';
                        this.hoverStatusClass = 'text-slate-400';
                    }
                }
            }"
            class="md:col-span-4 flex flex-col justify-center"
        >
            <div class="flex items-center justify-between mb-1.5">
                <h4 class="text-xs font-bold text-slate-900">24-hour Bite timeline</h4>
                <span class="text-[10px] text-slate-600 font-medium hidden sm:inline">Hover chart for exact time</span>
            </div>

            <div 
                x-ref="chartContainer"
                @mouseenter="hovered = true"
                @mouseleave="hovered = false"
                @mousemove="onMouseMove($event)"
                @touchstart.passive="hovered = true; onMouseMove($event)"
                @touchmove.passive="onMouseMove($event)"
                @touchend="hovered = false"
                class="relative w-full pt-4 cursor-crosshair group"
            >
                <!-- Interactive Hover Tooltip & Crosshair -->
                <template x-if="hovered">
                    <div 
                        class="absolute -top-4 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-mono px-2 py-0.5 rounded-lg shadow-lg pointer-events-none whitespace-nowrap z-30 flex items-center gap-1.5 border border-slate-700/80"
                        :style="'left: ' + hoverPercent + '%;'"
                    >
                        <span class="font-bold text-amber-300" x-text="hoverTime + ' ' + @js($solunar['timezoneAbbr'])"></span>
                        <span class="text-slate-500">·</span>
                        <span :class="hoverStatusClass" x-text="hoverStatus"></span>
                    </div>
                </template>

                <template x-if="hovered">
                    <div 
                        class="absolute top-2 bottom-0 w-px bg-slate-800/80 pointer-events-none z-20"
                        :style="'left: ' + hoverPercent + '%;'"
                    >
                        <div class="w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white -translate-x-1/2 absolute top-0"></div>
                    </div>
                </template>

                <!-- Current Time Marker Indicator (Hidden on Hover to prevent clutter) -->
                @if($isToday)
                    @php
                        $nowH = (int) now()->format('G');
                        $nowM = (int) now()->format('i');
                        $currentPercent = min(99, max(1, (($nowH + ($nowM / 60)) / 24.0) * 100));
                    @endphp
                    <div 
                        x-show="!hovered" 
                        class="absolute top-0 -translate-x-1/2 flex flex-col items-center pointer-events-none z-10 transition-opacity" 
                        style="left: {{ $currentPercent }}%;"
                    >
                        <span class="text-[8px] font-bold text-slate-800 leading-none">Current</span>
                        <span class="text-[7px] text-slate-900 leading-none mt-0.5">▼</span>
                    </div>
                @endif

                <!-- SVG Continuous Wave Chart -->
                <div class="w-full h-10 relative">
                    <svg viewBox="{{ $solunar['waveCurve']['viewBox'] }}" preserveAspectRatio="none" class="w-full h-full overflow-visible">
                        <defs>
                            <linearGradient id="biteWaveGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#f97316" stop-opacity="0.9" />
                                <stop offset="35%" stop-color="#fbbf24" stop-opacity="0.8" />
                                <stop offset="75%" stop-color="#a7f3d0" stop-opacity="0.4" />
                                <stop offset="100%" stop-color="#e2e8f0" stop-opacity="0.1" />
                            </linearGradient>
                        </defs>

                        <!-- Subtle Baseline Bar -->
                        <line x1="0" y1="{{ $solunar['waveCurve']['baseY'] }}" x2="{{ $solunar['waveCurve']['width'] }}" y2="{{ $solunar['waveCurve']['baseY'] }}" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" />

                        <!-- Wave Area Fill -->
                        <path d="{{ $solunar['waveCurve']['fillPath'] }}" fill="url(#biteWaveGrad)" />

                        <!-- Wave Ridge Line Stroke -->
                        <path d="{{ $solunar['waveCurve']['strokePath'] }}" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <!-- 24-Hour Timeline Ticks -->
                <div class="flex justify-between text-[10px] font-mono font-medium text-slate-500 mt-1.5 px-0.5 select-none">
                    <span>12 AM</span>
                    <span class="hidden sm:inline">3 AM</span>
                    <span>6 AM</span>
                    <span class="hidden sm:inline">9 AM</span>
                    <span>12 PM</span>
                    <span class="hidden sm:inline">3 PM</span>
                    <span>6 PM</span>
                    <span class="hidden sm:inline">9 PM</span>
                    <span>12 AM</span>
                </div>
            </div>
        </div>
    </div>
</div>

