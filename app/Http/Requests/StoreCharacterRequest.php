<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCharacterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'initiative' => 'required|integer|min:1',
            'max_hp' => 'nullable|integer|min:1',
            'current_hp' => 'nullable|integer|min:0',
            'is_player' => 'nullable|boolean',
            'user_id' => 'nullable|exists:users,id',
            'resistances' => 'nullable|array',
            'resistances.*' => 'string',
            'immunities' => 'nullable|array',
            'immunities.*' => 'string',
            'vulnerabilities' => 'nullable|array',
            'vulnerabilities.*' => 'string',
            'condition_immunities' => 'nullable|array',
            'condition_immunities.*' => 'string',
        ];
    }
}
