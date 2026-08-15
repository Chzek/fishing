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

        <!-- Catch Photos Showcase -->
        @if($record->photos && $record->photos->count() > 0)
            <div class="space-y-3">
                @php $primary = $record->primaryPhoto(); @endphp
                <div class="relative group rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-900 aspect-16/10 max-h-[380px]">
                    <img id="main-catch-photo" src="{{ $primary->url }}" alt="Catch photo" class="w-full h-full object-cover cursor-pointer hover:scale-102 transition-transform duration-300" onclick="openPhotoLightbox('{{ $primary->url }}', '{{ addslashes($record->fishBreed->name ?? 'Catch') }} - {{ $record->length }}in')">
                    
                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-950/80 via-slate-950/40 to-transparent p-4 flex items-center justify-between text-white">
                        <span class="text-xs font-semibold drop-shadow-sm flex items-center gap-1.5">
                            <i data-lucide="camera" class="w-4 h-4 text-teal-400"></i>
                            <span>Catch Photo ({{ $record->photos->count() }} attached)</span>
                        </span>

                        <div class="flex items-center gap-2">
                            @auth
                                @if(auth()->user()->angler && auth()->user()->angler->id == $record->anglers_id)
                                    <form action="{{ route('photos.avatar', $primary) }}" method="POST" class="inline" onsubmit="return confirm('Use this catch photo as your profile avatar?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-teal-500/90 hover:bg-teal-400 text-slate-950 text-[11px] font-bold rounded-xl shadow backdrop-blur-xs transition flex items-center gap-1 cursor-pointer">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                            <span>Set as Avatar</span>
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                @if($record->photos->count() > 1)
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 pt-1">
                        @foreach($record->photos as $p)
                            <button type="button" onclick="document.getElementById('main-catch-photo').src='{{ $p->url }}'" class="aspect-square rounded-xl overflow-hidden border-2 hover:border-teal-500 transition-all focus:outline-none focus:ring-2 focus:ring-teal-500/50 bg-slate-100 cursor-pointer">
                                <img src="{{ $p->url }}" alt="Thumbnail" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

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
            <div class="flex items-center gap-2">
                <a href='/record/{{ $record->id }}/edit' class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Edit Record</span>
                </a>
                <form action="/record/{{ $record->id }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this catch record?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 font-bold text-xs rounded-xl transition-colors cursor-pointer">
                        <i data-lucide="trash-2" class="w-4 h-4 text-rose-500"></i>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
            <a href='/record' class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors">Return to Logbook</a>
        </div>
    </div>
</div>

<!-- Simple Lightbox Modal -->
<div id="photo-lightbox-modal" class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-sm hidden flex items-center justify-center p-4" onclick="closePhotoLightbox()">
    <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center" onclick="event.stopPropagation()">
        <img id="photo-lightbox-img" src="" alt="Catch Full Photo" class="max-w-full max-h-[80vh] rounded-2xl object-contain shadow-2xl">
        <p id="photo-lightbox-caption" class="text-white text-xs font-semibold mt-3 text-center"></p>
        <button type="button" onclick="closePhotoLightbox()" class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-sm hover:bg-slate-700 shadow-lg cursor-pointer">✕</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openPhotoLightbox(src, caption) {
        document.getElementById('photo-lightbox-img').src = src;
        document.getElementById('photo-lightbox-caption').textContent = caption || '';
        document.getElementById('photo-lightbox-modal').classList.remove('hidden');
    }

    function closePhotoLightbox() {
        document.getElementById('photo-lightbox-modal').classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePhotoLightbox();
    });

    @if($record->latitude && $record->longitude)
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
    @endif
</script>
@endsection
