<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStateEffectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modifier_type' => 'required|string',
            'name' => 'required|string|max:255',
            'value' => 'required|integer',
            'advantage_state' => 'required|string',
            'duration_rounds' => 'nullable|integer|min:1',
        ];
    }
}
