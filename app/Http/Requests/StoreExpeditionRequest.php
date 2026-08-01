<?php

namespace Fishinglog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpeditionRequest extends FormRequest
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
            'description' => 'required|string|max:1000',
            'start' => 'required|date',
            'finish' => 'required|date|after_or_equal:start',
        ];
    }
}
