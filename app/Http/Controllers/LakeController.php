<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Lake;
use Illuminate\Http\Request;

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
            ->orderBy('name', 'asc')
            ->get();

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
    public function show(Lake $lake, $id)
    {
        //
        $lake = \Fishinglog\Lake::find($id);

        return view('lake.show', [
            'lake' => $lake
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Lake  $lake
     * @return \Illuminate\Http\Response
     */
    public function edit(Lake $lake, $id)
    {
        //
        $lake = \Fishinglog\Lake::find($id);
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
}
