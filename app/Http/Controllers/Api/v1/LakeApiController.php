<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Http\Resources\LakeResource;
use Fishinglog\Models\Lake;
use Illuminate\Http\Request;

class LakeApiController extends Controller
{
    public function index()
    {
        $lakes = Lake::orderBy('name', 'asc')->paginate(15);

        return LakeResource::collection($lakes);
    }

    public function show(Lake $lake)
    {
        return new LakeResource($lake);
    }
}
