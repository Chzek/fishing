@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <h3 class='card-title'>
                    Lake
                        <a href='/lake/{{ $lake->id }}/visits' class='card-link btn btn-md btn-outline-dark float-right' role='button'>Visits</a>
                        <a href='/lake/{{ $lake->id }}/edit' class='card-link btn btn-md btn-outline-dark float-right' role='button'>Edit</a>
                        <a href='{{ url('lake') }}' class='card-link btn btn-md btn-outline-dark m-0 float-right' role='button'>Back</a>
                    </h3>
                    
                </div>
                <div class="card-body">
                    <h1 class="card-subtitle mb-2 text-muted">
                        {{ $lake->name }}
                    </h1>

                    @if($lake->structure || $lake->max_depth || ($lake->latitude && $lake->longitude))
                        <div class="mb-3">
                            @if($lake->structure)
                                <span class="badge badge-info p-2 mr-2">🌊 Bottom Terrain: <strong>{{ $lake->structure }}</strong></span>
                            @endif
                            @if($lake->max_depth)
                                <span class="badge badge-secondary p-2 mr-2">📏 Max Depth: <strong>{{ $lake->max_depth }} ft</strong></span>
                            @endif
                            @if($lake->latitude && $lake->longitude)
                                <span class="badge badge-success p-2">📍 Coordinates: <strong>{{ $lake->latitude }}°N, {{ $lake->longitude }}°W</strong></span>
                            @endif
                        </div>
                    @endif

                    @if($lake->latitude && $lake->longitude)
                        <div class="card mb-4">
                            <div class="card-header bg-light font-weight-bold d-flex justify-content-between align-items-center">
                                <span>🗺️ Location & Topo Map</span>
                                @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
                                    <span class="badge badge-info">{{ $nearbyLakes->count() }} Nearby Lake(s) within 2 Miles</span>
                                @endif
                            </div>
                            <div class="card-body p-0">
                                <div id="lake-show-map" style="height: 350px; width: 100%; border-radius: 4px;"></div>
                            </div>
                            @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
                                <div class="card-footer bg-light">
                                    <small class="font-weight-bold text-dark d-block mb-1">🌲 Identified Lakes Within 2 Miles:</small>
                                    @foreach($nearbyLakes as $nearLake)
                                        <a href="{{ url('/lake/' . $nearLake->id) }}" class="badge badge-pill badge-outline-primary border p-2 mr-1 mb-1 text-dark">
                                            🏞️ <strong>{{ $nearLake->name }}</strong> ({{ $nearLake->distance }} mi)
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="card-group" style="margin-bottom: 0.5em">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $count }}</h1>
                                <h6 class="text-muted text-center">{{ $visits }} Visits</h6>
                                <h6 class="text-muted text-center">{{ $anglers }} Anglers</h6>
                            </div>
                            <div class="card-footer text-center">
                            Total
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                @isset($longest)
                                    <h1 class="text-center" style="margin-bottom: 0">{{ $longest->length }} in.</h1>
                                    <h6 class="text-muted text-center">{{ $longest->fishBreed->name }}</h6>
                                @else
                                    <h1 class="text-center" style="margin-bottom: 0">--</h1>
                                    <h6 class="text-muted text-center">Please record more fish!</h6>
                                @endisset
                            </div>
                            <div class="card-footer text-center">
                            Longest
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                @if(isset($fattest) && !is_null($fattest->weight))
                                    <h1 class="text-center" style="margin-bottom: 0">{{ $fattest->weight }} lbs.</h1>
                                    <h6 class="text-muted text-center">{{ $fattest->fishBreed->name }}</h6>
                                @else
                                    <h1 class="text-center" style="margin-bottom: 0">--</h1>
                                    <h6 class="text-muted text-center">Please record more fish!</h6>
                                @endif
                            </div>
                            <div class="card-footer text-center">
                            Fattest
                            </div>
                        </div>
                    </div>

                    @foreach($stats as $stat)
                        <div class="card-body">
                            <h4 class="card-subtitle mb-2 text-muted">
                                {{ $stat->fishBreed->name}}
                            </h4>
                            <div class="card-group" style="margin-bottom: 0.5em">
                                <div class="card">
                                    <div class="card-body">
                                        <h1 class="text-center">{{ $stat->cnt }}</h1>
                                    </div>
                                    <div class="card-footer text-center">
                                    Total
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h1 class="text-center">{{ $stat->avg_length }} in.</h1>
                                        <h6 class="text-muted text-center">{{ $stat->min_length }}/{{ $stat->max_length }} in. (Min/Max)</h6>
                                    </div>
                                    <div class="card-footer text-center">
                                    Avg. Length
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        @if(!is_null($stat->avg_weight))
                                            <h1 class="text-center">{{ $stat->avg_weight }} lbs.</h1>
                                            <h6 class="text-muted text-center">{{ $stat->weighed_count }}/{{ $stat->min_weight }}/{{ $stat->max_weight }} (Cnt/Min/Max)</h6>
                                        @else
                                            <h1 class="text-center">--</h1>
                                            <h6 class="text-muted text-center">Please record more fish!</h6>
                                        @endif
                                    </div>
                                    <div class="card-footer text-center">
                                    *Avg. Weight
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    <i>*Average Weight of larger fish.</i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($lake->latitude && $lake->longitude)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $lake->latitude }};
        const lng = {{ $lake->longitude }};

        const map = L.map('lake-show-map').setView([lat, lng], 12);

        const topoLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Tiles &copy; Esri, NRCan CanVec'
        }).addTo(map);

        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 15,
            attribution: 'Source: Esri, Maxar'
        });

        L.control.layers({
            "🗺️ Topo / Waterbody": topoLayer,
            "🛰️ Satellite Imagery": satLayer
        }).addTo(map);

        // Draw 2-mile radius circle around lake (3,218.68 meters = 2 miles)
        L.circle([lat, lng], {
            color: '#17a2b8',
            fillColor: '#17a2b8',
            fillOpacity: 0.08,
            radius: 3218.68
        }).addTo(map);

        // Target Lake Marker (Blue)
        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>{{ $lake->name }}</b><br>Coordinates: " + lat + ", " + lng)
            .openPopup();

        // Render Nearby Lakes within 2 miles (Green Markers)
        @if(isset($nearbyLakes) && $nearbyLakes->count() > 0)
            const nearbyLakesData = @json($nearbyLakes);

            nearbyLakesData.forEach(function (nLake) {
                if (nLake.latitude && nLake.longitude) {
                    // Custom green circle marker for nearby identified lakes
                    const nMarker = L.circleMarker([nLake.latitude, nLake.longitude], {
                        radius: 8,
                        fillColor: "#28a745",
                        color: "#ffffff",
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(map);

                    nMarker.bindPopup(
                        "<b>🏞️ " + nLake.name + "</b><br>" +
                        "📍 " + nLake.distance + " miles away<br>" +
                        "<a href='/lake/" + nLake.id + "' class='btn btn-sm btn-outline-success mt-1'>View Lake</a>"
                    );
                }
            });
        @endif
    });
</script>
@endif
@endsection

