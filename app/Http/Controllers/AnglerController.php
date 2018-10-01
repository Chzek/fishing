<?php

namespace App\Http\Controllers;

use App\Angler;
use Illuminate\Http\Request;

class AnglerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $anglers = \App\Angler::withCount('records')
            ->orderBy('lastName', 'asc')
            ->orderBy('firstName', 'asc')
            ->orderBy('middleName', 'asc')
            ->get();

        return view('angler.index', [
            'anglers' => $anglers
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
        $angler = new Angler;
        return view('angler.create', [
            'angler' => $angler
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

        $angler = new Angler;
        $angler->firstName = $request->firstName;
        $angler->middleName = $request->middleName;
        $angler->lastName = $request->lastName;

        $angler->save();

        return redirect('/angler');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function show(Angler $angler, $id)
    {
        //
        $angler = \App\Angler::find($id);
        return view('angler.show', [
            'angler' => $angler
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function edit(Angler $angler, $id)
    {
        //
        $angler = \App\Angler::find($id);
        return view('angler.edit', [
            'angler' => $angler
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Angler $angler)
    {
        //
        $request->validate($this->rules());
        $angler = \App\Angler::find($request->id);

        $angler->firstName = $request->firstName;
        $angler->middleName = $request->middleName;
        $angler->lastName = $request->lastName;

        $angler->save();

        return redirect('/angler/'.$angler->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Angler $angler)
    {
        //
    }

    private function rules(){
        return [
            'firstName' => 'required|max:255',
            'middleName' => 'required|max:255',
            'lastName' => 'required|max:255',
        ];
    }
}
