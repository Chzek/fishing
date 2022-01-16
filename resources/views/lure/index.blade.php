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
