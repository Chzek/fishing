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
                                        <td class="align-middle text-center">
                                            <div class="btn-group float-right" role="group" aria-label="Angler actions">
                                                @if(view()->exists('angler.edit') && Auth::id() == $angler->id)
                                                    <a href='/angler/{{ $angler->id }}/edit' class='btn btn-sm btn-light' role='button'>
                                                        <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
                                                    </a>
                                                @endif
                                                @if(view()->exists('angler.show'))
                                                    <a href='/angler/{{ $angler->id }}' class='btn btn-sm btn-light' role='button'>
                                                        <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Show"></i>
                                                    </a>
                                                @endif
                                            </div>
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
