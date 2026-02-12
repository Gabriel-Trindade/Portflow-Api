<?php

namespace App\Modules\Ships\Domain\Repositories;

use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Infrastructure\Models\Ship;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ShipRepositoryInterface
{
    public function findById(int $id): ?Ship;

    public function existsByImo(string $imo, ?int $excludeId = null): bool;

    public function create(array $data): Ship;

    public function update(Ship $ship, array $data): Ship;

    public function hasDockedShipAtBerth(string $berthCode, ?int $excludeShipId = null): bool;

    public function paginate(
        int $perPage = 15,
        ?ShipStatus $status = null,
        ?string $search = null,
    ): LengthAwarePaginator;
}
