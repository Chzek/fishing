@props([
    'record',
    'showAngler' => true,
    'showLake' => true,
    'showSpeciesAvatar' => true,
])

@php
    $coverPhoto = $record->primaryPhoto();
    $imageUrl = $coverPhoto ? asset('storage/' . $coverPhoto->path) : null;
    $speciesName = $record->fishBreed?->name ?? 'Unidentified Species';
    $lakeName = $record->lake?->name ?? 'Unknown Waterbody';
    $anglerName = $record->angler ? $record->angler->firstName . ' ' . $record->angler->lastName : 'Angler';
    $caughtDate = $record->caught ? \Carbon\Carbon::parse($record->caught)->format('M d, Y') : null;
@endphp

<div {{ $attributes->merge(['class' => 'group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-teal-300 transition-all duration-200 overflow-hidden flex flex-col justify-between']) }}>
    <div>
        <!-- Photo Container / Fallback Header -->
        <div class="relative h-44 bg-slate-900 overflow-hidden">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $speciesName }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full bg-gradient-to-br from-slate-900 via-slate-800 to-teal-950 flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                    <x-fishAvatar :breed="$record->fishBreed" size="lg" />
                    <span class="text-[11px] font-bold text-slate-300 mt-2 truncate max-w-[90%]">{{ $speciesName }}</span>
                </div>
            @endif

            <!-- Species Avatar Badge Overlay -->
            @if($showSpeciesAvatar && $record->fishBreed)
                <div class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-md p-1 rounded-xl border border-slate-700/80 shadow-md">
                    <x-fishAvatar :breed="$record->fishBreed" size="xs" />
                </div>
            @endif

            <!-- Length / Weight Pill Badges Overlay -->
            <div class="absolute bottom-3 right-3 flex items-center gap-1.5 font-mono">
                @if($record->length)
                    <span class="px-2.5 py-1 bg-slate-950/85 backdrop-blur-md text-amber-300 font-extrabold text-xs rounded-lg border border-slate-800 shadow-md">
                        {{ number_format($record->length, 1) }}"
                    </span>
                @endif
                @if($record->weight)
                    <span class="px-2.5 py-1 bg-slate-950/85 backdrop-blur-md text-sky-300 font-extrabold text-xs rounded-lg border border-slate-800 shadow-md">
                        {{ number_format($record->weight, 1) }} lbs
                    </span>
                @endif
            </div>
        </div>

        <!-- Details Container -->
        <div class="p-4 space-y-2">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm tracking-tight group-hover:text-teal-600 transition-colors">
                        <a href="{{ url('/record/' . $record->id) }}" class="focus:outline-hidden">
                            {{ $speciesName }}
                        </a>
                    </h3>
                    @if($showLake && $record->lake)
                        <div class="text-xs text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                            <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 shrink-0"></i>
                            <a href="{{ url('/lake/' . $record->lakes_id) }}" class="hover:text-teal-600 truncate">
                                {{ $lakeName }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Container -->
    <div class="px-4 py-3 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        @if($showAngler && $record->angler)
            <div class="flex items-center gap-2 truncate">
                <x-anglerAvatar :angler="$record->angler" size="xs" />
                <span class="font-semibold text-slate-700 truncate">{{ $anglerName }}</span>
            </div>
        @else
            <span class="text-[11px] font-mono text-slate-400">{{ $caughtDate ?? 'Logged Catch' }}</span>
        @endif

        <a href="{{ url('/record/' . $record->id) }}" class="text-[11px] font-bold text-teal-600 hover:text-teal-700 hover:underline shrink-0 flex items-center gap-0.5">
            <span>Dossier</span>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
        </a>
    </div>
</div>
