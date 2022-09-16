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
                                <th class="text-center">Visits</th>
                                <th class="text-center">Fish/Visit</th>
                                <th class="text-center">Anglers</th>
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
                                    <td class="align-middle text-center">{{ $lake->visits }}</td>
                                    <td class="align-middle text-center">@if($lake->visits > 0) {{ round($lake->records_count/$lake->visits, 2) }} @endif</td>
                                    <td class="align-middle text-center">{{ $lake->anglers_count }}</td>
                                    <td class="align-middle text-center">
                                        <x-tableOptions name='lake'
                                            identifier='{{ $lake->id }}'
                                        />
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
