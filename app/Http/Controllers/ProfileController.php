<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
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
        $angler = Angler::where('user_id', Auth::id())->first();

        if (isset($angler->id)) {
            $records = Record::where('anglers_id', $angler->id)
                ->orderBy('caught', 'desc')
                ->get()
                ->groupBy('caught');

            $crews = Crew::where('anglers_id', $angler->id)->count();

            $personalBest = [
                'byLength' => PersonalBestController::bestByLength($angler),
                'byWeight' => PersonalBestController::bestByWeight($angler),
                'lakeWithMostCatches' => PersonalBestController::lakeWithMostCatches($angler),
            ];
        } else {
            $records = [];
            $crews = [];
            $personalBest = [];
        }

        $recordCount = isset($angler->id) ? Record::where('anglers_id', $angler->id)->count() : 0;
        $lakeCountResult = isset($angler->id)
            ? Record::select(DB::raw('count(distinct lakes_id) as lake_count'))
                ->where('anglers_id', $angler->id)
                ->first()
            : null;
        $lakeCount = $lakeCountResult ? $lakeCountResult->lake_count : 0;

        return view('profile.show', [
            'angler' => $angler,
            'records' => $records,
            'crews' => $crews,
            'personalBest' => $personalBest,
            'record_count' => $recordCount,
            'lake_count' => $lakeCount,
        ]);
    }

    /**
     * Edit the authenticated users profile
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }
}
