@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-header">
                    <h3 class='card-title'>
                    Lake
                        <a href='/lake/{{ $lake->id }}/visits' class='card-link btn btn-md btn-outline-dark float-right' role='button'>Visits</a>
                        <a href='/lake/{{ $lake->id }}/edit' class='card-link btn btn-md btn-outline-dark float-right' role='button'>Edit</a>
                        <a href='{{ url('lake') }}' class='card-link btn btn-md btn-outline-dark m-0 float-right' role='button'>Back</a>
                    </h3>
                    
                </div>
                <div class="card-body">
                    <h1 class="card-subtitle mb-2 text-muted">
                        {{ $lake->name}}
                    </h1>
                    <div class="card-group" style="margin-bottom: 0.5em">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="text-center">{{ $count }}</h1>
                                <h6 class="text-muted text-center">{{ $visits }} Visits</h6>
                                <h6 class="text-muted text-center">{{ $anglers }} Anglers</h6>
                            </div>
                            <div class="card-footer text-center">
                            Total
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                @isset($longest)
                                    <h1 class="text-center" style="margin-bottom: 0">{{ $longest->length }} in.</h1>
                                    <h6 class="text-muted text-center">{{ $longest->fishBreed->name }}</h6>
                                @else
                                    <h1 class="text-center" style="margin-bottom: 0">--</h1>
                                    <h6 class="text-muted text-center">Please record more fish!</h6>
                                @endisset
                            </div>
                            <div class="card-footer text-center">
                            Longest
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                @if(isset($fattest) && !is_null($fattest->weight))
                                    <h1 class="text-center" style="margin-bottom: 0">{{ $fattest->weight }} lbs.</h1>
                                    <h6 class="text-muted text-center">{{ $fattest->fishBreed->name }}</h6>
                                @else
                                    <h1 class="text-center" style="margin-bottom: 0">--</h1>
                                    <h6 class="text-muted text-center">Please record more fish!</h6>
                                @endif
                            </div>
                            <div class="card-footer text-center">
                            Fattest
                            </div>
                        </div>
                    </div>

                    @foreach($stats as $stat)
                        <div class="card-body">
                            <h4 class="card-subtitle mb-2 text-muted">
                                {{ $stat->fishBreed->name}}
                            </h4>
                            <div class="card-group" style="margin-bottom: 0.5em">
                                <div class="card">
                                    <div class="card-body">
                                        <h1 class="text-center">{{ $stat->cnt }}</h1>
                                    </div>
                                    <div class="card-footer text-center">
                                    Total
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h1 class="text-center">{{ $stat->avg_length }} in.</h1>
                                        <h6 class="text-muted text-center">{{ $stat->min_length }}/{{ $stat->max_length }} in. (Min/Max)</h6>
                                    </div>
                                    <div class="card-footer text-center">
                                    Avg. Length
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        @if(!is_null($stat->avg_weight))
                                            <h1 class="text-center">{{ $stat->avg_weight }} lbs.</h1>
                                            <h6 class="text-muted text-center">{{ $stat->weighed_count }}/{{ $stat->min_weight }}/{{ $stat->max_weight }} (Cnt/Min/Max)</h6>
                                        @else
                                            <h1 class="text-center">--</h1>
                                            <h6 class="text-muted text-center">Please record more fish!</h6>
                                        @endif
                                    </div>
                                    <div class="card-footer text-center">
                                    *Avg. Weight
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    <i>*Average Weight of larger fish.</i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
