<?php

namespace Fishinglog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLureRequest extends FormRequest
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
            'name' => 'required|string',
            'brand' => 'nullable|string',
            'category' => 'nullable|string',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'weight' => 'nullable|string',
            'depth_range' => 'nullable|string',
        ];
    }

}
