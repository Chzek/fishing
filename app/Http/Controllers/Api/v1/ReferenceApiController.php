<?php

namespace Fishinglog\Http\Controllers\Api\v1;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Illuminate\Http\JsonResponse;

class ReferenceApiController extends Controller
{
    public function index(): JsonResponse
    {
        $anglers = Angler::orderBy('lastName', 'asc')->get()->map(fn (Angler $a) => [
            'id' => (string) $a->id,
            'firstName' => $a->firstName,
            'middleName' => $a->middleName,
            'lastName' => $a->lastName,
            'full_name' => $a->full_name,
        ]);

        $lakes = Lake::orderBy('name', 'asc')->get(['id', 'name', 'latitude', 'longitude'])->map(fn (Lake $l) => [
            'id' => (string) $l->id,
            'name' => $l->name,
            'latitude' => $l->latitude,
            'longitude' => $l->longitude,
        ]);

        $fishBreeds = FishBreed::orderBy('name', 'asc')->get(['id', 'name', 'fish_families_id'])->map(fn (FishBreed $b) => [
            'id' => (string) $b->id,
            'name' => $b->name,
            'fish_families_id' => $b->fish_families_id ? (string) $b->fish_families_id : null,
        ]);

        $lures = Lure::orderBy('name', 'asc')->get(['id', 'name', 'brand', 'category', 'color', 'size', 'depth_range'])->map(fn (Lure $lu) => [
            'id' => (string) $lu->id,
            'name' => $lu->name,
            'brand' => $lu->brand,
            'category' => $lu->category ?: 'Other',
            'color' => $lu->color,
            'size' => $lu->size,
            'depth_range' => $lu->depth_range,
        ]);

        $expeditions = Expedition::orderBy('start', 'desc')->get(['id', 'description', 'start', 'finish'])->map(fn (Expedition $e) => [
            'id' => (string) $e->id,
            'description' => $e->description,
            'start' => $e->start,
            'finish' => $e->finish,
        ]);

        return response()->json([
            'anglers' => $anglers,
            'lakes' => $lakes,
            'fish_breeds' => $fishBreeds,
            'lures' => $lures,
            'expeditions' => $expeditions,
        ]);
    }
}
