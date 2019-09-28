@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in as an admin!
                    <div class="card-group">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $anglers }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Anglers
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $lakes }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Lakes
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $expeditions }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Expeditions
                            </div>
                        </div>
                    </div>
                    <div class="card-group">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $records }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Records
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $fishBreeds }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Fish Breeds
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $fishFamilies }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Fish Families
                            </div>
                        </div>
                    </div>
                    <div class="card-group">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $users }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Users
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $lures }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Lures
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $posts }}</h1>
                            </div>
                            <div class="card-footer text-center">
                            Posts
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
