@props([
    'lake',
    'catchesCount' => null,
    'showCoordinates' => true,
    'compact' => false,
])

@php
    $totalCatches = $catchesCount ?? ($lake->records_count ?? ($lake->records ? $lake->records->count() : 0));
    $hasGeo = !empty($lake->latitude) && !empty($lake->longitude);
@endphp

<div {{ $attributes->merge(['class' => 'group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-teal-300 transition-all duration-200 p-5 flex flex-col justify-between space-y-4']) }}>
    <div class="space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center shrink-0 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-200 shadow-2xs">
                    <i data-lucide="waves" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base tracking-tight group-hover:text-teal-600 transition-colors">
                        <a href="{{ url('/lake/' . $lake->id) }}" class="focus:outline-hidden">
                            {{ $lake->name }}
                        </a>
                    </h3>
                    @if($lake->fishingZone)
                        <span class="text-xs text-slate-500 font-medium block">
                            {{ $lake->fishingZone->name ?? $lake->fishingZone->code }}
                        </span>
                    @endif

                </div>
            </div>
            <span class="px-2.5 py-1 bg-slate-100 font-mono font-bold text-xs text-slate-700 rounded-full border border-slate-200 shrink-0">
                {{ number_format($totalCatches) }} catch{{ $totalCatches === 1 ? '' : 'es' }}
            </span>
        </div>

        @if($showCoordinates && $hasGeo)
            <div class="flex items-center gap-2 text-xs text-slate-500 font-mono bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-teal-600 shrink-0"></i>
                <span>{{ number_format($lake->latitude, 4) }}, {{ number_format($lake->longitude, 4) }}</span>
            </div>
        @endif
    </div>

    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <span class="text-[11px] font-semibold text-slate-400">Waterbody Dossier</span>
        <a href="{{ url('/lake/' . $lake->id) }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline flex items-center gap-1">
            <span>Explore Water →</span>
        </a>
    </div>
</div>
