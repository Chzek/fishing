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

                    {{-- Filters form --}}
                    <form class="form">
                        <div class="form-group form-row justify-content-between">
                            
                            <div class="col-4 input-group">
                                <input id="name" name="name" class='form-control' type="text"
                                    @if(Request::input('name', false))
                                        value='{{ Request::input('name') }}'
                                    @endif
                                    placeholder="Name"
                                />
                            </div>

                            <div class="col-2 input-group">
                                <input id="records_count" name="records_count" class='form-control' type="number"
                                    @if(Request::input('records_count', false))
                                        value='{{ Request::input('records_count') }}'
                                    @endif
                                    placeholder="Fish"
                                />

                                <select name="records_count_operator" class='form-control'>
                                    <option value=">" {{ Request::input('records_count_operator') === ">" ? "selected" : ""}} >&gt;</option>
                                    <option value="=" {{ Request::input('records_count_operator') === "=" ? "selected" : ""}} >=</option>
                                    <option value="<" {{ Request::input('records_count_operator') === "<" ? "selected" : ""}} >&lt;</option>
                                </select>
                            </div>

                            <div class="col-1 input-group">
                                <input type="submit" class='card-link btn btn-sm btn-dark' value="Filter" />
                            </div>

                        </div>
                    </form>
                    <table class='table table-hover'>
                        <thead class='thead-light'>
                            <tr>
                                <th>
                                    @if (Request::input('sort_by') === 'name' && Request::input('sort_order') === 'desc')
                                        <a href="{{ route('lakes.index', ['sort_by' => 'name', 'sort_order' => 'asc']) }}">Name</a>    
                                    @else
                                        <a href="{{ route('lakes.index', ['sort_by' => 'name', 'sort_order' => 'desc']) }}">Name</a>
                                    @endif
                                </th>
                                <th class="text-center">Latitude</th>
                                <th class="text-center">Longitude</th>
                                <th class="text-center">
                                    @if(Request::input('sort_by') === 'records_count' && Request::input('sort_order') === 'desc')
                                        <a href="{{ route('lakes.index', ['sort_by' => 'records_count', 'sort_order' => 'asc']) }}">Fish</a>
                                    @else
                                        <a href="{{ route('lakes.index', ['sort_by' => 'records_count', 'sort_order' => 'desc']) }}">Fish</a>
                                    @endif
                                </th>
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
