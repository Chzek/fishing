<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Http\Resources\RecordResource;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;

class RecordApiController extends Controller
{
    public function index()
    {
        $records = Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->orderBy('caught', 'desc')
            ->paginate(15);

        return RecordResource::collection($records);
    }

    public function show(Record $record)
    {
        $record->load(['angler', 'lake', 'fishBreed', 'lure']);

        return new RecordResource($record);
    }
}
