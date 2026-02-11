<?php

namespace App\Modules\Containers\Http\Requests;

use App\Modules\Containers\Domain\Enums\ContainerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:11', 'regex:/^[A-Za-z]{4}\d{7}$/', 'unique:containers,code'],
            'type' => ['required', Rule::in(array_map(fn ($type) => $type->value, ContainerType::cases()))],
        ];
    }
}
