<?php

namespace App\Modules\Ships\Application\Actions;

use App\Modules\Ships\Application\DTOs\UpdateShipDTO;
use App\Modules\Ships\Domain\Exceptions\InvalidShipScheduleException;
use App\Modules\Ships\Domain\Exceptions\ShipImoAlreadyExistsException;
use App\Modules\Ships\Domain\Exceptions\ShipNotFoundException;
use App\Modules\Ships\Domain\Repositories\ShipRepositoryInterface;
use App\Modules\Ships\Infrastructure\Models\Ship;
use Carbon\CarbonImmutable;

class UpdateShipAction
{
    public function __construct(
        private readonly ShipRepositoryInterface $repository,
    ) {}

    public function execute(int $id, UpdateShipDTO $dto): Ship
    {
        $ship = $this->repository->findById($id);

        if (! $ship) {
            throw new ShipNotFoundException($id);
        }

        $data = [];

        if ($dto->name !== null) {
            $data['name'] = trim($dto->name);
        }

        if ($dto->imo !== null) {
            $imo = $this->normalizeImo($dto->imo);

            if ($this->repository->existsByImo($imo, $ship->id)) {
                throw new ShipImoAlreadyExistsException($imo);
            }

            $data['imo'] = $imo;
        }

        if ($dto->eta !== null) {
            $data['eta'] = CarbonImmutable::parse($dto->eta);
        }

        if ($dto->etd !== null) {
            $data['etd'] = CarbonImmutable::parse($dto->etd);
        }

        if ($dto->has_pending_operations !== null) {
            $data['has_pending_operations'] = $dto->has_pending_operations;
        }

        if ($data === []) {
            return $ship;
        }

        $nextEta = $data['eta'] ?? $ship->eta;
        $nextEtd = $data['etd'] ?? $ship->etd;

        if ($nextEtd->lt($nextEta)) {
            throw new InvalidShipScheduleException();
        }

        return $this->repository->update($ship, $data);
    }

    private function normalizeImo(string $imo): string
    {
        return trim($imo);
    }
}
