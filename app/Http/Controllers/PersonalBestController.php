<?php

namespace Fishinglog\Http\Controllers;

use Illuminate\Http\Request;

class PersonalBestController extends Controller
{
    //

    public static function bestByLength(\Fishinglog\Angler $angler)
    {
        return \Fishinglog\Record::where("anglers_id", $angler->id)
            ->orderBy("length", "desc")
            ->first();
    }

    public static function bestByWeight(\Fishinglog\Angler $angler)
    {
        return \Fishinglog\Record::where("anglers_id", $angler->id)
            ->whereNotNull("weight")
            ->orderBy("weight", "desc")
            ->first();
    }
}
