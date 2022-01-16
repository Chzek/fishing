@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <div class="d-inline-block">
                        <h5 class='card-title'>{{ $fish->name }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $fish->family->name }}</h6>
                    </div>
                    <div class="btn-group float-right" role="group" aria-label="Fish collction actions">
                        <a href='/fish/family/{{ $fish->family->id }}/edit' class='btn btn-md btn-outline-dark' role='button'>Edit Family</a>
                        <a href='/fish/breed/{{ $fish->id }}/edit' class='btn btn-md btn-outline-dark m-0' role='button'>Edit Breed</a>
                        <a href='/fish' class='btn btn-md btn-outline-dark m-0' role='button'>Return</a>
                    </div>
                </div>
                <div class="card-body">
                    <img class="img-thumbnail mx-auto d-block m-1" style="object-fit: contain;"
                        src="/storage/fish/{{ $fish->image }}" width="100%">
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

                    <div class="card">
                        <div class="card-header">
                            Lakes
                        </div>
                        <div class="card-body">
                            <table class='table table-hover'>
                                <thead class='thead-light'>
                                    <tr>
                                        <th>Name</th>
                                        <th class="text-center">Latitude</th>
                                        <th class="text-center">Longitude</th>
                                        <th class="text-center">Fish</th>
                                        <th class="text-center">Min/Max/Avg(in)</th>
                                        <th class="text-center">Visits</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lakes as $lake)
                                        <tr>
                                            <td class="align-middle">{{ $lake->lake->name }}</td>
                                            <td class="align-middle text-center">{{ $lake->lake->latitude }}</td>
                                            <td class="align-middle text-center">{{ $lake->lake->longitude }}</td>
                                            <td class="align-middle text-center">{{ $lake->count }}</td>
                                            <td class="align-middle text-center">
                                                {{ $lake->min_length }}/{{ $lake->max_length }}/{{ $lake->avg_length }}
                                            </td>
                                            <td class="align-middle text-center">{{ $lake->visits }}</td>
                                            <td>
                                                <a href='/lake/{{ $lake->lake->id }}' class='btn btn-sm btn-light' role='button'>
                                                    <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Show"></i>
                                                </a>
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
    </div>
</div>
@endsection
