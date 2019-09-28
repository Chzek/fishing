@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-inline-block">
                        <h5 class='card-title'>{{ $fish->name }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $fish->family->name }}</h6>
                    </div>
                </div>
                <div class="card-body">
                    <img class="img-thumbnail mx-auto d-block m-1" style="object-fit: contain;"
                        src="/images/{{ $fish->image }}.jpg" width="100%">
                    <div class="card-group" style="margin-bottom: 0.5em">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $count }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Total
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center" style="margin-bottom: 0">{{ $longest }} in.</h1>
                            </div>
                            <div class="card-footer text-center">
                            Longest
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $fattest }} lbs.</h1>
                            </div>
                            <div class="card-footer text-center">
                            Fattest
                            </div>
                        </div>
                    </div>

                    <a href='/fish/family/{{ $fish->family->id }}/edit' class='card-link btn btn-md btn-outline-dark' role='button'>Edit Family</a>
                    <a href='/fish/breed/{{ $fish->id }}/edit' class='card-link btn btn-md btn-outline-dark m-0' role='button'>Edit Breed</a>
                    <a href='/fish' class='card-link btn btn-md btn-outline-dark m-0' role='button'>Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
