@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='card-title'>Lake</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        {{ $lake->name}}
                    </h6>
                    <a href='/lake/{{ $lake->id }}/edit' class='card-link btn btn-md btn-outline-dark' role='button'>Edit</a>
                    <a href='/lake' class='card-link btn btn-md btn-outline-dark m-0' role='button'>Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
