@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <x-pageNavigation name="expedition" />
                </div>
                <div class="card-body">
                    <p class="card-text">
                        An <b>expedition</b> is a group of like minded anglers gathering to adventure into the wilderness in pursuit of the big one.
                    </p>
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Anglers</th>
                                <th>Fish</th>
                                <th>Posts</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expeditions as $expedition)
                                <tr>
                                    <td class="align-middle">{{ $expedition->description }}</td>
                                    <td class="align-middle">{{ $expedition->start }}</td>
                                    <td class="align-middle">{{ $expedition->finish }}</td>
                                    <td class="align-middle text-right">{{ $expedition->crews_count }}</td>
                                    <td class="align-middle text-right">{{ $expedition->records_count }}</td>
                                    <td class="align-middle text-right">{{ $expedition->posts_count }}</td>
                                    <td class="align-middle text-center">
                                        <x-tableOptions name='expedition'
                                            identifier='{{ $expedition->id }}'
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <caption>
                            {{ $expeditions->count() }} Total Expeditions
                        </caption>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
