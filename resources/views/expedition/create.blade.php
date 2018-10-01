@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Expedition</div>
                <div class="card-body">
                    {!! Form::model($expedition, ['url' => 'expedition']) !!}

                        <div class="form-group">
                            {!! Form::label('description', 'Description') !!}
                            {!! Form::text('description', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                {!! Form::label('start', 'Start Date') !!}
                                {!! Form::date('start', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-6">
                                {!! Form::label('finish', 'Finish Date') !!}
                                {!! Form::date('finish', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        {!! Form::submit('Create', ['class' => 'btn btn-md btn-outline-dark']) !!}

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
