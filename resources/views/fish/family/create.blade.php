@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Fish Family</div>
                <div class="card-body">
                    {!! Form::model($family, ['url' => 'fish/family']) !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'list' => 'nameList']) !!}

                            <datalist id="nameList">
                                @foreach($families as $fam)
                                    <option value="{{ $fam->name }}">
                                @endforeach
                            </datalist>
                        </div>

                        {!! Form::submit('Create', ['class' => 'btn btn-md btn-outline-dark']) !!}
                        <a href='/fish' class='card-link btn btn-md btn-outline-dark m-0' role='button'>Cancel</a>

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
