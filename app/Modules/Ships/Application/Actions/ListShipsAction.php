<?php

namespace App\Modules\Ships\Application\Actions;

use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Domain\Repositories\ShipRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListShipsAction
{
    public function __construct(
        private readonly ShipRepositoryInterface $repository,
    ) {}

    public function execute(
        int $perPage = 15,
        ?ShipStatus $status = null,
        ?string $search = null,
    ): LengthAwarePaginator {
        return $this->repository->paginate($perPage, $status, $search);
    }
}
