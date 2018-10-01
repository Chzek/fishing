@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Lure</div>
                <div class="card-body">
                    {!! Form::model($lure, ['url' => 'lure']) !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('color', 'Color') !!}
                            {!! Form::text('color', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('size', 'Size') !!}
                            {!! Form::text('size', null, ['class' => 'form-control']) !!}
                        </div>

                        {!! Form::submit('Create', ['class' => 'btn btn-md btn-outline-dark']) !!}

                    {!! Form::close() !!}

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
</div>
@endsection
