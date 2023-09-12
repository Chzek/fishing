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
                                        <h2 class="text-center">{{ $lake_count }}</h2>
                                    </div>
                                    <div class="card-footer text-center">
                                    Lakes Visited
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="text-center">{{ $record_count }}</h2>
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
                                                <li>You have caught the most fish on {{ $personalBest['lakeWithMostCatches']->name }}.</li>
                                            @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    @if(count($records) > 0)
                                        <table class='table'>
                                            <thead>
                                                @foreach($records as $key => $record)
                                                    <tr>
                                                        <th>{{ $key }}</th>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table class="table table-hover">
                                                                <thead class='thead-light'>
                                                                    <tr>
                                                                        <th>Lake</th>
                                                                        <th>Fish</th>
                                                                        <th class="text-center">Weight (lb)</th>
                                                                        <th class="text-center">Length (in)</th>
                                                                        <th>Released</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($record as $catch)
                                                                        <tr>
                                                                            <td class="align-middle">{{ $catch->lake->name }}</td>
                                                                            <td class="align-middle">{{ $catch->fishBreed->name }}</td>
                                                                            <td class="align-middle text-center">{{ $catch->weight }}</td>
                                                                            <td class="align-middle text-center">{{ $catch->length }}</td>
                                                                            <td class="align-middle">
                                                                                @if($catch->released == 1)
                                                                                    <span class="badge badge-secondary">Released</span>
                                                                                @else
                                                                                    <span class="badge badge-primary">Caught</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </thead>
                                        </table>
                                    @endif
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
