@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='display-5 d-inline'>Fish Index</h5>
                    <div class='d-inline float-right'>
                        <a href='/fish/family/create' class='btn btn-sm btn-dark' role='button'>Add Family</a>
                        <a href='/fish/breed/create' class='btn btn-sm btn-dark' role='button'>Add Breed</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Family</th>
                                <th>Name (Breed)</th>
                                <th>Caught</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fishes as $fish)
                                <tr>
                                    <td>{{ $fish->family->name }}</td>
                                    <td>{{ $fish->name }}</td>
                                    <td align="right">{{ $fish->records_count }}</td>
                                    <td align="center">
                                        @if(view()->exists('fish.breed.edit'))
                                            <a href='/fish/breed/{{ $fish->id }}/edit' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
                                            </a>
                                        @endif
                                        @if(view()->exists('fish.show'))
                                            <a href='/fish/{{ $fish->id }}' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Show"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
