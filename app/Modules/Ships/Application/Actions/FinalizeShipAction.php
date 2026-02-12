<?php

namespace App\Modules\Ships\Application\Actions;

use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Domain\Exceptions\ShipHasPendingOperationsException;
use App\Modules\Ships\Domain\Exceptions\ShipNotFoundException;
use App\Modules\Ships\Domain\Repositories\ShipRepositoryInterface;
use App\Modules\Ships\Domain\Services\ShipStateMachine;
use App\Modules\Ships\Infrastructure\Models\Ship;

class FinalizeShipAction
{
    public function __construct(
        private readonly ShipRepositoryInterface $repository,
        private readonly ShipStateMachine $stateMachine,
    ) {}

    public function execute(int $id): Ship
    {
        $ship = $this->repository->findById($id);

        if (! $ship) {
            throw new ShipNotFoundException($id);
        }

        if ($ship->has_pending_operations) {
            throw new ShipHasPendingOperationsException($ship->id);
        }

        $this->stateMachine->assertCanTransition($ship->status, ShipStatus::Finalized);

        return $this->repository->update($ship, [
            'status' => ShipStatus::Finalized,
        ]);
    }
}
