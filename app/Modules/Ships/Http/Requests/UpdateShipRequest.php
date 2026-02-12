<?php

namespace App\Modules\Ships\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'imo' => [
                'sometimes',
                'string',
                'size:7',
                'regex:/^\d{7}$/',
                Rule::unique('ships', 'imo')->ignore($this->route('id')),
            ],
            'eta' => ['sometimes', 'date'],
            'etd' => ['sometimes', 'date', 'after_or_equal:eta'],
            'has_pending_operations' => ['sometimes', 'boolean'],
        ];
    }
}
