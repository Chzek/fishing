@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='display-5 d-inline'>Fish Index</h5>
                    <div class="btn-group float-right" role="group" aria-label="Fish collction actions">
                        <a href='/fish/family/create' class='btn btn-sm btn-dark' role='button'>Add Family</a>
                        <a href='/fish/breed/create' class='btn btn-sm btn-dark' role='button'>Add Breed</a>
                        <a href='/fish' class='btn btn-sm btn-dark' role='button'>Return</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Family</th>
                                <th>Name (Breed)</th>
                                <th class="text-center">Caught</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fishes as $fish)
                                <tr>
                                    <td class="align-middle">{{ $fish->family->name }}</td>
                                    <td class="align-middle">{{ $fish->name }}</td>
                                    <td class="align-middle text-center">{{ $fish->records_count }}</td>
                                    <td class="align-middle text-center">
                                        <x-tableOptions name='fish'
                                            identifier='{{ $fish->id }}'
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <caption>
                            ({{ $fishes->firstItem() }} to {{ $fishes->lastItem() }}) of {{ $fishes->total() }} Fish
                            {{ $fishes->links() }}
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
