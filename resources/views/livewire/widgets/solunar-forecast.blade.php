<div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 sm:p-5 transition-all">
    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pb-3 mb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                Solunar & Moon Phase Feeding Forecast
            </h3>
            <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-lg font-mono">
                <x-lucide-map-pin class="w-3 h-3 text-teal-600" />
                <span>{{ $lakeName }}</span>
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
        <div class="md:col-span-4 flex flex-col justify-center">
            <h4 class="text-xs font-bold text-slate-900 mb-1.5">24-hour Bite timeline</h4>

            <div class="relative w-full pt-3">
                <!-- Current Time Marker Indicator -->
                @if($isToday)
                    @php
                        $nowH = (int) now()->format('G');
                        $nowM = (int) now()->format('i');
                        $currentPercent = min(99, max(1, (($nowH + ($nowM / 60)) / 24.0) * 100));
                    @endphp
                    <div class="absolute top-0 -translate-x-1/2 flex flex-col items-center pointer-events-none z-10" style="left: {{ $currentPercent }}%;">
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
                <div class="flex justify-between text-[8px] font-mono text-slate-400 mt-1 px-0.5 select-none">
                    <span>00</span>
                    <span>01</span>
                    <span>02</span>
                    <span>03</span>
                    <span>04</span>
                    <span>05</span>
                    <span>06</span>
                    <span>09</span>
                    <span>10</span>
                    <span>11</span>
                    <span>02</span>
                    <span>03</span>
                    <span>04</span>
                    <span>05</span>
                    <span>06</span>
                    <span>12</span>
                    <span>15</span>
                    <span>18</span>
                    <span>21</span>
                    <span>22</span>
                    <span>23</span>
                    <span>24</span>
                </div>
            </div>
        </div>
    </div>
</div>

