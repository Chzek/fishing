@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Lake</div>
                <div class="card-body">
                    {!! Form::model($lake, ['url' => 'lake', 'method' => 'put']) !!}
                        {!! Form::hidden('id') !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', $lake->name, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('latitude', 'Latitude') !!}
                            {!! Form::text('latitude', $lake->latitude, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('longitude', 'Longitude') !!}
                            {!! Form::text('longitude', $lake->longitude, ['class' => 'form-control']) !!}
                        </div>

                        {!! Form::submit('Save', ['class' => 'btn btn-md btn-outline-dark']) !!}
                        <a href='/lake' class='btn btn-md btn-outline-dark' role='button'>Cancel</a>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
