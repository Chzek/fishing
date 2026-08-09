@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Fisheries Management Zones (FMZs)</h1>
                <p class="text-xs text-teal-400 mt-0.5">Canadian & Ontario Provincial Fishing License Boundaries</p>
            </div>
        </div>

        <span class="bg-teal-500/15 text-teal-300 border border-teal-500/30 text-xs font-semibold px-3.5 py-1.5 rounded-full font-mono">
            {{ count($fishingZones) }} FMZs Defined
        </span>
    </div>

    <!-- FMZ List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($fishingZones as $zone)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:border-teal-500/40 transition-all space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200 font-mono">
                            {{ $zone->code }}
                        </span>
                        <span class="text-xs text-slate-500 font-medium flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $zone->province_state }}, {{ $zone->country }}
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-slate-900 leading-tight">
                        <a href="{{ url('/fishing-zone/' . $zone->id) }}" class="hover:text-teal-600 hover:underline">
                            {{ $zone->name }}
                        </a>
                    </h2>

                    @if($zone->description)
                        <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                            {{ $zone->description }}
                        </p>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <i data-lucide="waves" class="w-3.5 h-3.5 text-teal-600"></i>
                        <strong class="font-mono text-slate-800">{{ $zone->lakes_count }}</strong> Lakes
                    </span>

                    <a href="{{ url('/fishing-zone/' . $zone->id) }}" class="text-teal-600 hover:text-teal-700 font-bold flex items-center gap-1">
                        <span>Details</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
