<?php

namespace Fishinglog\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Display the offline map region manager and pre-cache interface.
     */
    public function offline()
    {
        return view('map.offline');
    }
}
