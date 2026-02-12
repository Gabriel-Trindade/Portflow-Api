<?php

namespace App\Modules\Ships\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DockShipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'berth_code' => ['required', 'string', 'max:20'],
        ];
    }
}
