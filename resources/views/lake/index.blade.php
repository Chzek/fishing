@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <x-pageNavigation name="lake" />
                </div>
                <div class="card-body">
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Latitude</th>
                                <th class="text-center">Longitude</th>
                                <th class="text-center">Fish</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lakes as $lake)
                                <tr>
                                    <td class="align-middle">{{ $lake->name }}</td>
                                    <td class="align-middle text-center">{{ $lake->latitude }}</td>
                                    <td class="align-middle text-center">{{ $lake->longitude }}</td>
                                    <td class="align-middle text-center">{{ $lake->records_count }}</td>
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
                            ({{ $lakes->firstItem() }} to {{ $lakes->lastItem() }}) of {{ $lakes->total() }} Lakes
                            {{ $lakes->links() }}
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
