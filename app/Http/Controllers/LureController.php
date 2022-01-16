<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Lure;
use Illuminate\Http\Request;

class LureController extends Controller
{
    // Validation rules
    protected $rules = [
        'name' => 'string|required|unique:lures,name,color,size',
        'color' => 'string',
        'size' => 'string',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $lures = \Fishinglog\Lure::paginate();

        return view('lure.index', [
            'lures' => $lures
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
        $lure = new Lure;
        return view('lure.create', [
            'lure' => $lure
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
        $request->validate($this->rules);

        $lure = new Lure;
        $lure->name = $request->name;
        $lure->color = $request->color;
        $lure->size = $request->size;

        $lure->save();

        return redirect('/lure');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function show(Lure $lure)
    {
        return view('lure.show', [
            'lure' => $lure
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function edit(Lure $lure)
    {
        //
        $lureNames = \Fishinglog\Lure::distinct()->select('name')->get();
        $lureColors = \Fishinglog\Lure::distinct()->select('color')->get();
        $lureSizes = \Fishinglog\Lure::distinct()->select('size')->get();

        return view('lure.edit', [
            'lure' => $lure,
            'lureNames' => $lureNames,
            'lureColors' => $lureColors,
            'lureSizes' => $lureSizes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lure $lure)
    {
        //
        $rules = $this->rules;

        // Modify rule to allow for updating the FishBreed
        $rules['name'] = 'string|required|unique_with:lures,name,color,size,'.$request->id;

        $request->validate($rules);
        $lure = \Fishinglog\Lure::find($request->id);

        $lure->name = $request->name;
        $lure->color = $request->color;
        $lure->size = $request->size;

        $lure->save();

        return redirect('/lure/'.$lure->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lure $lure)
    {
        //
    }
}
