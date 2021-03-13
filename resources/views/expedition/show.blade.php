@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class='card-title'>
                        {{ $expedition->description }}
                        @if(view()->exists('expedition.edit'))
                            <a href='/expedition/{{ $expedition->id }}/edit' class='btn btn-sm btn-light' role='button'>
                                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit Expedition"></i>
                            </a>
                        @endif
                        <a href='/expedition' class='card-link btn btn-md btn-outline-dark m-0  float-right' role='button'>Return</a>
                    </h3>
                    <h5 class="card-subtitle mb-2 text-muted">{{ $expedition->start }} - {{ $expedition->finish }}</h5>
                </div>
                <div class="card-body">
                    <div class="card-block">
                        <div class="row">
                            <div class="col-lg-6 pb-2">
                                <h4>
                                    Crew
                                    @if(view()->exists('expedition.post.create'))
                                        <a href='/crew/create?expeditions_id={{ $expedition->id }}' class='btn btn-sm btn-light' role='button'>
                                            <i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Add crew member"></i>
                                        </a>
                                    @endif
                                </h4>
                                <ul>
                                    @foreach($expedition->crews as $crew)
                                        <li>{{ $crew->angler->fullName }} 
                                            @if($expedition->start != $crew->joined)
                                                ({{ $crew->joined }})
                                            @endif
                                            @if(view()->exists('expedition.crew.delete'))
                                                <a href='/crew/delete' class='btn btn-sm btn-light' role='button'>
                                                    <i class="fa fa-trash" data-toggle="tooltip" data-placement="top" title="Remove crew member"></i>
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-lg-4 offset-lg-1">
                            </div>
                        </div>
                    </div>
                    <div class="card-block">
                        <h4>
                            Posts
                            @if(view()->exists('expedition.post.create'))
                                <a href='/post/create?expeditions_id={{ $expedition->id }}' class='btn btn-sm btn-light' role='button'>
                                    <i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Add post"></i>
                                </a>
                            @endif
                        </h4>
                        @foreach($expedition->posts as $post)
                            <blockquote>
                                {{ $post->description }}
                            </blockquote>
                        @endforeach
                    </div>
                    @if(count($records) > 0)
                        <h4>
                            Records
                        </h4>
                        <table class='table table-hover'>
                            <thead class='thead-light'>
                                <tr>
                                    <th>Caught</th>
                                    <th>Angler</th>
                                    <th>Lake</th>
                                    <th>Released</th>
                                    <th>Fish</th>
                                    <th>Lure</th>
                                    <th>Weight</th>
                                    <th>Length</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                    <tr>
                                        <td>{{ $record->caught }}</td>
                                        <td>{{ $record->angler->lastName }}, {{ $record->angler->firstName }} {{ $record->angler->middleName }}</td>
                                        <td>{{ $record->lake->name }}</td>
                                        <td>
                                            @if($record->released == 1)
                                                <span class="badge badge-secondary">Released</span>
                                            @else
                                                <span class="badge badge-primary">Caught</span>
                                            @endif
                                        </td>
                                        <td>{{ $record->fishBreed->name }}</td>
                                        <td>
                                            @if($record->lure)
                                                @if(strlen($record->lure->displayName) >= 20)
                                                    <span title="{{ $record->lure->displayName }}">{{ substr($record->lure->displayName, 0, 17) }}...</span>
                                                @else
                                                    {{ $record->lure->displayName }}
                                                @endif
                                            @endif
                                        </td>
                                        <td align="right">{{ $record->weight }}</td>
                                        <td align="right">{{ $record->length }}</td>
                                        <td align="center">
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
                            <caption>
                                ({{ $records->firstItem() }} to {{ $records->lastItem() }}) of {{ $records->total() }} Records
                                {{ $records->links() }}
                            </caption>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection