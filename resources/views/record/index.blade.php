@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <x-pageNavigation name="record" />
                </div>
                <div class="card-body">

                    {{-- Filters form --}}
                    <form class="form">
                        <div class="form-group form-row justify-content-between">
                            
                            {{-- FilterByAngler --}}
                            {{-- <div class="col-4 input-group">
                                <input id="angler" name="angler" class='form-control' type="text"
                                    @if(Request::input('angler', false))
                                        value='{{ Request::input('angler') }}'
                                    @endif
                                    placeholder="Angler"
                                />
                            </div> --}}

                            {{-- FilterByLength --}}
                            <div class="col-2 input-group">
                                <input id="length" name="length" class='form-control' type="number"
                                    @if(Request::input('length', false))
                                        value='{{ Request::input('length') }}'
                                    @endif
                                    placeholder="Length"
                                />

                                <select name="length_operator" class='form-control'>
                                    <option value=">" {{ Request::input('length_operator') === ">" ? "selected" : ""}} >&gt;</option>
                                    <option value="=" {{ Request::input('length_operator') === "=" ? "selected" : ""}} >=</option>
                                    <option value="<" {{ Request::input('length_operator') === "<" ? "selected" : ""}} >&lt;</option>
                                </select>
                            </div>

                            <div class="col-1 input-group">
                                <input type="submit" class='card-link btn btn-sm btn-dark' value="Filter" />
                            </div>

                        </div>
                    </form>

                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Caught</th>
                                <th>Angler</th>
                                <th>Lake</th>
                                <th>Fish</th>
                                <th>Lure</th>
                                <th class="text-center">Weight (lb)</th>
                                <th class="text-center">Length (in)</th>
                                <th class="text-center" title="Water Temperature logged on boat">🌊 Water Temp</th>
                                <th class="text-center">🌤️ Weather</th>
                                <th>Released</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td class="align-middle">{{ $record->caught }}</td>
                                    <td class="align-middle">{{ $record->angler->full_name }}</td>
                                    <td class="align-middle">{{ $record->lake->name }}</td>
                                    <td class="align-middle">{{ $record->fishBreed->name }}</td>
                                    <td class="align-middle">
                                        @if($record->lure)
                                            @if(strlen($record->lure->displayName) >= 20)
                                                <span title="{{ $record->lure->displayName }}">{{ substr($record->lure->displayName, 0, 17) }}...</span>
                                            @else
                                                {{ $record->lure->displayName }}
                                            @endif
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">{{ $record->weight }}</td>
                                    <td class="align-middle text-center">{{ $record->length }}</td>
                                    <td class="align-middle text-center">{{ $record->temperature ? $record->temperature . '°F' : '-' }}</td>
                                    <td class="align-middle text-center">
                                        @if($record->dailyWeather)
                                            <span class="badge badge-light border px-2 py-1 text-wrap" style="font-size: 0.8rem; cursor: pointer;" title="🌤️ {{ $record->lake->name }} ({{ $record->caught }})" data-toggle="popover" data-trigger="hover focus" data-html="true" data-content="<strong>Condition:</strong> {{ $record->dailyWeather->weather_condition }}<br><strong>Air Temp:</strong> {{ $record->dailyWeather->air_temp_min }}°F – {{ $record->dailyWeather->air_temp_max }}°F<br><strong>Pressure:</strong> {{ $record->dailyWeather->barometric_pressure }} hPa<br><strong>Wind:</strong> {{ $record->dailyWeather->wind_speed_max }} mph ({{ $record->dailyWeather->wind_direction_text }})">
                                                {{ $record->dailyWeather->weather_condition }}
                                                <small class="d-block text-muted">{{ $record->dailyWeather->air_temp_mean }}°F | {{ $record->dailyWeather->barometric_pressure }}hPa</small>
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($record->released == 1)
                                            <span class="badge badge-secondary">Released</span>
                                        @else
                                            <span class="badge badge-primary">Caught</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <x-tableOptions name='record'
                                            identifier='{{ $record->id }}'
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <caption>
                            ({{ $records->firstItem() }} to {{ $records->lastItem() }}) of {{ $records->total() }} Records
                            {{ $records->links() }}
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
