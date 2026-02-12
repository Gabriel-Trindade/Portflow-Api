<?php

namespace App\Modules\Ships\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'imo' => $this->imo,
            'eta' => $this->eta?->toISOString(),
            'etd' => $this->etd?->toISOString(),
            'status' => $this->status->value,
            'berth_code' => $this->berth_code,
            'has_pending_operations' => $this->has_pending_operations,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
