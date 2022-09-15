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
                                    <img src="/storage/avatars/{{ ($angler->avatar ?: "user.jpg" ) }}" alt="Profile picture." style="max-width=200;" class="img-thumbnail">
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
                                            <ul>
                                            @if($personalBest['byLength'])
                                                <li>Caught a {{ $personalBest['byLength']->length }}in. {{ $personalBest['byLength']->fishBreed->name }} in the waters of {{ $personalBest['byLength']->lake->name }} on {{ $personalBest['byLength']->caught }}.</li>
                                            @else
                                                <li>Have you caught a fish yet?</li>
                                            @endif
                                            @if($personalBest['byWeight'])
                                                <li>Caught a {{ $personalBest['byWeight']->weight }}lbs. {{ $personalBest['byWeight']->fishBreed->name }} in the waters of {{ $personalBest['byWeight']->lake->name }} on {{ $personalBest['byWeight']->caught }}.</li>
                                            @else
                                                <li>Someone is forgetting to write down the weight of your fish!</li>
                                            @endif
                                            @if($personalBest['lakeWithMostCatches'])
                                                <li>You have caught the more fish on {{ $personalBest['lakeWithMostCatches']->name }}.</li>
                                            @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div>
                                User not associated with angler. Please contact your administrator.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
