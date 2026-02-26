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
            'anglers_id' => 'integer|required',
            'lakes_id' => 'integer|required',
            'fish_breeds_id' => 'integer|required',
            'length' => 'numeric|required',
            'released' => 'boolean|required',
            'caught' => 'date|required',
        ];
    }
}
