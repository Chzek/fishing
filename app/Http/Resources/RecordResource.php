<?php

namespace Fishinglog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'caught' => $this->caught,
            'length' => $this->length,
            'weight' => $this->weight,
            'temperature' => $this->temperature,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'released' => (bool) $this->released,
            'angler' => new AnglerResource($this->whenLoaded('angler')),
            'lake' => new LakeResource($this->whenLoaded('lake')),
            'daily_weather' => $this->whenLoaded('dailyWeather'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
