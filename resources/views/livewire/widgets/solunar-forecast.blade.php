<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all">
    <!-- Header Row -->
    <div class="p-4 sm:p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 {{ $collapsed ? '' : 'border-b border-slate-100' }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-xl shrink-0">
                <span>{{ $solunar['moon']['emoji'] }}</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-black text-slate-900 text-sm sm:text-base tracking-tight flex items-center gap-1.5">
                        <span>Solunar & Moon Feeding Forecast</span>
                    </h2>
                    <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-lg font-mono">
                        <x-lucide-map-pin class="w-3 h-3 text-teal-600" />
                        <span>{{ $lakeName }}</span>
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5 font-medium">
                    <span>100% Offline Tactical Bite Windows & Celestial Timing</span>
                </p>
            </div>
        </div>

        <!-- Date Controls & Day Rating -->
        <div class="flex flex-wrap items-center gap-2 self-stretch sm:self-auto justify-between sm:justify-end">
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
            <div class="flex items-center gap-2 bg-slate-900 text-white px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-800 shrink-0">
                <span class="text-amber-400 font-mono">{{ $solunar['rating']['score'] }}/5</span>
                <span class="text-[10px] uppercase font-black text-emerald-400 tracking-wider">{{ $solunar['rating']['label'] }}</span>
            </div>

            <!-- Collapse Toggle -->
            <button type="button" wire:click="toggleCollapsed" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer" title="{{ $collapsed ? 'Expand Forecast' : 'Collapse Forecast' }}">
                @if($collapsed)
                    <x-lucide-chevron-down class="w-4 h-4" />
                @else
                    <x-lucide-chevron-up class="w-4 h-4" />
                @endif
            </button>
        </div>
    </div>

    @if(!$collapsed)
        <!-- 4-Column Key Solunar Metrics Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 text-xs">
            <!-- 1. Moon Phase -->
            <div class="p-4 space-y-1 bg-slate-50/40">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block flex items-center gap-1">
                    <span>Moon Phase</span>
                </span>
                <div class="flex items-center gap-2 pt-0.5">
                    <span class="text-xl">{{ $solunar['moon']['emoji'] }}</span>
                    <div>
                        <strong class="text-sm font-black text-slate-900 block leading-tight">{{ $solunar['moon']['phase'] }}</strong>
                        <span class="text-[11px] font-mono text-teal-600 font-semibold">{{ $solunar['moon']['illumination'] }}% Illuminated ({{ $solunar['moon']['ageDays'] }}d)</span>
                    </div>
                </div>
            </div>

            <!-- 2. Major Feeding Windows (2 Hours) -->
            <div class="p-4 space-y-1">
                <span class="text-[10px] uppercase font-bold text-amber-700 tracking-wider block flex items-center justify-between">
                    <span class="flex items-center gap-1">👑 Major Windows</span>
                    <span class="text-[9px] bg-amber-100 text-amber-800 font-mono font-bold px-1.5 py-0.2 rounded">2-Hr Peak</span>
                </span>
                <div class="space-y-0.5 pt-0.5">
                    @foreach($solunar['majorWindows'] as $maj)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 text-[11px]">{{ $maj['type'] }}:</span>
                            <span class="font-mono font-bold text-slate-900">{!! $maj['display'] !!}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. Minor Feeding Windows (1 Hour) -->
            <div class="p-4 space-y-1">
                <span class="text-[10px] uppercase font-bold text-teal-700 tracking-wider block flex items-center justify-between">
                    <span class="flex items-center gap-1">⚡ Minor Windows</span>
                    <span class="text-[9px] bg-teal-100 text-teal-800 font-mono font-bold px-1.5 py-0.2 rounded">1-Hr Window</span>
                </span>
                <div class="space-y-0.5 pt-0.5">
                    @foreach($solunar['minorWindows'] as $min)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 text-[11px]">{{ $min['type'] }}:</span>
                            <span class="font-mono font-bold text-slate-900">{!! $min['display'] !!}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 4. Sun Times & Day Length -->
            <div class="p-4 space-y-1 bg-slate-50/40">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block flex items-center justify-between">
                    <span>Sun & Daylight</span>
                    <span class="text-[9px] font-mono text-slate-500">{{ $solunar['sun']['dayLength'] }} light</span>
                </span>
                <div class="space-y-0.5 pt-0.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1">
                            <x-lucide-sunrise class="w-3 h-3 text-amber-500" />
                            <span>Sunrise:</span>
                        </span>
                        <span class="font-mono font-bold text-slate-900">{{ $solunar['sun']['sunrise'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1">
                            <x-lucide-sunset class="w-3 h-3 text-orange-500" />
                            <span>Sunset:</span>
                        </span>
                        <span class="font-mono font-bold text-slate-900">{{ $solunar['sun']['sunset'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 24-Hour Bite Activity Timeline Bar -->
        <div class="p-4 sm:p-5 bg-slate-900 text-white space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-200 flex items-center gap-1.5">
                    <x-lucide-clock class="w-3.5 h-3.5 text-teal-400" />
                    <span>24-Hour Solunar Feeding Activity Ribbon ({{ $solunar['formattedDate'] }})</span>
                </span>
                <div class="flex items-center gap-3 text-[10px] font-semibold text-slate-300">
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block shadow-xs"></span>
                        <span>Major Period (2h)</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-400 inline-block shadow-xs"></span>
                        <span>Minor Period (1h)</span>
                    </span>
                    <span class="hidden sm:flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-700 inline-block"></span>
                        <span>Night</span>
                    </span>
                    <span class="hidden sm:flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-600 inline-block"></span>
                        <span>Daylight</span>
                    </span>
                </div>
            </div>

            <!-- Visual Bar Grid -->
            <div class="space-y-1.5">
                <div class="h-9 rounded-xl overflow-hidden border border-slate-700/80 bg-slate-950/80 p-0.5 gap-0.5" style="display: grid; grid-template-columns: repeat(24, minmax(0, 1fr));">
                    @php
                        $currentHour = (int) now()->format('H');
                    @endphp
                    @foreach($solunar['hourlyIntensity'] as $hourData)
                        @php
                            $bg = 'bg-slate-800/60';
                            if ($hourData['isDaylight']) {
                                $bg = 'bg-slate-700/60';
                            }
                            if ($hourData['status'] === 'major') {
                                $bg = 'bg-gradient-to-t from-amber-600 to-amber-400 text-amber-950 font-black shadow-md shadow-amber-500/20';
                            } elseif ($hourData['status'] === 'minor') {
                                $bg = 'bg-gradient-to-t from-teal-600 to-teal-400 text-teal-950 font-black shadow-md shadow-teal-500/20';
                            }
                            $isNow = $isToday && ($currentHour === $hourData['hour']);
                        @endphp
                        <div class="h-full rounded-md flex flex-col items-center justify-center transition-all relative group {{ $bg }} {{ $isNow ? 'ring-2 ring-white ring-offset-1 ring-offset-slate-900 z-10' : '' }}" title="{{ $hourData['timeLabel'] }}: {{ ucfirst($hourData['status']) }} Activity {{ $isNow ? '(Current Hour)' : '' }}">
                            @if($hourData['status'] === 'major')
                                <span class="text-[8px] uppercase tracking-tighter">MAJ</span>
                            @elseif($hourData['status'] === 'minor')
                                <span class="text-[8px] uppercase tracking-tighter">MIN</span>
                            @elseif($hourData['isSunrise'])
                                <x-lucide-sunrise class="w-2.5 h-2.5 text-amber-300" />
                            @elseif($hourData['isSunset'])
                                <x-lucide-sunset class="w-2.5 h-2.5 text-orange-300" />
                            @endif

                            @if($isNow)
                                <div class="absolute -top-1 w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Hour Tick Markers (Every 2 Hours) -->
                <div class="grid grid-cols-12 text-[9px] font-mono text-slate-400 text-center px-0.5">
                    <span>00:00</span>
                    <span>02:00</span>
                    <span>04:00</span>
                    <span>06:00</span>
                    <span>08:00</span>
                    <span>10:00</span>
                    <span>12:00</span>
                    <span>14:00</span>
                    <span>16:00</span>
                    <span>18:00</span>
                    <span>20:00</span>
                    <span>22:00</span>
                </div>
            </div>
        </div>
    @endif
</div>
