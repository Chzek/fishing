@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='display-5 d-inline'>Angler Index</h5>
                    <a href='/angler/create' class='btn btn-sm btn-dark float-right' role='button'>Add</a>
                </div>
                <div class="card-body">
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Fish</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anglers as $angler)
                                <tr>
                                    <td>{{ $angler->lastName }}</td>
                                    <td>{{ $angler->firstName }}</td>
                                    <td>{{ $angler->middleName }}</td>
                                    <td align="right">{{ $angler->records_count }}</td>
                                    <td align="center">
                                        @if(view()->exists('angler.edit'))
                                            <a href='/angler/{{ $angler->id }}/edit' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
                                            </a>
                                        @endif
                                        @if(view()->exists('angler.show'))
                                            <a href='/angler/{{ $angler->id }}' class='btn btn-sm btn-light' role='button'>
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
