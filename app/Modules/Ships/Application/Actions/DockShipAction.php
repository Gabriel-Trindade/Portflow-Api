<?php

namespace App\Modules\Ships\Application\Actions;

use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Domain\Exceptions\BerthNotAvailableException;
use App\Modules\Ships\Domain\Exceptions\ShipNotFoundException;
use App\Modules\Ships\Domain\Repositories\ShipRepositoryInterface;
use App\Modules\Ships\Domain\Services\ShipStateMachine;
use App\Modules\Ships\Infrastructure\Models\Ship;

class DockShipAction
{
    public function __construct(
        private readonly ShipRepositoryInterface $repository,
        private readonly ShipStateMachine $stateMachine,
    ) {}

    public function execute(int $id, string $berthCode): Ship
    {
        $ship = $this->repository->findById($id);

        if (! $ship) {
            throw new ShipNotFoundException($id);
        }

        $this->stateMachine->assertCanTransition($ship->status, ShipStatus::Docked);

        $normalizedBerthCode = $this->normalizeBerthCode($berthCode);

        if ($this->repository->hasDockedShipAtBerth($normalizedBerthCode, $ship->id)) {
            throw new BerthNotAvailableException($normalizedBerthCode);
        }

        return $this->repository->update($ship, [
            'status' => ShipStatus::Docked,
            'berth_code' => $normalizedBerthCode,
        ]);
    }

    private function normalizeBerthCode(string $berthCode): string
    {
        return strtoupper(trim($berthCode));
    }
}
