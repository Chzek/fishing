@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Breed</div>
                <div class="card-body">
                    {!! Form::model($breed, ['url' => 'fish/breed']) !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'list' => 'nameList']) !!}

                            <datalist id="nameList">
                                @foreach($breeds as $breed)
                                    <option value="{{ $breed->name }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="form-group">
                            {!! Form::label('fish_families_id', 'Family') !!} <a href="/fish/family/create" class="btn" role="button">New Family</a>
                            {!! Form::select('fish_families_id', $families, null, ['class' => 'form-control']) !!}
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
