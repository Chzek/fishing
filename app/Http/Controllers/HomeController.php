<?php

namespace Fishinglog\Http\Controllers;

use Faker\Provider\ar_JO\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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
    public function index()
    {
        $angler = \Fishinglog\Angler::where('user_id', Auth::id())->first();

        if(isset($angler->id))
        {
            $records = \Fishinglog\Record::where('anglers_id', $angler->id)->get();
            $crews = \Fishinglog\Crew::where('anglers_id', $angler->id)->count();
            $personalBest = [
                'byLength' => PersonalBestController::bestByLength($angler),
                'byWeight' => PersonalBestController::bestByWeight($angler),
                'lakeWithMostCatches' => PersonalBestController::lakeWithMostCatches($angler),
            ];
        }else{
            $records = [];
            $crews = [];
            $personalBest = [];
        }

        return view('home', [
            'angler' => $angler,
            'records' => $records,
            'crews' => $crews,
            'personalBest' => $personalBest
        ]);
    }
}
