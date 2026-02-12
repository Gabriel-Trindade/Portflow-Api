<?php

namespace App\Modules\Ships\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'imo' => ['required', 'string', 'size:7', 'regex:/^\d{7}$/', 'unique:ships,imo'],
            'eta' => ['required', 'date'],
            'etd' => ['required', 'date', 'after_or_equal:eta'],
            'has_pending_operations' => ['sometimes', 'boolean'],
        ];
    }
}
