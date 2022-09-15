<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Lake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LakeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $lakes = \Fishinglog\Lake::withCount('records')
            ->withCount(['records as visits' => function($query) {
                $query->select(DB::raw('count(distinct records.caught)'));
            }])
            ->withCount(['anglers as anglers_count' => function($query){
                $query->select(DB::raw('count(distinct anglers.id)'));
            }])
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('lake.index', [
            'lakes' => $lakes
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
    public function store(Request $request)
    {
        //
        $request->validate($this->rules());

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

        $trips = \Fishinglog\Record::where('lakes_id', $lake->id)->get();
        $visits = $trips->unique('caught')->count();
        $anglers = $trips->unique('anglers_id')->count();

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
    public function update(Request $request, Lake $lake)
    {
        //
        $request->validate($this->rules());
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

    private function rules()
    {
        return [
            'name' => 'required|max:255',

            // TODO: Figure out the appropriate validation method for this.

            // 'latitude' => [
            //     'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'
            // ],
            // 'longitude' => [
            //     'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'
            // ]
        ];
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
