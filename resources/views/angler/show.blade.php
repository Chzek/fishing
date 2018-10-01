@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='card-title'>Angler</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        {{ $angler->firstName}} {{ $angler->middleName }} {{ $angler->lastName }}
                    </h6>
                    <a href='/angler/{{ $angler->id }}/edit' class='card-link btn btn-md btn-outline-dark' role='button'>Edit</a>
                    <a href='/angler' class='card-link btn btn-md btn-outline-dark m-0' role='button'>Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
