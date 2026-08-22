<?php

namespace Fishinglog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFishBreedRequest extends FormRequest
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
            'fish_families_id' => 'required|exists:fish_families,id',
            'name' => [
                'required',
                \Illuminate\Validation\Rule::unique('fish_breeds')->ignore($this->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:30720',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:30720',
        ];


    }
}
