@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='card-title d-inline align-middle'>Angler</h5>
                    <div class="btn-group float-right" role="group" aria-label="Basic example">
                        @if(view()->exists('angler.edit') && Auth::id() == $angler->id)
                            <a href='/angler/{{ $angler->id }}/edit' class='card-link btn btn-sm btn-dark' role='button'>Edit</a>
                        @endif
                        <a href='/angler' class='card-link btn btn-sm btn-dark' role='button'>Return</a>
                    </div>
                </div>
                <div class="card-body">
                    <h2 class="card-subtitle mb-2 text-muted">
                        {{ $angler->firstName}} {{ $angler->middleName }} {{ $angler->lastName }}
                    </h2>
                    @if( $count > 0 )
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
                        <table class="table table-sm">
                            <thead class="head-light">
                                <tr>
                                    <th>Fish Breed</th>
                                    <th class="text-center">Length (in)</th>
                                    <th class="text-center">Weight (lbs)</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $records as $record)
                                    <tr>
                                        <td>{{ $record->fishBreed->name }}</td>
                                        <td class="text-center">{{ number_format($record->length, 2) }}</td>
                                        <td class="text-center">{{ $record->weight }}</td>
                                        <td>{{ $record->caught }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <caption>Last 10 catches.</caption>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
