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
                    <img class="img-thumbnail mx-auto d-block m-1" src="/images/{{ $fish->image }}.jpg">

                    <a href='/fish/family/{{ $fish->family->id }}/edit' class='card-link btn btn-md btn-outline-dark' role='button'>Edit Family</a>
                    <a href='/fish/breed/{{ $fish->id }}/edit' class='card-link btn btn-md btn-outline-dark m-0' role='button'>Edit Breed</a>
                    <a href='/fish' class='card-link btn btn-md btn-outline-dark m-0' role='button'>Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
