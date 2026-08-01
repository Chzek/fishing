<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalBestController extends Controller
{
    /**
     * @param \Fishinglog\Models\Angler $angler
     * 
     * @return \Fishinglog\Models\Record|null
     */
    public static function bestByLength(Angler $angler)
    {
        return Record::where("anglers_id", $angler->id)
            ->orderBy("length", "desc")
            ->first();
    }

    /**
     * @param \Fishinglog\Models\Angler $angler
     * 
     * @return \Fishinglog\Models\Record|null
     */
    public static function bestByWeight(Angler $angler)
    {
        return Record::where("anglers_id", $angler->id)
            ->whereNotNull("weight")
            ->orderBy("weight", "desc")
            ->first();
    }

    /**
     * @param \Fishinglog\Models\Angler $angler
     * 
     * @return \Fishinglog\Models\Lake|null
     */
    public static function lakeWithMostCatches(Angler $angler)
    {
        $lake = Record::select('lakes_id', DB::raw('count(id) total'))
            ->where("anglers_id", $angler->id)
            ->groupBy('lakes_id')
            ->orderBy('total', 'desc')
            ->first();

        return $lake ? Lake::find($lake->lakes_id) : null;
    }
}
