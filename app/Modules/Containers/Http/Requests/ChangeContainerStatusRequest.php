<?php

namespace App\Modules\Containers\Http\Requests;

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeContainerStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_map(fn ($status) => $status->value, ContainerStatus::cases()))],
        ];
    }
}
