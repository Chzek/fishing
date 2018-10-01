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
                            {{ $record->temperature }}
                        </p>
                    @endif

                    @if($record->lure)
                        <p class="card-text">
                            {{ $record->lure->displayName }}
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
