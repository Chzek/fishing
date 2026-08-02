@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🏞️ Log New Lake / Waterbody</h5>
                    <button type="button" id="btn-use-gps" onclick="useCurrentGPS()" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
                        📍 Use Current GPS Location
                    </button>
                </div>
                <div class="card-body">
                    {!! Form::model($lake, ['url' => 'lake']) !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Lake / Waterbody Name') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'e.g. Wawa Lake, Hawk Lake, Magpie River']) !!}
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                {!! Form::label('latitude', 'Latitude (°N)') !!}
                                {!! Form::text('latitude', null, ['class' => 'form-control', 'id' => 'input-lat', 'placeholder' => 'e.g. 48.0042']) !!}
                            </div>
                            <div class="col-md-6 form-group">
                                {!! Form::label('longitude', 'Longitude (°W)') !!}
                                {!! Form::text('longitude', null, ['class' => 'form-control', 'id' => 'input-lng', 'placeholder' => 'e.g. -84.7712']) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                {!! Form::label('structure', '🌊 Bottom Terrain / Cover') !!}
                                {!! Form::text('structure', null, ['class' => 'form-control', 'placeholder' => 'e.g. Rock/Granite, Weedline, Drop-off, Boulders']) !!}
                            </div>
                            <div class="col-md-6 form-group">
                                {!! Form::label('max_depth', '📏 Max Depth (ft)') !!}
                                {!! Form::number('max_depth', null, ['class' => 'form-control', 'placeholder' => 'e.g. 45']) !!}
                            </div>
                        </div>

                        <!-- Interactive Offline Leaflet Map Location Picker -->
                        <div class="form-group">
                            <label class="font-weight-bold">🗺️ Interactive Map Location Picker (Tap to Pin)</label>
                            <small class="text-muted d-block mb-2">Tap/click anywhere on the waterbody to drop a pin and set exact coordinates offline.</small>
                            <div id="lake-picker-map" style="height: 380px; width: 100%; border-radius: 6px;" class="border"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ url('/lake') }}" class="btn btn-outline-secondary">Cancel</a>
                            {!! Form::submit('💾 Save Lake Log', ['class' => 'btn btn-primary font-weight-bold px-4']) !!}
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
        // Default center: Wawa / Hawk Junction region
        const defaultLat = parseFloat(document.getElementById('input-lat').value) || 48.15;
        const defaultLng = parseFloat(document.getElementById('input-lng').value) || -84.85;

        lakeMap = L.map('lake-picker-map').setView([defaultLat, defaultLng], 9);

        // ESRI Topo Map Layer
        const topoLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Tiles &copy; Esri, NRCan CanVec'
        }).addTo(lakeMap);

        // ESRI Satellite Layer
        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Source: Esri, Maxar'
        });

        L.control.layers({
            "🗺️ Topo / Waterbody": topoLayer,
            "🛰️ Satellite Imagery": satLayer
        }).addTo(lakeMap);

        // Initial Marker if lat/lng present
        if (document.getElementById('input-lat').value && document.getElementById('input-lng').value) {
            marker = L.marker([defaultLat, defaultLng]).addTo(lakeMap);
        }

        // Tap/click on map to drop pin
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
