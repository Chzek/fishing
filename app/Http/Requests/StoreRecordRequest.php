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
            'anglers_id' => 'required|integer|exists:anglers,id',
            'lakes_id' => 'required|integer|exists:lakes,id',
            'fish_breeds_id' => 'required|integer|exists:fish_breeds,id',
            'lures_id' => 'nullable|integer|exists:lures,id',
            'length' => 'required|numeric|gt:0',
            'weight' => 'nullable|numeric|gt:0',
            'temperature' => 'nullable|numeric',
            'released' => 'required|boolean',
            'caught' => 'required|date|before_or_equal:today',
        ];
    }
}
