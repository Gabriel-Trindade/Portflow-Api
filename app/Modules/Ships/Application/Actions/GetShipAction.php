<?php

namespace App\Modules\Ships\Application\Actions;

use App\Modules\Ships\Domain\Exceptions\ShipNotFoundException;
use App\Modules\Ships\Domain\Repositories\ShipRepositoryInterface;
use App\Modules\Ships\Infrastructure\Models\Ship;

class GetShipAction
{
    public function __construct(
        private readonly ShipRepositoryInterface $repository,
    ) {}

    public function execute(int $id): Ship
    {
        $ship = $this->repository->findById($id);

        if (! $ship) {
            throw new ShipNotFoundException($id);
        }

        return $ship;
    }
}
