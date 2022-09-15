<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Angler;
use Fishinglog\User;
use Illuminate\Http\Request;
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
        //
        $angler = new Angler;
        $unassigned = Angler::select('id')->whereNull('user_id')->get();

        $users = \Fishinglog\User::whereIn('id', $unassigned->toArray())->pluck('name', 'id');

        return view('angler.create', [
            'angler' => $angler,
            'users' => $users
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
        $unassigned = Angler::select('id')->get();

        $users = \Fishinglog\User::whereIn('id', $unassigned->toArray())->pluck('name', 'id');
        return view('angler.edit', [
            'angler' => $angler,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Fishinglog\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Angler $angler)
    {
        //
        $request->validate($this->rules());
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

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]); 
        
        $angler = Angler::find(Auth::user()->anglers_id);

        $avatarName = 'avatar_'.$angler->id.time().request()->avatar->getClientOriginalExtension();

        $request->avatar->storeAs('avatars', $avatarName);

        $user->avatar = $avatarName;
        $user->save();

        return back()->with('success', 'You have successfully uploaded your avatar.');
    }

    private function rules(){
        return [
            'firstName' => 'required|max:255',
            'middleName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'user_id' => 'integer|nullable',
            'birthdate' => 'date|nullable',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
