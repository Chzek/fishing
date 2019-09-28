@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class='card-title'>
                    Lake
                        <a href='/lake/{{ $lake->id }}/edit' class='card-link btn btn-md btn-outline-dark float-right' role='button'>Edit</a>
                        <a href='/lake' class='card-link btn btn-md btn-outline-dark m-0 float-right' role='button'>Cancel</a>
                    </h3>
                    
                </div>
                <div class="card-body">
                    <h1 class="card-subtitle mb-2 text-muted">
                        {{ $lake->name}}
                    </h1>
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
                                <h1 class="text-center" style="margin-bottom: 0">{{ $longest->length }} in.</h1>
                                <h6 class="text-muted text-center">{{ $longest->fishBreed->name }}</h6>
                            </div>
                            <div class="card-footer text-center">
                            Longest
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center" style="margin-bottom: 0">{{ $fattest->weight }} lbs.</h1>
                                <h6 class="text-muted text-center">{{ $longest->fishBreed->name }}</h6>
                            </div>
                            <div class="card-footer text-center">
                            Fattest
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
