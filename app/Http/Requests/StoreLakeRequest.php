<?php

namespace Fishinglog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'structure' => 'nullable|string|max:255',
            'max_depth' => 'nullable|numeric|min:0',
            'fishing_zone_id' => 'nullable|string|exists:fishing_zones,id',
        ];
    }
}
