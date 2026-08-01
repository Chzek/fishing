<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Http\Resources\AnglerResource;
use Fishinglog\Models\Angler;
use Illuminate\Http\Request;

class AnglerApiController extends Controller
{
    public function index()
    {
        $anglers = Angler::orderBy('lastName', 'asc')->paginate(15);

        return AnglerResource::collection($anglers);
    }

    public function show(Angler $angler)
    {
        return new AnglerResource($angler);
    }
}
