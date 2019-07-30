@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='display-5 d-inline'>Lure Index</h5>
                    <a href='/lure/create' class='btn btn-sm btn-dark float-right' role='button'>Add</a>
                </div>
                <div class="card-body">
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Size</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lures as $lure)
                                <tr>
                                    <td class="align-middle">{{ $lure->id }}</td>
                                    <td class="align-middle">{{ $lure->name }}</td>
                                    <td class="align-middle">{{ $lure->color }}</td>
                                    <td class="align-middle">{{ $lure->size }}</td>
                                    <td class="align-middle text-center">
                                        @if(view()->exists('lure.edit'))
                                            <a href='/lure/{{ $lure->id }}/edit' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
                                            </a>
                                        @endif
                                        @if(view()->exists('lure.show'))
                                            <a href='/lure/{{ $lure->id }}' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Show"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <caption>
                            {{ $lures->count() }} Total Lures
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
