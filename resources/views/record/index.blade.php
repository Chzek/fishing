@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class='display-5 d-inline'>Record Index</h5>
                    <a href='/record/create' class='btn btn-sm btn-dark float-right' role='button'>Add</a>
                </div>
                <div class="card-body">
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Caught</th>
                                <th>Angler</th>
                                <th>Lake</th>
                                <th>Fish</th>
                                <th>Lure</th>
                                <th>Weight</th>
                                <th>Length</th>
                                <th>Temperature</th>
                                <th>Released</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->caught }}</td>
                                    <td>{{ $record->angler->lastName }}, {{ $record->angler->firstName }} {{ $record->angler->middleName }}</td>
                                    <td>{{ $record->lake->name }}</td>
                                    <td>{{ $record->fishBreed->name }}</td>
                                    <td>
                                        @if($record->lure)
                                            @if(strlen($record->lure->displayName) >= 20)
                                                <span title="{{ $record->lure->displayName }}">{{ substr($record->lure->displayName, 0, 17) }}...</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td align="right">{{ $record->weight }}</td>
                                    <td align="right">{{ $record->length }}</td>
                                    <td align="right">{{ $record->temperature }}</td>
                                    <td>
                                        @if($record->released == 1)
                                            <span class="badge badge-secondary">Released</span>
                                        @else
                                            <span class="badge badge-primary">Caught</span>
                                        @endif
                                    </td>
                                    <td align="center">
                                        @if(view()->exists('record.edit'))
                                            <a href='/record/{{ $record->id }}/edit' class='btn btn-sm btn-light' role='button'>
                                                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
                                            </a>
                                        @endif
                                        @if(view()->exists('record.show'))
                                            <a href='/record/{{ $record->id }}' class='btn btn-sm btn-light' role='button'>
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
