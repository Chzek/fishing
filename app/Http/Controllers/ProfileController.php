<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $angler = \Fishinglog\Angler::where('user_id', Auth::id())
            ->first();

        if(isset($angler->id))
        {
            $records = \Fishinglog\Record::where('anglers_id', $angler->id)
                ->orderBy('caught', 'desc')
                ->get()
                ->groupBy('caught');

            $crews = \Fishinglog\Crew::where('anglers_id', $angler->id)
                ->count();

            $personalBest = [
                'byLength' => PersonalBestController::bestByLength($angler),
                'byWeight' => PersonalBestController::bestByWeight($angler),
                'lakeWithMostCatches' => PersonalBestController::lakeWithMostCatches($angler),
            ];

            $record_count = \Fishinglog\Record::where('anglers_id', $angler->id)->count();
            $lake_count = \Fishinglog\Record::where('anglers_id', $angler->id)
                ->distinct('lakes_id')
                ->count('lakes_id');
        }else{
            $records = [];
            $crews = [];
            $personalBest = [];
            $record_count = 0;
            $lake_count = 0;
        }

        return view('profile.show', [
            'angler' => $angler,
            'records' => $records,
            'crews' => $crews,
            'personalBest' => $personalBest,
            'record_count' => $record_count,
            'lake_count' => $lake_count,
        ]);
    }

    /**
     * Edit the authenticated users profile
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('profile.edit',[
            'user' => Auth::user(),
        ]);
    }
}
