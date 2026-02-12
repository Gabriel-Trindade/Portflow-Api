<?php

namespace App\Modules\Containers\Application\Actions;

use App\Modules\Containers\Application\DTOs\CreateContainerDTO;
use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Enums\ContainerType;
use App\Modules\Containers\Domain\Exceptions\ContainerCodeAlreadyExistsException;
use App\Modules\Containers\Domain\Repositories\ContainerRepositoryInterface;
use App\Modules\Containers\Infrastructure\Models\Container;

class CreateContainerAction
{
    public function __construct(
        private readonly ContainerRepositoryInterface $repository,
    ) {}

    public function execute(CreateContainerDTO $dto): Container
    {
        $code = $this->normalizeCode($dto->code);

        if ($this->repository->existsByCode($code)) {
            throw new ContainerCodeAlreadyExistsException($code);
        }

        return $this->repository->create([
            'code' => $code,
            'type' => ContainerType::from($dto->type),
            'status' => ContainerStatus::AwaitingUnloading,
        ]);
    }

    private function normalizeCode(string $code): string
    {
        return strtoupper(trim($code));
    }
}
