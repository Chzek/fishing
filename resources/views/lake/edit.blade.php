@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">✏️ Edit Lake: {{ $lake->name }}</h5>
                    <button type="button" id="btn-use-gps" onclick="useCurrentGPS()" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
                        📍 Use Current GPS Location
                    </button>
                </div>
                <div class="card-body">
                    {!! Form::model($lake, ['url' => 'lake', 'method' => 'put']) !!}
                        {!! Form::hidden('id') !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Lake / Waterbody Name') !!}
                            {!! Form::text('name', $lake->name, ['class' => 'form-control', 'required' => true]) !!}
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                {!! Form::label('latitude', 'Latitude (°N)') !!}
                                {!! Form::text('latitude', $lake->latitude, ['class' => 'form-control', 'id' => 'input-lat']) !!}
                            </div>
                            <div class="col-md-6 form-group">
                                {!! Form::label('longitude', 'Longitude (°W)') !!}
                                {!! Form::text('longitude', $lake->longitude, ['class' => 'form-control', 'id' => 'input-lng']) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                {!! Form::label('structure', '🌊 Bottom Terrain / Cover') !!}
                                {!! Form::text('structure', $lake->structure, ['class' => 'form-control', 'placeholder' => 'e.g. Rock/Granite, Weedline, Drop-off']) !!}
                            </div>
                            <div class="col-md-6 form-group">
                                {!! Form::label('max_depth', '📏 Max Depth (ft)') !!}
                                {!! Form::number('max_depth', $lake->max_depth, ['class' => 'form-control', 'placeholder' => 'e.g. 45']) !!}
                            </div>
                        </div>

                        <!-- Interactive Offline Leaflet Map Location Picker -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">🗺️ Interactive Map Location Picker (Tap to Pin)</label>
                            <small class="text-muted d-block mb-2">Tap/click anywhere on the waterbody to update pin coordinates offline.</small>
                            <div id="lake-picker-map" style="height: 380px; width: 100%; border-radius: 6px;" class="border"></div>
                        </div>

                        @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
                            <div class="alert alert-warning border-warning mb-3">
                                <strong>⚠️ Identified Lakes Within 2 Miles ({{ $nearbyLakes->count() }}):</strong>
                                <small class="d-block mb-2 text-dark">Check existing lakes in this area before renaming to prevent duplicate entries:</small>
                                <div>
                                    @foreach($nearbyLakes as $nearLake)
                                        <a href="{{ url('/lake/' . $nearLake->id) }}" target="_blank" class="badge badge-pill badge-light border border-dark p-2 mr-1 mb-1 text-dark">
                                            🏞️ <strong>{{ $nearLake->name }}</strong> ({{ $nearLake->distance }} mi away) ↗
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ url('/lake/' . $lake->id) }}" class="btn btn-outline-secondary">Cancel</a>
                            {!! Form::submit('💾 Save Changes', ['class' => 'btn btn-info text-white font-weight-bold px-4']) !!}
                        </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
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

            // Draw 2-mile radius circle around lake being edited (3,218.68 meters = 2 miles)
            L.circle([defaultLat, defaultLng], {
                color: '#17a2b8',
                fillColor: '#17a2b8',
                fillOpacity: 0.08,
                radius: 3218.68
            }).addTo(lakeMap);
        }

        // Render Nearby Lakes within 2 miles (Green Markers)
        @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
            const nearbyLakesData = @json($nearbyLakes);

            nearbyLakesData.forEach(function (nLake) {
                if (nLake.latitude && nLake.longitude) {
                    const nMarker = L.circleMarker([nLake.latitude, nLake.longitude], {
                        radius: 8,
                        fillColor: "#28a745",
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
        btn.innerText = '📡 Querying Hardware GPS...';
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
                    btn.innerText = '📍 Use Current GPS Location';
                    btn.disabled = false;
                }, 2000);
            },
            (error) => {
                console.error(error);
                alert('Could not acquire GPS position. Ensure location services are enabled on your device.');
                btn.innerText = '📍 Use Current GPS Location';
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }
</script>
@endsection
