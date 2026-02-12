<?php

namespace App\Modules\Containers\Infrastructure\Models;

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Enums\ContainerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContainerType::class,
            'status' => ContainerStatus::class,
        ];
    }
}
