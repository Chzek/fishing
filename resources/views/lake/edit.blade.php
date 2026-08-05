@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Lake: {{ $lake->name }}</h1>
                    <p class="text-xs text-slate-500">Update coordinates & waterbody details</p>
                </div>
            </div>

            <button type="button" id="btn-use-gps" onclick="useCurrentGPS()" class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs py-2 px-3.5 rounded-xl shadow transition-colors shrink-0">
                <i data-lucide="navigation" class="w-3.5 h-3.5"></i>
                <span>Use Current GPS Location</span>
            </button>
        </div>

        {!! Form::model($lake, ['url' => 'lake', 'method' => 'put', 'class' => 'space-y-4']) !!}
            {!! Form::hidden('id') !!}

            <div class="space-y-1.5">
                {!! Form::label('name', 'Lake / Waterbody Name', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::text('name', $lake->name, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'required' => true]) !!}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    {!! Form::label('latitude', 'Latitude (°N)', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('latitude', $lake->latitude, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'id' => 'input-lat']) !!}
                </div>
                <div class="space-y-1.5">
                    {!! Form::label('longitude', 'Longitude (°W)', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('longitude', $lake->longitude, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'id' => 'input-lng']) !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    {!! Form::label('structure', 'Bottom Terrain / Cover', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('structure', $lake->structure, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'placeholder' => 'e.g. Rock/Granite, Weedline, Drop-off']) !!}
                </div>
                <div class="space-y-1.5">
                    {!! Form::label('max_depth', 'Max Depth (ft)', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::number('max_depth', $lake->max_depth, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'placeholder' => 'e.g. 45']) !!}
                </div>
            </div>

            <!-- Interactive Map Location Picker -->
            <div class="space-y-2 pt-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Interactive Map Location Picker (Tap to Pin)</label>
                <p class="text-xs text-slate-500">Tap or click anywhere on the waterbody to update pin coordinates offline.</p>
                <div id="lake-picker-map" class="w-full h-[380px] rounded-2xl border border-slate-200 overflow-hidden"></div>
            </div>

            @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
                <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-4 text-xs space-y-1.5">
                    <strong class="font-bold text-amber-900 block">Identified Lakes Within 2 Miles ({{ $nearbyLakes->count() }}):</strong>
                    <p class="text-amber-800">Check existing lakes in this area before renaming to prevent duplicate entries:</p>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        @foreach($nearbyLakes as $nearLake)
                            <a href="{{ url('/lake/' . $nearLake->id) }}" target="_blank" class="bg-white hover:bg-amber-100 text-slate-800 border border-amber-300 font-semibold px-2.5 py-1 rounded-lg transition-colors">
                                🏞️ {{ $nearLake->name }} ({{ $nearLake->distance }} mi) ↗
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pt-4 flex items-center justify-between">
                <a href="{{ url('/lake/' . $lake->id) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors">Cancel</a>
                {!! Form::submit('Save Changes', ['class' => 'px-6 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors cursor-pointer']) !!}
            </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('scripts')
<script>
    let lakeMap, marker;

    document.addEventListener('DOMContentLoaded', function () {
        const defaultLat = parseFloat(document.getElementById('input-lat').value) || 48.15;
        const defaultLng = parseFloat(document.getElementById('input-lng').value) || -84.85;

        lakeMap = L.map('lake-picker-map').setView([defaultLat, defaultLng], 11);

        const topoLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Tiles &copy; Esri, NRCan CanVec'
        }).addTo(lakeMap);

        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Source: Esri, Maxar'
        });

        L.control.layers({
            "🗺️ Topo / Waterbody": topoLayer,
            "🛰️ Satellite Imagery": satLayer
        }).addTo(lakeMap);

        if (document.getElementById('input-lat').value && document.getElementById('input-lng').value) {
            marker = L.marker([defaultLat, defaultLng]).addTo(lakeMap);

            L.circle([defaultLat, defaultLng], {
                color: '#0d9488',
                fillColor: '#0d9488',
                fillOpacity: 0.08,
                radius: 3218.68
            }).addTo(lakeMap);
        }

        @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
            const nearbyLakesData = @json($nearbyLakes);

            nearbyLakesData.forEach(function (nLake) {
                if (nLake.latitude && nLake.longitude) {
                    const nMarker = L.circleMarker([nLake.latitude, nLake.longitude], {
                        radius: 8,
                        fillColor: "#10b981",
                        color: "#ffffff",
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(lakeMap);

                    nMarker.bindPopup(
                        "<b>🏞️ " + nLake.name + "</b><br>" +
                        "📍 " + nLake.distance + " miles away<br>" +
                        "<a href='/lake/" + nLake.id + "' target='_blank' class='btn btn-sm btn-outline-success mt-1'>View Lake ↗</a>"
                    );
                }
            });
        @endif

        lakeMap.on('click', function (e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);

            document.getElementById('input-lat').value = lat;
            document.getElementById('input-lng').value = lng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(lakeMap);
            }
        });
    });

    function useCurrentGPS() {
        if (!navigator.geolocation) {
            alert('Hardware GPS is not supported on this browser.');
            return;
        }

        const btn = document.getElementById('btn-use-gps');
        btn.innerText = '📡 Querying GPS...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);

                document.getElementById('input-lat').value = lat;
                document.getElementById('input-lng').value = lng;

                const latLng = [lat, lng];
                lakeMap.setView(latLng, 13);

                if (marker) {
                    marker.setLatLng(latLng);
                } else {
                    marker = L.marker(latLng).addTo(lakeMap);
                }

                btn.innerText = '✅ GPS Acquired!';
                setTimeout(() => {
                    btn.innerText = 'Use Current GPS Location';
                    btn.disabled = false;
                }, 2000);
            },
            (error) => {
                console.error(error);
                alert('Could not acquire GPS position. Ensure location services are enabled.');
                btn.innerText = 'Use Current GPS Location';
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }
</script>
@endsection
