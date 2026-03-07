<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Lake;
use Fishinglog\Pipes\Filters\FilterByName;
use Fishinglog\Pipes\Filters\FilterByRecordsCount;
use Fishinglog\Pipes\Filters\SortBy;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Fishinglog\Http\Requests\StoreLakeRequest;
use Fishinglog\Http\Requests\UpdateLakeRequest;

class LakeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Pipeline $pipeline, Request $request)
    {
        //
        $lakes = \Fishinglog\Lake::withCount('records')
            ->withCount(['records as visits' => function($query) {
                $query->select(DB::raw('count(distinct records.caught)'));
            }])
            ->withCount(['anglers as anglers_count' => function($query){
                $query->select(DB::raw('count(distinct anglers.id)'));
            }]);
            // ->orderBy('name', 'asc');
            // ->paginate(10);

        $lakes = $pipeline->send($lakes)
            ->through([
                SortBy::class,
                FilterByName::class,
                FilterByRecordsCount::class,
            ])
            ->thenReturn();

        $lakes = $lakes->paginate(10);

        // dd($request->query());

        $lakes->appends($request->query());

        return view('lake.index', [
            'lakes' => $lakes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $lake = new Lake;
        return view('lake.create', [
            'lake' => $lake
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLakeRequest $request)
    {
        //

        $lake = new Lake;
        $lake->name = $request->name;
        $lake->latitude = $request->latitude;
        $lake->longitude = $request->longitude;

        $lake->save();

        return redirect('/lake');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Lake  $lake
     * @return \Illuminate\Http\Response
     */
    public function show(Lake $lake)
    {
        $count = \Fishinglog\Record::where('lakes_id', $lake->id)->count();
        $longest = \Fishinglog\Record::where('lakes_id', $lake->id)
            ->orderBy('length', 'desc')
            ->first();
        $fattest = \Fishinglog\Record::where('lakes_id', $lake->id)
            ->orderBy('weight', 'desc')
            ->first();

        $visits = \Fishinglog\Record::where('lakes_id', $lake->id)
            ->distinct('caught')
            ->count('caught');
            
        $anglers = \Fishinglog\Record::where('lakes_id', $lake->id)
            ->distinct('anglers_id')
            ->count('anglers_id');

        return view('lake.show', [
            'lake' => $lake,
            'count' => $count,
            'longest' => $longest,
            'fattest' => $fattest,
            'visits' => $visits,
            'anglers' => $anglers,
            'stats' => $this->stats($lake),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Lake  $lake
     * @return \Illuminate\Http\Response
     */
    public function edit(Lake $lake)
    {
        return view('lake.edit', [
            'lake' => $lake
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\Lake  $lake
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLakeRequest $request, Lake $lake)
    {
        //
        $lake = \Fishinglog\Lake::find($request->id);

        $lake->name = $request->name;
        $lake->latitude = $request->latitude;
        $lake->longitude = $request->longitude;

        $lake->save();

        return redirect('/lake/'.$lake->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Lake  $lake
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lake $lake)
    {
        //
    }



    public function stats(Lake $lake, $quantity = null)
    {
        $query = \Fishinglog\Record::select('fish_breeds_id',
                DB::raw('count(*) as cnt'),
                DB::raw('round(avg(length), 2) as avg_length'),
                DB::raw('min(length) as min_length'),
                DB::raw('max(length) as max_length'),
                DB::raw('round(avg(weight), 2) as avg_weight'),
                DB::raw('min(weight) as min_weight'),
                DB::raw('max(weight) as max_weight'),
                DB::raw('sum(if(weight IS NOT NULL, 1, 0)) as weighed_count')
            )
            ->where('lakes_id', $lake->id)
            ->with('fishBreed')
            ->groupBy('fish_breeds_id')
            ->orderBy('cnt', 'desc');

        if(!is_null($quantity))
            $query->limit($quantity);
        
        return $query->get();
    }
}
