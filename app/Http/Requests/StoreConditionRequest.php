<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'condition_type' => 'required|string',
            'custom_name' => 'nullable|string|max:255',
            'duration_rounds' => 'nullable|integer|min:1',
            'bypass_immunity' => 'nullable|boolean',
        ];
    }
}
