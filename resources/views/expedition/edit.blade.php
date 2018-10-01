@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Expedition</div>
                <div class="card-body">
                    {!! Form::model($expedition, ['url' => 'expedition', 'method' => 'put']) !!}
                        {!! Form::hidden('id') !!}

                        <div class="form-group">
                            {!! Form::label('description', 'Description') !!}
                            {!! Form::text('description', $expedition->description, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                {!! Form::label('start', 'Start Date') !!}
                                {!! Form::date('start', $expedition->start, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-6">
                                {!! Form::label('finish', 'Finish Date') !!}
                                {!! Form::date('finish', $expedition->finish, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        {!! Form::submit('Save', ['class' => 'btn btn-md btn-outline-dark']) !!}
                        <a href='/expedition' class='btn btn-md btn-outline-dark' role='button'>Cancel</a>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
