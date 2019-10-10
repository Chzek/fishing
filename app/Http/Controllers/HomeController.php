<?php

namespace Fishinglog\Http\Controllers;

use Illuminate\Http\Request;

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
        $angler = \Fishinglog\Angler::where('user_id', auth()->user()->id)->first();

        if(isset($angler->id))
        {
            $records = \Fishinglog\Record::where('anglers_id', $angler->id)->get();
            $crews = \Fishinglog\Crew::where('anglers_id', $angler->id)->count();
        }else{
            $records = [];
            $crews = [];
        }

        return view('home', [
            'angler' => $angler,
            'records' => $records,
            'crews' => $crews,
        ]);
    }
}
