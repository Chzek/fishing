@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $expedition->description }}</h3>
                    <h5 class="card-subtitle mb-2 text-muted">Post about your trip.</h5>
                </div>
                <div class="card-body">
                    {!! Form::model($post, ['url' => 'post']) !!}
                        {!! Form::hidden('expeditions_id', $expedition->id ) !!}

                        <div class="form-group">
                            {!! Form::label('description', 'Description') !!}
                            {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group col-6">
                            {!! Form::label('date', 'Date ('. $expedition->start .' through '. $expedition->finish .')') !!}
                            {!! Form::date('date', $expedition->start, ['class' => 'form-control']) !!}
                        </div>
                        
                        {!! Form::submit('Create', ['class' => 'btn btn-md btn-outline-dark']) !!}
                        <a href='/expedition/{{ $expedition->id }}' class='btn btn-md btn-outline-dark' role='button'>Return</a>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
