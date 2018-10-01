@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Lure</div>
                <div class="card-body">
                    {!! Form::model($lure, ['url' => 'lure', 'method' => 'put']) !!}
                        {!! Form::hidden('id') !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', $lure->name, ['class' => 'form-control', 'list' => 'nameList']) !!}

                            <datalist id="nameList">
                                @foreach($lureNames as $lure)
                                    <option value="{{ $lure->name }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="form-group">
                            {!! Form::label('color', 'Color') !!}
                            {!! Form::text('color', $lure->color, ['class' => 'form-control', 'list' => 'colorList']) !!}

                            <datalist id="colorList">
                                @foreach($lureColors as $lure)
                                    <option value="{{ $lure->color }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="form-group">
                            {!! Form::label('size', 'Size') !!}
                            {!! Form::text('size', $lure->size, ['class' => 'form-control', 'list' => 'sizeList']) !!}

                            <datalist id="sizeList">
                                @foreach($lureSizes as $lure)
                                    <option value="{{ $lure->size }}">
                                @endforeach
                            </datalist>
                        </div>

                        {!! Form::submit('Save', ['class' => 'btn btn-md btn-outline-dark']) !!}
                        <a href='/lure' class='btn btn-md btn-outline-dark' role='button'>Cancel</a>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
