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
                                    <td class="align-middle">{{ $record->caught }}</td>
                                    <td class="align-middle">{{ $record->angler->lastName }}, {{ $record->angler->firstName }} {{ $record->angler->middleName }}</td>
                                    <td class="align-middle">{{ $record->lake->name }}</td>
                                    <td class="align-middle">{{ $record->fishBreed->name }}</td>
                                    <td class="align-middle">
                                        @if($record->lure)
                                            @if(strlen($record->lure->displayName) >= 20)
                                                <span title="{{ $record->lure->displayName }}">{{ substr($record->lure->displayName, 0, 17) }}...</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="align-middle text-right">{{ $record->weight }}</td>
                                    <td class="align-middle text-right">{{ $record->length }}</td>
                                    <td class="align-middle text-right">{{ $record->temperature }}</td>
                                    <td class="align-middle">
                                        @if($record->released == 1)
                                            <span class="badge badge-secondary">Released</span>
                                        @else
                                            <span class="badge badge-primary">Caught</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
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
                        <caption>
                            {{ $records->count() }} Total Records
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
