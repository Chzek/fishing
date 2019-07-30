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
        $angler = \Fishinglog\Angler::where('user_id', auth()->user()->id )->get();

        $records = \Fishinglog\Record::take(10)
        ->get();

        return view('home', [
            'angler' => $angler,
            'records' => $records
        ]);
    }
}
