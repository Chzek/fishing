@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Angler</div>
                <div class="card-body">
                    {!! Form::model($angler, ['url' => 'angler', 'method' => 'put']) !!}
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

                        {!! Form::submit('Save', ['class' => 'btn btn-md btn-outline-dark']) !!}
                        <a href='/angler' class='btn btn-md btn-outline-dark' role='button'>Cancel</a>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
