<?php

namespace Fishinglog\Http\Controllers\Angler;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Http\Requests\StoreAnglerRequest;
use Fishinglog\Http\Requests\UpdateAnglerAvatarRequest;
use Fishinglog\Http\Requests\UpdateAnglerRequest;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnglerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $anglers = Angler::withCount('records')
            ->withCount(['records as lakes_count' => function ($query) {
                $query->select(DB::raw('count(distinct records.lakes_id)'));
            }])
            ->orderBy('records_count', 'desc')
            ->paginate(10);

        return view('angler.index', [
            'anglers' => $anglers,
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
     * @param  \Fishinglog\Http\Requests\StoreAnglerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnglerRequest $request)
    {
        $angler = new Angler;
        $angler->firstName = $request->firstName;
        $angler->middleName = $request->middleName;
        $angler->lastName = $request->lastName;
        $angler->user_id = $request->user_id;
        $angler->birthdate = $request->birthdate;

        $angler->save();

        return redirect('/angler');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Models\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function show(Angler $angler)
    {
        $records = Record::where('anglers_id', $angler->id)
            ->orderBy('caught', 'desc')
            ->with('fishBreed')
            ->take(10)
            ->get();

        $longest = Record::where('anglers_id', $angler->id)
            ->orderBy('length', 'desc')
            ->first();

        $count = Record::where('anglers_id', $angler->id)->count();

        $crews = Crew::where('anglers_id', $angler->id)->count();

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
     * @param  \Fishinglog\Models\Angler  $angler
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
     * @param  \Fishinglog\Http\Requests\UpdateAnglerRequest  $request
     * @param  \Fishinglog\Models\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnglerRequest $request, Angler $angler)
    {
        $targetAngler = Angler::find($request->id) ?? $angler;

        if ($request->hasFile('avatar')) {
            $avatarName = 'avatar_' . time() . '.' . $request->avatar->getClientOriginalExtension();
            $request->avatar->storeAs('avatars', $avatarName);
            $targetAngler->avatar = $avatarName;
        }

        $targetAngler->firstName = $request->firstName;
        $targetAngler->middleName = $request->middleName;
        $targetAngler->lastName = $request->lastName;
        $targetAngler->user_id = $request->user_id;
        $targetAngler->birthdate = $request->birthdate;

        $targetAngler->save();

        return redirect('/angler/' . $targetAngler->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Angler $angler)
    {
        //
    }

    public function updateAvatar(UpdateAnglerAvatarRequest $request)
    {
        $angler = Auth::user()->angler;

        $avatarName = 'avatar_' . $angler->id . '_' . time() . '.' . $request->avatar->getClientOriginalExtension();
        $request->avatar->storeAs('avatars', $avatarName);

        $angler->avatar = $avatarName;
        $angler->save();

        return back()->with('success', 'You have successfully uploaded your avatar.');
    }
}
