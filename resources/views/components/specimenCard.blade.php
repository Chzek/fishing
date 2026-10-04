@props([
    'record',
    'rank' => 0,
    'showLake' => false,
    'showSpecies' => false,
    'showAngler' => true,
    'subtitle' => null,
])

@php
    $rankIndex = is_numeric($rank) ? (int) $rank : 0;
    $rankNumber = $rankIndex + 1;

    if (is_null($subtitle)) {
        if ($showLake && $showSpecies) {
            $parts = array_filter([
                $record->fishBreed?->name,
                $record->lake?->name,
            ]);
            $subtitle = implode(' • ', $parts);
        } elseif ($showSpecies) {
            $subtitle = $record->fishBreed?->name ?? 'Fish';
        } elseif ($showLake) {
            $subtitle = $record->lake?->name ?? 'Waterbody';
        } else {
            $subtitle = $record->lake?->name ?? 'Waterbody';
        }
    }

    $anglerName = $showAngler ? ($record->angler?->fullName ?? 'Unknown') : null;
    $length = (float) ($record->length ?? 0);
    $weight = (float) ($record->weight ?? 0);
    $caughtDate = $record->caught ? \Illuminate\Support\Carbon::parse($record->caught)->format('M Y') : 'Historical';
@endphp

<div {{ $attributes->merge(['class' => 'p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/70 hover:border-amber-300 dark:hover:border-amber-500 transition-all flex flex-col justify-between relative group shadow-2xs']) }}>
    <div>
        <!-- Top Row: Rank badge on left, Length & Weight stack on right -->
        <div class="flex items-start justify-between gap-2 mb-2">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black font-mono shrink-0 {{ $rankIndex === 0 ? 'bg-amber-400 text-slate-900 shadow-2xs ring-1 ring-amber-500/50' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                #{{ $rankNumber }}
            </span>
            <div class="text-right font-mono shrink-0 leading-tight">
                @if($length > 0)
                    <strong class="text-base font-black text-amber-600 dark:text-amber-400 block">{{ number_format($length, 1) }}"</strong>
                @endif
                @if($weight > 0)
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block mt-0.5">{{ number_format($weight, 2) }} lbs</span>
                @endif
            </div>
        </div>

        <!-- Middle: Angler & Waterbody / Species -->
        <div class="space-y-0.5 min-w-0">
            @if($anglerName)
                <span class="font-bold text-slate-900 dark:text-white text-sm block truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                    {{ $anglerName }}
                </span>
            @endif
            @if($subtitle)
                <span class="text-xs text-slate-500 dark:text-slate-400 block truncate" title="{{ $subtitle }}">
                    {{ $subtitle }}
                </span>
            @endif
        </div>
    </div>

    <!-- Bottom: Centered Catch Date -->
    <div class="text-center text-xs font-mono font-medium text-slate-400 dark:text-slate-500 border-t border-slate-200/60 dark:border-slate-700/60 pt-2 mt-3">
        <span>{{ $caughtDate }}</span>
    </div>
</div>
