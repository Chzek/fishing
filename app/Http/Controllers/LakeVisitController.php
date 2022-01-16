<?php

namespace Fishinglog\Http\Controllers;

use Illuminate\Http\Request;
use Fishinglog\Lake;

class LakeVisitController extends Controller
{
    //
    public function index(Lake $lake)
    {
        $fish = \Fishinglog\Record::where('lakes_id', $lake->id)->orderBy('caught', 'desc')->get();

        $fish = $fish->groupBy('caught');

        return view('lake.visits', [
            "recordsByDate" => $fish,
            "lake" => $lake,
        ]);
    }
}
