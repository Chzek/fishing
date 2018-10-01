@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Record</div>
                <div class="card-body">
                    {!! Form::model($record, ['url' => 'record']) !!}

                        <div class="form-group">
                            {!! Form::label('anglers_id', 'Angler') !!}
                            {!! Form::select('anglers_id', $anglers, null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('lakes_id', 'Lake') !!}
                            {!! Form::select('lakes_id', $lakes, null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('fish_breeds_id', 'Fish') !!}
                            {!! Form::select('fish_breeds_id', $fishes, null, ['class' => 'form-control']) !!}
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('lures_id', 'Lure') !!}
                            {!! Form::select('lures_id', $lures, null, ['class' => 'form-control']) !!}
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-6">
                                {!! Form::label('caught', 'Catch Date') !!}
                                {!! Form::date('caught', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-6">
                                {!! Form::label('weight', 'Weight(pounds)') !!}
                                {!! Form::text('weight', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                {!! Form::label('length', 'Length(inches)') !!}
                                {!! Form::text('length', null, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-6">
                                {!! Form::label('temperature', 'Temperature') !!}
                                {!! Form::text('temperature', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <fieldset class="form-group">
                            {!! Form::label('released', 'Released') !!}
                            {!! Form::select('released', [ '0' => 'No', '1' => 'Yes'], '0', ['class' => 'form-control']) !!}
                        </fieldset>


                        {!! Form::submit('Create', ['class' => 'btn btn-md btn-outline-dark']) !!}

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
