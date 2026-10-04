@props([
    'record',
    'rank' => 0,
    'layout' => 'card',
    'showLake' => false,
    'showSpecies' => false,
    'showAngler' => true,
    'showScore' => false,
    'header' => null,
    'subtitle' => null,
])

@php
    $rankIndex = is_numeric($rank) ? (int) $rank : 0;
    $rankNumber = $rankIndex + 1;

    $mainTitle = $showAngler 
        ? ($record->angler?->fullName ?? 'Unknown Angler') 
        : ($record->fishBreed?->name ?? 'Fish');

    if (is_null($subtitle)) {
        if ($showAngler) {
            if ($showLake && $showSpecies) {
                $parts = array_filter([
                    $record->fishBreed?->name,
                    $record->lake?->name,
                ]);
                $subtitle = implode(' • ', $parts);
            } elseif ($showSpecies) {
                $subtitle = $record->fishBreed?->name ?? 'Fish';
            } else {
                $subtitle = $record->lake?->name ?? 'Waterbody';
            }
        } else {
            $subtitle = $record->lake?->name ?? 'Waterbody';
        }
    }

    $length = (float) ($record->length ?? 0);
    $weight = (float) ($record->weight ?? 0);
    $caughtDate = $record->caught ? \Illuminate\Support\Carbon::parse($record->caught)->format('M Y') : 'Historical';
    $trophyScore = (float) ($record->trophy_score ?? 0);
    $trophyTier = $record->trophy_tier ?? ['is_trophy' => false, 'label' => 'Standard Catch', 'badge_variant' => 'slate'];

    // Header generation for card layout
    if (is_null($header) && $layout === 'card') {
        if ($rankIndex === 0) {
            $header = $trophyTier['is_trophy'] ? '🏆 #1 Master Angler Specimen' : '🏆 #1 Top Ranked Catch';
        } else {
            $header = "#{$rankNumber} " . ($trophyTier['is_trophy'] ? 'Master Angler' : ($trophyTier['label'] ?? 'Specimen'));
        }
    }
@endphp

@if($layout === 'row' || $layout === 'list')
    <!-- Row / List Layout (Vertical Stack) -->
    <div {{ $attributes->merge(['class' => 'p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 hover:border-amber-300 dark:hover:border-amber-500 transition-all flex items-center justify-between gap-3 shadow-2xs group']) }}>
        <!-- Left: Rank Badge + Title & Subtitle -->
        <div class="flex items-center gap-3 min-w-0">
            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black font-mono shrink-0 {{ $rankIndex === 0 ? 'bg-amber-400 text-slate-900 shadow-2xs ring-1 ring-amber-500/50' : ($rankIndex === 1 ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white' : ($rankIndex === 2 ? 'bg-amber-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300')) }}">
                #{{ $rankNumber }}
            </span>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900 dark:text-white text-sm truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                        {{ $mainTitle }}
                    </span>
                    @if($trophyTier['is_trophy'])
                        <x-lucide-award class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                    @endif
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate flex items-center gap-1.5 mt-0.5">
                    <span class="truncate">{{ $subtitle }}</span>
                    <span>&bull;</span>
                    <span class="font-mono shrink-0 text-slate-400 dark:text-slate-500 text-[10px]">{{ $caughtDate }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Metrics Tower (Length, Weight, Normalized Score) -->
        <div class="flex items-center gap-3 font-mono shrink-0 text-right">
            <div class="leading-tight">
                @if($length > 0)
                    <strong class="text-sm font-black text-amber-600 dark:text-amber-400 block">{{ number_format($length, 1) }}"</strong>
                @endif
                @if($weight > 0)
                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 block">{{ number_format($weight, 2) }} lbs</span>
                @endif
            </div>
            @if($showScore && $trophyScore > 0)
                <span class="px-2 py-1 rounded-lg text-xs font-black tracking-tight {{ $trophyTier['is_trophy'] ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700' : 'bg-slate-200/80 dark:bg-slate-700/80 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600' }}">
                    {{ number_format($trophyScore, 1) }} pts
                </span>
            @endif
        </div>
    </div>
@else
    <!-- Card / Grid Layout -->
    <div {{ $attributes->merge(['class' => 'rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 hover:border-amber-300 dark:hover:border-amber-500 transition-all flex flex-col justify-between overflow-hidden relative group shadow-2xs']) }}>
        <!-- Header Banner -->
        @if($header)
            <div class="px-3 py-1.5 border-b text-[11px] font-bold flex items-center justify-between tracking-tight {{ $rankIndex === 0 ? 'bg-amber-400/15 dark:bg-amber-950/40 border-amber-200/60 dark:border-amber-800/60 text-amber-900 dark:text-amber-300' : 'bg-slate-100/80 dark:bg-slate-800/90 border-slate-200/60 dark:border-slate-700/60 text-slate-700 dark:text-slate-300' }}">
                <span class="truncate">{{ $header }}</span>
                @if($trophyTier['is_trophy'])
                    <x-lucide-award class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0 ml-1" />
                @endif
            </div>
        @endif

        <!-- Main Card Body: Left Story & Right Metrics Tower -->
        <div class="p-3 grid grid-cols-[1fr_auto] gap-2.5 items-center flex-1">
            <!-- Left Side: Icon, Name, Lake/Species + Date -->
            <div class="min-w-0 flex flex-col justify-between h-full space-y-1.5">
                <!-- Icon / Rank Badge -->
                <div class="flex items-center gap-1.5">
                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black font-mono shrink-0 {{ $rankIndex === 0 ? 'bg-amber-400 text-slate-900 shadow-2xs ring-1 ring-amber-500/50' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        #{{ $rankNumber }}
                    </span>
                    @if($showScore && $trophyScore > 0)
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black font-mono tracking-tight sm:hidden {{ $trophyTier['is_trophy'] ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700' : 'bg-slate-200/80 dark:bg-slate-700/80 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600' }}">
                            {{ number_format($trophyScore, 1) }} pts
                        </span>
                    @endif
                </div>

                <!-- Main Title (Angler or Species) -->
                @if($mainTitle)
                    <span class="font-bold text-slate-900 dark:text-white text-sm block truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors" title="{{ $mainTitle }}">
                        {{ $mainTitle }}
                    </span>
                @endif

                <!-- Bottom Row: Lake/Species & Date side-by-side -->
                <div class="flex items-center justify-between gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                    <span class="truncate font-medium" title="{{ $subtitle }}">{{ $subtitle }}</span>
                    <span class="font-mono shrink-0 text-slate-400 dark:text-slate-500 text-[10px]">{{ $caughtDate }}</span>
                </div>
            </div>

            <!-- Right Side: Metrics Tower (Length, Weight, Normalized Score) -->
            <div class="flex flex-col items-end justify-center text-right font-mono shrink-0 space-y-1 pl-2.5 border-l border-slate-200/60 dark:border-slate-700/60">
                @if($length > 0)
                    <strong class="text-base font-black text-amber-600 dark:text-amber-400 block leading-tight">{{ number_format($length, 1) }}"</strong>
                @endif
                @if($weight > 0)
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block leading-tight">{{ number_format($weight, 2) }} lbs</span>
                @endif
                @if($showScore && $trophyScore > 0)
                    <span class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black font-mono leading-tight tracking-tight {{ $trophyTier['is_trophy'] ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700' : 'bg-slate-200/80 dark:bg-slate-700/80 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600' }}" title="Trophy Rating Score: {{ $trophyScore }}% of Master Angler Benchmark ({{ $trophyTier['label'] }})">
                        {{ number_format($trophyScore, 1) }} pts
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif


