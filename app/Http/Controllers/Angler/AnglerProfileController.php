<?php

namespace Fishinglog\Http\Controllers\Angler;

use Fishinglog\Angler;
use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Record;
use Illuminate\Support\Facades\DB;

class AnglerProfileController extends Controller
{

    public function show(Angler $angler)
    {
        $records = Record::select(
                'lakes_id',
                'fish_breeds_id',
                DB::raw('count(id) as catches'),
                DB::raw('min(length) as min_length'),
                DB::raw('max(length) as max_length'),
                DB::raw('round(avg(length), 2) as avg_length'),
                DB::raw('min(weight) as min_weight'),
                DB::raw('max(weight) as max_weight'),
                DB::raw('round(avg(weight), 2) as avg_weight'),
            )
            ->with('lake','fishBreed')
            ->where('anglers_id', $angler->id)
            ->groupBy('lakes_id','fish_breeds_id')
            ->get();

        return view('angler.profile', [
            'angler' => $angler,
            'records' => $records->sortBy(function($item) {
                return $item->lake->name;
            })->groupBy(function($item){
                return $item->fishBreed->name;
            })->sortKeys(),
        ]);
    }

}
