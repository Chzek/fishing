@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $expedition->description }}</h3>
                    <h5 class="card-subtitle mb-2 text-muted">Add Crew Member</h5>
                </div>
                <div class="card-body">
                    {!! Form::model($crew, ['url' => 'crew']) !!}
                        {!! Form::hidden('expeditions_id', $expedition->id ) !!}

                        <div class="form-group">
                            {!! Form::label('angler', 'Angler') !!}
                            {!! Form::select('anglers_id', $anglers, null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                {!! Form::label('joined', 'Date Joined ('. $expedition->start .' through '. $expedition->finish .')') !!}
                                {!! Form::date('joined', $expedition->start, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        {!! Form::submit('Create', ['class' => 'btn btn-md btn-outline-dark']) !!}
                        <a href='/expedition/{{ $expedition->id }}' class='btn btn-md btn-outline-dark' role='button'>Return</a>

                    {!! Form::close() !!}
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
