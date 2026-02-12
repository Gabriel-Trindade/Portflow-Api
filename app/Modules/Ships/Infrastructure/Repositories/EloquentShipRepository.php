<?php

namespace App\Modules\Ships\Infrastructure\Repositories;

use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Domain\Repositories\ShipRepositoryInterface;
use App\Modules\Ships\Infrastructure\Models\Ship;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentShipRepository implements ShipRepositoryInterface
{
    public function findById(int $id): ?Ship
    {
        return Ship::find($id);
    }

    public function existsByImo(string $imo, ?int $excludeId = null): bool
    {
        return Ship::query()
            ->where('imo', $imo)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->exists();
    }

    public function create(array $data): Ship
    {
        return Ship::create($data);
    }

    public function update(Ship $ship, array $data): Ship
    {
        $ship->update($data);

        return $ship->refresh();
    }

    public function hasDockedShipAtBerth(string $berthCode, ?int $excludeShipId = null): bool
    {
        return Ship::query()
            ->where('berth_code', $berthCode)
            ->where('status', ShipStatus::Docked)
            ->when($excludeShipId, fn ($query) => $query->where('id', '!=', $excludeShipId))
            ->exists();
    }

    public function paginate(
        int $perPage = 15,
        ?ShipStatus $status = null,
        ?string $search = null,
    ): LengthAwarePaginator {
        return Ship::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when(
                $search,
                fn ($query) => $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('imo', 'like', "%{$search}%")
                        ->orWhere('berth_code', 'like', "%{$search}%");
                }),
            )
            ->orderBy('eta')
            ->paginate($perPage);
    }
}
