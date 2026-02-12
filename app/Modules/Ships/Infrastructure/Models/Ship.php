<?php

namespace App\Modules\Ships\Infrastructure\Models;

use App\Modules\Ships\Domain\Enums\ShipStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'imo',
        'eta',
        'etd',
        'status',
        'berth_code',
        'has_pending_operations',
    ];

    protected function casts(): array
    {
        return [
            'eta' => 'immutable_datetime',
            'etd' => 'immutable_datetime',
            'status' => ShipStatus::class,
            'has_pending_operations' => 'boolean',
        ];
    }
}
