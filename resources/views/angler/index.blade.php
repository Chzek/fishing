@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <x-pageNavigation name="angler" />
                </div>
                <div class="card-body">
                    @if(count($anglers) > 0)
                        <table class='table table-hover'>
                            <thead class='thead-light'>
                                <tr>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Middle Name</th>
                                    <th class="text-center">Fish</th>
                                    <th class="text-center">Lakes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anglers as $angler)
                                    <tr>
                                        <td class="align-middle">{{ $angler->lastName }}</td>
                                        <td class="align-middle">{{ $angler->firstName }}</td>
                                        <td class="align-middle">{{ $angler->middleName }}</td>
                                        <td class="align-middle text-center">{{ $angler->records_count }}</td>
                                        <td class="align-middle text-center">{{ $angler->lakes_count }}</td>
                                        <td class="align-middle text-center">
                                            <x-tableOptions name='angler'
                                                identifier='{{ $angler->id }}'
                                                user='{{ $angler->user_id }}'
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <caption>
                                ({{ $anglers->firstItem() }} to {{ $anglers->lastItem() }}) of {{ $anglers->total() }} Anglers
                                {{ $anglers->links() }}
                            </caption>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
