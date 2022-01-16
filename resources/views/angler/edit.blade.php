@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class='card-title d-inline align-middle'>Edit Angler</h5>
                    <div class="btn-group float-right" role="group" aria-label="Actions">
                        <a href='/angler' class='card-link btn btn-sm btn-dark' role='button'>Return</a>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::model($angler, ['url' => 'angler', 'method' => 'put', 'files' => 'true']) !!}
                        {!! Form::hidden('id') !!}

                        <div class="form-group">
                            {!! Form::label('firstName', 'First Name') !!}
                            {!! Form::text('firstName', $angler->firstName, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('middleName', 'Middle Name') !!}
                            {!! Form::text('middleName', $angler->middleName, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('lastName', 'Last Name') !!}
                            {!! Form::text('lastName', $angler->lastName, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-row">
                            <div class="form-group col-3">
                                {!! Form::label('user_id', 'User') !!}
                                {!! Form::select('user_id', $users, $angler->user_id,
                                    ['class' => 'form-control', 'placeholder' => 'Please select a user.']) !!}
                            </div>

                            <div class="form-group col-3">
                                {!! Form::label('birthdate', 'Birthday') !!}
                                {!! Form::date('birthdate', $angler->birthdate, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-3">
                                {!! Form::label('avatar', 'Avatar') !!}
                                {!! Form::input('file', 'avatar', '/storage/avatars/'.$angler->avatar, ['class' => 'form-control-file', 'onChange' => 'readURL(this);']) !!}
                                 <img src="{{ '/storage/avatars/'.$angler->avatar }}" id="avatar-img-tag" width="200px" />
                            </div>
                        </div>

                        <div class="btn-group" role="group" aria-label="Actions">
                            {!! Form::submit('Save', ['class' => 'btn btn-md btn-outline-dark']) !!}
                            <a href='/angler' class='btn btn-md btn-outline-dark' role='button'>Cancel</a>
                        </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#avatar-img-tag').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
@endsection