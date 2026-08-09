<?php

namespace Fishinglog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'nullable|string|max:64',
            'anglers_id' => 'required|string|exists:anglers,id',
            'lakes_id' => 'required|string|exists:lakes,id',
            'fish_breeds_id' => 'required|string|exists:fish_breeds,id',
            'lures_id' => 'nullable|string|exists:lures,id',
            'length' => 'required|numeric|gt:0',
            'weight' => 'nullable|numeric|gt:0',
            'temperature' => 'nullable|numeric',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'released' => 'required|boolean',
            'caught' => 'required|date|before_or_equal:today',
        ];
    }
}
