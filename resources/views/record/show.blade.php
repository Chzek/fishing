@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class='card-title'>
                        {{ $record->length }}in. 
                        @if($record->weight)
                            {{ $record->weight }}lbs. 
                        @endif
                        {{ $record->fishBreed->name }} ({{ $record->fishBreed->family->name }})
                    </h3>
                    <h5 class="card-subtitle mb-2 text-muted">
                        @if($record->released)
                            Released
                        @else
                            Caught
                        @endif
                        ({{ $record->caught }})
                    </h5>
                </div>
                <div class="card-body">
                    <p class='card-text'>
                        {{ $record->angler->fullName }}
                    </p>

                    <p class='card-text'>
                        {{ $record->lake->name }}
                    </p>

                    @if($record->temperature)
                        <p class='card-text'>
                            <strong>🌊 Water Temperature (Boat):</strong> {{ $record->temperature }}°F
                        </p>
                    @endif

                    @if($record->dailyWeather)
                        <div class="card bg-light my-3 border-info">
                            <div class="card-header bg-info text-white font-weight-bold py-2">
                                🌤️ Daily Environmental Telemetry ({{ $record->caught }})
                            </div>
                            <div class="card-body py-2">
                                <div class="row text-center">
                                    <div class="col-md-3 col-6 my-1">
                                        <small class="text-muted d-block">Condition</small>
                                        <strong>{{ $record->dailyWeather->weather_condition }}</strong>
                                    </div>
                                    <div class="col-md-3 col-6 my-1">
                                        <small class="text-muted d-block">Air Temp</small>
                                        <strong>{{ $record->dailyWeather->air_temp_min }}°F – {{ $record->dailyWeather->air_temp_max }}°F</strong>
                                    </div>
                                    <div class="col-md-3 col-6 my-1">
                                        <small class="text-muted d-block">Barometric Pressure</small>
                                        <strong>{{ $record->dailyWeather->barometric_pressure }} hPa</strong>
                                    </div>
                                    <div class="col-md-3 col-6 my-1">
                                        <small class="text-muted d-block">Wind</small>
                                        <strong>{{ $record->dailyWeather->wind_speed_max }} mph ({{ $record->dailyWeather->wind_direction_text }})</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($record->lure)
                        <p class="card-text">
                            <strong>Lure:</strong> {{ $record->lure->displayName }}
                        </p>
                    @endif
                    
                    <a href='/record/{{ $record->id }}/edit' class='card-link btn btn-md btn-outline-dark' role='button'>Edit</a>
                    <a href='/record' class='card-link btn btn-md btn-outline-dark m-0' role='button'>Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
