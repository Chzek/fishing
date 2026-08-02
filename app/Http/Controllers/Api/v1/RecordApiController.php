<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Http\Requests\StoreRecordRequest;
use Fishinglog\Http\Resources\RecordResource;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;

class RecordApiController extends Controller
{
    public function index()
    {
        $records = Record::with(['angler', 'lake', 'fishBreed', 'lure', 'dailyWeather'])
            ->orderBy('caught', 'desc')
            ->paginate(15);

        return RecordResource::collection($records);
    }

    public function show(Record $record)
    {
        $record->load(['angler', 'lake', 'fishBreed', 'lure', 'dailyWeather']);

        return new RecordResource($record);
    }

    public function store(StoreRecordRequest $request, \Fishinglog\Services\WeatherTelemetryService $weatherService)
    {
        // 1. Check idempotency by Client UUID
        if ($request->filled('client_id')) {
            $existing = Record::where('client_id', $request->client_id)->first();
            if ($existing) {
                return (new RecordResource($existing->load(['angler', 'lake', 'fishBreed', 'lure', 'dailyWeather'])))
                    ->additional(['status' => 'duplicate_ignored']);
            }
        }

        // 2. Fallback attribute matching guard
        $attributeMatch = Record::where('anglers_id', $request->anglers_id)
            ->where('lakes_id', $request->lakes_id)
            ->where('fish_breeds_id', $request->fish_breeds_id)
            ->where('length', $request->length)
            ->where('caught', $request->caught)
            ->first();

        if ($attributeMatch) {
            return (new RecordResource($attributeMatch->load(['angler', 'lake', 'fishBreed', 'lure', 'dailyWeather'])))
                ->additional(['status' => 'duplicate_ignored']);
        }

        // 3. Create new record
        $record = Record::create($request->validated());

        // 4. Attempt weather telemetry lookup (gracefully succeeds offline)
        if ($record->lake) {
            $weatherService->fetchForLakeAndDate($record->lake, $record->caught);
        }

        return (new RecordResource($record->load(['angler', 'lake', 'fishBreed', 'lure', 'dailyWeather'])))
            ->additional(['status' => 'created']);
    }
}
