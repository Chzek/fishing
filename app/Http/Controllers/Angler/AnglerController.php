<?php

namespace Fishinglog\Http\Controllers\Angler;

use Fishinglog\Angler;
use Fishinglog\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Fishinglog\Http\Requests\StoreAnglerRequest;
use Fishinglog\Http\Requests\UpdateAnglerRequest;
use Fishinglog\Http\Requests\UpdateAnglerAvatarRequest;

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
        $anglers = \Fishinglog\Angler::withCount('records')
            ->withCount(['records as lakes_count' => function($query) {
                $query->select(DB::raw('count(distinct records.lakes_id)'));
            }])
            // ->orderBy('lastName', 'asc')
            // ->orderBy('firstName', 'asc')
            // ->orderBy('middleName', 'asc')
            ->orderBy('records_count', 'desc')
            ->paginate(10);

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
        $angler = new Angler;

        return view('angler.create', [
            'angler' => $angler,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnglerRequest $request)
    {
        //

        // $avatarName = 'avatar_'.time().'.'.$request->avatar->getClientOriginalExtension();
        // $request->avatar->storeAs('avatars', $avatarName);

        $angler = new Angler;
        $angler->firstName = $request->firstName;
        $angler->middleName = $request->middleName;
        $angler->lastName = $request->lastName;
        $angler->user_id = $request->user_id;
        $angler->birthdate = $request->birthdate;
        // $angler->avatar = $avatarName;

        $angler->save();

        return redirect('/angler');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function show(Angler $angler)
    {
        $records = \Fishinglog\Record::where('anglers_id', $angler->id)
            ->orderBy('caught', 'desc')
            ->with('fishBreed')
            ->take(10)
            ->get();

        $longest = \Fishinglog\Record::where('anglers_id', $angler->id)
            ->orderBy('length', 'desc')
            ->first();

        $count = \Fishinglog\Record::where('anglers_id', $angler->id)
            ->count();

        $crews = \Fishinglog\Crew::where('anglers_id', $angler->id)->count();

        return view('angler.show', [
            'angler' => $angler,
            'records' => $records,
            'longest' => $longest,
            'count' => $count,
            'crews' => $crews,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function edit(Angler $angler)
    {
        return view('angler.edit', [
            'angler' => $angler,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnglerRequest $request, Angler $angler)
    {
        //
        $angler = \Fishinglog\Angler::find($request->id);

        $avatarName = 'avatar_'.time().'.'.$request->avatar->getClientOriginalExtension();
        $request->avatar->storeAs('avatars', $avatarName);

        $angler->firstName = $request->firstName;
        $angler->middleName = $request->middleName;
        $angler->lastName = $request->lastName;
        $angler->user_id = $request->user_id;
        $angler->birthdate = $request->birthdate;
        $angler->avatar = $avatarName;

        $angler->save();

        return redirect('/angler/'.$angler->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Angler $angler)
    {
        //
    }

    public function updateAvatar(UpdateAnglerAvatarRequest $request)
    {
        $angler = Auth::user()->angler;

        $avatarName = 'avatar_'.$angler->id.'_'.time().'.'.$request->avatar->getClientOriginalExtension();

        $request->avatar->storeAs('avatars', $avatarName);

        $angler->avatar = $avatarName;
        $angler->save();

        return back()->with('success', 'You have successfully uploaded your avatar.');
    }


}
