<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSharedCharacterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'initiative' => 'required|integer|min:1|max:30',
            'max_hp' => 'required|integer|min:1',
            'current_hp' => 'nullable|integer|min:0',
            'armor_class' => 'required|integer|min:1|max:30',
            'resistances' => 'nullable|array',
            'resistances.*' => 'string',
            'immunities' => 'nullable|array',
            'immunities.*' => 'string',
            'vulnerabilities' => 'nullable|array',
            'vulnerabilities.*' => 'string',
        ];
    }
}
