@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <x-pageNavigation name="lure" />
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
                                        <x-tableOptions name='lure'
                                            identifier='{{ $lure->id }}'
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <caption>
                            ({{ $lures->firstItem() }} to {{ $lures->lastItem() }}) of {{ $lures->total() }} Lures
                            {{ $lures->links() }}
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
