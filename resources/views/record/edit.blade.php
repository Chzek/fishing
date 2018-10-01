@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Record</div>
                <div class="card-body">
                    {!! Form::model($record, ['url' => 'record', 'method' => 'put']) !!}
                        {!! Form::hidden('id') !!}

                        <div class="form-group">
                            {!! Form::label('anglers_id', 'Angler') !!}
                            {!! Form::select('anglers_id', $anglers, $record->anglers_id, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('lakes_id', 'Lake') !!}
                            {!! Form::select('lakes_id', $lakes, $record->lakes_id, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('fish_breeds_id', 'Fish') !!}
                            {!! Form::select('fish_breeds_id', $fishes, $record->fish_breeds_id, ['class' => 'form-control']) !!}
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('lures_id', 'Lure') !!}
                            {!! Form::select('lures_id', $lures, $record->lures_id, ['class' => 'form-control']) !!}
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-6">
                                {!! Form::label('caught', 'Catch Date') !!}
                                {!! Form::date('caught', $record->caught, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-6">
                                {!! Form::label('weight', 'Weight(pounds)') !!}
                                {!! Form::text('weight', $record->weight, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                {!! Form::label('length', 'Length(inches)') !!}
                                {!! Form::text('length', $record->length, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-6">
                                {!! Form::label('temperature', 'Temperature') !!}
                                {!! Form::text('temperature', $record->temperature, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <fieldset class="form-group">
                            {!! Form::label('released', 'Released') !!}
                            {!! Form::select('released', [ '0' => 'No', '1' => 'Yes'], $record->released, ['class' => 'form-control']) !!}
                        </fieldset>


                        {!! Form::submit('Save', ['class' => 'btn btn-md btn-outline-dark']) !!}
                        <a href='/record' class='btn btn-md btn-outline-dark' role='button'>Cancel</a>

                    {!! Form::close() !!}
                </div>
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
@endsection
