@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='display-5 d-inline'>Lake Index</h5>
                    <a href='/lake/create' class='btn btn-sm btn-dark float-right' role='button'>Add</a>
                </div>
                <div class="card-body">
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Name</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Fish</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lakes as $lake)
                                <tr>
                                    <td class="align-middle">{{ $lake->name }}</td>
                                    <td class="align-middle text-right">{{ $lake->latitude }}</td>
                                    <td class="align-middle text-right">{{ $lake->longitude }}</td>
                                    <td class="align-middle text-right">{{ $lake->records_count }}</td>
                                    <td class="align-middle text-center">
                                        @if(view()->exists('lake.edit'))
                                            <a href='/lake/{{ $lake->id }}/edit' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
                                            </a>
                                        @endif
                                        @if(view()->exists('lake.show'))
                                            <a href='/lake/{{ $lake->id }}' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Show"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <caption>
                            {{ $lakes->count() }} Total Lakes
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
