<?php

namespace Fishinglog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalBestController extends Controller
{
    /**
     * @param \Fishinglog\Angler
     * 
     * @return \Fishinglog\Record
     */
    public static function bestByLength(\Fishinglog\Angler $angler)
    {
        return \Fishinglog\Record::where("anglers_id", $angler->id)
            ->orderBy("length", "desc")
            ->first();
    }

    /**
     * @param \Fishinglog\Angler
     * 
     * @return \Fishinglog\Record
     */
    public static function bestByWeight(\Fishinglog\Angler $angler)
    {
        return \Fishinglog\Record::where("anglers_id", $angler->id)
            ->whereNotNull("weight")
            ->orderBy("weight", "desc")
            ->first();
    }

    /**
     * @param \Fishinglog\Angler
     * 
     * @return \Fishinglog\Lake
     */
    public static function lakeWithMostCatches(\Fishinglog\Angler $angler)
    {
        $lake = \Fishinglog\Record::select('lakes_id', DB::raw('count(id) total'))
            ->where("anglers_id", $angler->id)
            ->groupBy('lakes_id')
            ->orderBy('total', 'desc')
            ->first();

        return \Fishinglog\Lake::find($lake->lakes_id);
    }
}
