@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='card-title d-inline align-middle'>Create Angler</h5>
                    <div class="btn-group float-right" role="group" aria-label="Actions">
                        <a href='/angler' class='card-link btn btn-sm btn-dark' role='button'>Return</a>
                    </div>
                </div>
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

                        <div class="form-row">
                            <div class="form-group">
                                {!! Form::label('user_id', 'User') !!}
                                {!! Form::select('user_id', $users, null,
                                    ['class' => 'form-control', 'placeholder' => 'Please select a user.' ]) !!}
                            </div>

                            <div class="form-group col-6">
                                {!! Form::label('birthdate', 'Birthday') !!}
                                {!! Form::date('birthdate', null, ['class' => 'form-control']) !!}
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
