<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreLureRequest;
use Fishinglog\Http\Requests\UpdateLureRequest;
use Fishinglog\Models\Lure;
use Illuminate\Http\Request;

class LureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(\Illuminate\Pipeline\Pipeline $pipeline, Request $request)
    {
        $query = Lure::query();

        if (!$request->has('sort_by')) {
            $query->orderBy('name', 'asc');
        }

        $query = $pipeline->send($query)
            ->through([
                \Fishinglog\Pipes\Filters\SortBy::class,
                \Fishinglog\Pipes\Filters\FilterBySearch::class,
            ])
            ->thenReturn();

        $lures = $query->paginate()->withQueryString();

        return view('lure.index', [
            'lures' => $lures,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lure = new Lure;
        return view('lure.create', [
            'lure' => $lure,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreLureRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLureRequest $request)
    {
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
     * @param  \Fishinglog\Models\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function show(Lure $lure)
    {
        return view('lure.show', [
            'lure' => $lure,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function edit(Lure $lure)
    {
        $lureNames = Lure::distinct()->select('name')->get();
        $lureColors = Lure::distinct()->select('color')->get();
        $lureSizes = Lure::distinct()->select('size')->get();

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
     * @param  \Fishinglog\Http\Requests\UpdateLureRequest  $request
     * @param  \Fishinglog\Models\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLureRequest $request, Lure $lure)
    {
        $targetLure = Lure::find($request->id) ?? $lure;

        $targetLure->name = $request->name;
        $targetLure->color = $request->color;
        $targetLure->size = $request->size;

        $targetLure->save();

        return redirect('/lure/' . $targetLure->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lure $lure)
    {
        $lure->delete();

        return redirect('/lure')->with('status', 'Lure removed successfully.');
    }
}
