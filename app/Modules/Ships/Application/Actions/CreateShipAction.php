<?php

namespace App\Modules\Ships\Application\Actions;

use App\Modules\Ships\Application\DTOs\CreateShipDTO;
use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Domain\Exceptions\InvalidShipScheduleException;
use App\Modules\Ships\Domain\Exceptions\ShipImoAlreadyExistsException;
use App\Modules\Ships\Domain\Repositories\ShipRepositoryInterface;
use App\Modules\Ships\Infrastructure\Models\Ship;
use Carbon\CarbonImmutable;

class CreateShipAction
{
    public function __construct(
        private readonly ShipRepositoryInterface $repository,
    ) {}

    public function execute(CreateShipDTO $dto): Ship
    {
        $imo = $this->normalizeImo($dto->imo);

        if ($this->repository->existsByImo($imo)) {
            throw new ShipImoAlreadyExistsException($imo);
        }

        $eta = CarbonImmutable::parse($dto->eta);
        $etd = CarbonImmutable::parse($dto->etd);

        if ($etd->lt($eta)) {
            throw new InvalidShipScheduleException();
        }

        return $this->repository->create([
            'name' => trim($dto->name),
            'imo' => $imo,
            'eta' => $eta,
            'etd' => $etd,
            'status' => ShipStatus::Scheduled,
            'berth_code' => null,
            'has_pending_operations' => $dto->has_pending_operations,
        ]);
    }

    private function normalizeImo(string $imo): string
    {
        return trim($imo);
    }
}
