@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Angler</div>
                <div class="card-body">
                    {!! Form::model($angler, ['url' => 'angler']) !!}

                        <div class="form-group">
                            {!! Form::label('firstName', 'First Name') !!}
                            {!! Form::text('firstName', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('middleName', 'Middle Name') !!}
                            {!! Form::text('middleName', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('lastName', 'Last Name') !!}
                            {!! Form::text('lastName', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('user_id', 'User') !!}
                            {!! Form::select('user_id', $users, null,
                                ['class' => 'form-control', 'placeholder' => 'Please select a user.' ]) !!}
                        </div>

                        {!! Form::submit('Create', ['class' => 'btn btn-md btn-outline-dark']) !!}

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
