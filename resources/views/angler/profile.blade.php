@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='card-title d-inline align-middle'>Angler</h5>
                </div>
                <div class="card-body">
                    <h2 class="card-subtitle mb-2 text-muted">
                        {{ $angler->firstName}} {{ $angler->middleName }} {{ $angler->lastName }}
                    </h2>
                    @if( false )
                        <div class="card-group" style="margin-bottom: 0.5em">
                            <div class="card">
                                <div class="card-body">
                                    <h1 class="text-center">{{ $count }}</h1>
                                </div>
                                <div class="card-footer text-center">
                                Fish Caught
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="text-center" style="margin-bottom: 0">{{ $longest->length }}in.</h2>
                                    <h6 class="text-muted text-center">{{ $longest->fishBreed->name }}</h6>
                                </div>
                                <div class="card-footer text-center">
                                Longest
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h1 class="text-center">{{ $crews }}</h1>
                                </div>
                                <div class="card-footer text-center">
                                Expeditions
                                </div>
                            </div>
                        </div>
                    @else
                        This angler needs to do some more fishing. <br>
                    @endif
                    @if( count($records) > 0)
                        @foreach($records as $key => $group)
                            <h4>{{ $key }}</h4>
                            <table class="table table-sm">
                                <thead class="head-light">
                                    <tr>
                                        <th>Lake</th>
                                        {{-- <th>Fish</th> --}}
                                        <th align='right'>Catches</th>
                                        <th align='center'>Length(Min/Max/Avg)</th>
                                        <th align='center'>Weight(Min/Max/Avg)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach( $group as $record)
                                        <tr>
                                            <td>{{ $record->lake->name }}</td>
                                            {{-- <td>{{ $record->fishBreed->name }}</td> --}}
                                            <td align='right'>{{ $record->catches }}</td>
                                            <td align='center'>{{ $record->min_length }}/{{ $record->max_length }}/{{ $record->avg_length }}</td>
                                            <td align='center'>
                                                @if(!is_null($record->min_weight))
                                                    {{ $record->min_weight }}/{{ $record->max_weight }}/{{ $record->avg_weight }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <hr />
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
