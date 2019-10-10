@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class='display-5 d-inline'>Expedition Index</h5>
                    <a href='/expedition/create' class='btn btn-sm btn-dark float-right' role='button'>Add</a>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        An <b>expedition</b> is a group of like minded anglers gathering to adventure into the wilderness in pursuit of the big one.
                    </p>
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Anglers</th>
                                <th>Fish</th>
                                <th>Posts</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expeditions as $expedition)
                                <tr>
                                    <td class="align-middle">{{ $expedition->description }}</td>
                                    <td class="align-middle">{{ $expedition->start }}</td>
                                    <td class="align-middle">{{ $expedition->finish }}</td>
                                    <td class="align-middle text-right">{{ $expedition->crews_count }}</td>
                                    <td class="align-middle text-right">{{ $expedition->records_count }}</td>
                                    <td class="align-middle text-right">{{ $expedition->posts_count }}</td>
                                    <td class="align-middle text-center">
                                        @if(view()->exists('expedition.edit'))
                                            <a href='/expedition/{{ $expedition->id }}/edit' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
                                            </a>
                                        @endif
                                        @if(view()->exists('expedition.show'))
                                            <a href='/expedition/{{ $expedition->id }}' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Show"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <caption>
                            {{ $expeditions->count() }} Total Expeditions
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
