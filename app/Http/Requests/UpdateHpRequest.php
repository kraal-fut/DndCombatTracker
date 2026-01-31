<?php

namespace App\Http\Requests;

use App\Enums\HPUpdateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateHpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hp_change' => 'nullable|integer',
            'change_type' => ['required', new Enum(HPUpdateType::class)],
            'damages' => 'nullable|array',
            'damages.*.amount' => 'required_with:damages|integer',
            'damages.*.type' => 'required_with:damages|string',
            'ignore_resist' => 'nullable|boolean',
        ];
    }
}
