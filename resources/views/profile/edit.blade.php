@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Profile</div>

                <div class="card-body">
                    {!! Form::model($user, ['url' => 'profile', 'method' => 'put', 'files' => 'true']) !!}
                        {!! Form::hidden('id') !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', $user->name, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('email', 'Email') !!}
                            {!! Form::text('email', $user->email, ['class' => 'form-control']) !!}
                        </div>

                        <div class="btn-group" role="group" aria-label="Actions">
                            {!! Form::submit('Save', ['class' => 'btn btn-md btn-outline-dark']) !!}
                            <a href='/profile' class='btn btn-md btn-outline-dark' role='button'>Cancel</a>
                        </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
