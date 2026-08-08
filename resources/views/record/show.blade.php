@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                    {{ $record->length }}in. 
                    @if($record->weight)
                        {{ $record->weight }}lbs. 
                    @endif
                    {{ $record->fishBreed->name ?? 'Fish' }}
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-2">
                    <span class="font-bold text-teal-600">{{ $record->fishBreed->family->name ?? '' }} Family</span>
                    <span>•</span>
                    <span>{{ $record->caught }}</span>
                </p>
            </div>
            <div class="shrink-0">
                @if($record->released)
                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200">
                        <i data-lucide="heart" class="w-3.5 h-3.5 text-emerald-500"></i> Released
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 text-xs font-bold px-3 py-1 rounded-full border border-sky-200">
                        <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-sky-500"></i> Kept
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Angler</span>
                <span class="text-base font-bold text-slate-800 block">{{ $record->angler->fullName }}</span>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Water / Lake</span>
                <span class="text-base font-bold text-slate-800 block">{{ $record->lake->name }}</span>
            </div>
        </div>

        @if($record->temperature)
            <div class="p-4 rounded-xl bg-teal-50/60 border border-teal-200/80 flex items-center gap-3">
                <i data-lucide="thermometer" class="w-5 h-5 text-teal-600 shrink-0"></i>
                <div class="text-xs text-teal-900 font-medium">
                    <strong>Water Temperature:</strong> {{ $record->temperature }}°F (Logged on boat)
                </div>
            </div>
        @endif

        @if($record->dailyWeather)
            <div class="bg-slate-900 text-slate-200 rounded-2xl p-5 border border-slate-800 space-y-3">
                <div class="flex items-center gap-2 text-teal-400 font-bold text-xs uppercase tracking-wider">
                    <i data-lucide="cloud-sun" class="w-4 h-4"></i>
                    <span>Daily Environmental Telemetry</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center pt-2 border-t border-slate-800 text-xs">
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 block">Condition</span>
                        <strong class="text-white block mt-0.5">{{ $record->dailyWeather->weather_condition }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 block">Air Temp</span>
                        <strong class="text-white block mt-0.5">{{ $record->dailyWeather->air_temp_min }}°F – {{ $record->dailyWeather->air_temp_max }}°F</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 block">Barometric</span>
                        <strong class="text-white block mt-0.5">{{ $record->dailyWeather->barometric_pressure }} hPa</strong>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 block">Wind</span>
                        <strong class="text-white block mt-0.5">{{ $record->dailyWeather->wind_speed_max }} mph</strong>
                    </div>
                </div>
            </div>
        @endif

        @if($record->lure)
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Lure / Bait Used</span>
                <span class="text-sm font-semibold text-slate-800 block">{{ $record->lure->displayName }}</span>
            </div>
        @endif

        @if($record->latitude && $record->longitude)
            <div class="space-y-2 pt-2">
                <div class="flex items-center justify-between text-xs text-slate-700 font-bold uppercase tracking-wider">
                    <span>📍 Catch GPS Pinpoint Location</span>
                    <span class="font-mono text-slate-500 text-[11px] font-normal">{{ $record->latitude }}, {{ $record->longitude }}</span>
                </div>
                <div id="catch-pin-map" class="w-full h-56 rounded-xl border border-slate-200 overflow-hidden"></div>
            </div>
        @endif

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <a href='/record/{{ $record->id }}/edit' class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                <span>Edit Record</span>
            </a>
            <a href='/record' class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors">Return to Logbook</a>
        </div>
    </div>
</div>
@endsection

@if($record->latitude && $record->longitude)
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $record->latitude }};
        const lng = {{ $record->longitude }};

        const map = L.map('catch-pin-map').setView([lat, lng], 14);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 16,
            attribution: 'Tiles &copy; Esri, NRCan CanVec'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>🐟 {{ $record->fishBreed->name ?? 'Catch' }}</b><br>Length: {{ $record->length }} in.")
            .openPopup();
    });
</script>
@endsection
@endif
