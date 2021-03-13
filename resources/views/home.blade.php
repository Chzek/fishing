@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Profile</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="container">
                        @if (isset($angler))
                            <div class="row">
                                <div class="col-8">
                                <h1>{{ $angler->firstName }} {{ $angler->lastName }}</h1>
                                </div>
                                <div class="col-4">
                                    <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16d1e10f30b%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16d1e10f30b%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.421875%22%20y%3D%22104.4578125%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Profile picture." class="img-thumbnail">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    Bio??
                                </div>
                            </div>
                            <div class="card-group">
                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="text-center">{{ $records->groupBy('lakes_id')->count() }}</h2>
                                    </div>
                                    <div class="card-footer text-center">
                                    Lakes Visited
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="text-center">{{ $records->count() }}</h2>
                                    </div>
                                    <div class="card-footer text-center">
                                    Fish Caught
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="text-center">{{ $crews }}</h2>
                                    </div>
                                    <div class="card-footer text-center">
                                    Expeditions
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">Personal Bests</div>
                                        <div class="card-body">
                                            Caught a {{ $angler->personal_best()->length }}in. {{ $angler->personal_best()->fishBreed->name }} in the waters of {{ $angler->personal_best()->lake->name }} on {{ $angler->personal_best()->caught }}.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div>
                                User not associated with angler.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
