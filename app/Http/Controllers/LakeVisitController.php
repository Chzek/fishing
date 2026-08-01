<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;

class LakeVisitController extends Controller
{
    public function index(Lake $lake)
    {
        $fish = Record::where('lakes_id', $lake->id)->orderBy('caught', 'desc')->get();
        $fish = $fish->groupBy('caught');

        return view('lake.visits', [
            "recordsByDate" => $fish,
            "lake" => $lake,
        ]);
    }
}
