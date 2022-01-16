@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $lake->name }} Visits
                        <a href='/lake/{{ $lake->id }}' class='card-link btn btn-md btn-outline-dark m-0 float-right' role='button'>Back</a>
                    </h3>
                </div>

                @foreach($recordsByDate as $records)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                Visit: {{ $records[0]->caught }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class='table table-hover'>
                                <thead class='thead-light'>
                                    <tr>
                                        <th>Angler</th>
                                        <th>Fish</th>
                                        <th>Lure</th>
                                        <th class="text-center">Weight (lb)</th>
                                        <th class="text-center">Length (in)</th>
                                        <th class="text-center">Temp.</th>
                                        <th>Released</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $record)
                                        <tr>
                                            <td class="align-middle">{{ $record->angler->lastName }}, {{ $record->angler->firstName }} {{ $record->angler->middleName }}</td>
                                            <td class="align-middle">{{ $record->fishBreed->name }}</td>
                                            <td class="align-middle">
                                                @if($record->lure)
                                                    @if(strlen($record->lure->displayName) >= 20)
                                                        <span title="{{ $record->lure->displayName }}">{{ substr($record->lure->displayName, 0, 17) }}...</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">{{ $record->weight }}</td>
                                            <td class="align-middle text-center">{{ $record->length }}</td>
                                            <td class="align-middle text-center">{{ $record->temperature }}</td>
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
                            </table>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endsection
