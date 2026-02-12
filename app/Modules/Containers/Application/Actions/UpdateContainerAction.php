<?php

namespace App\Modules\Containers\Application\Actions;

use App\Modules\Containers\Application\DTOs\UpdateContainerDTO;
use App\Modules\Containers\Domain\Enums\ContainerType;
use App\Modules\Containers\Domain\Exceptions\ContainerCodeAlreadyExistsException;
use App\Modules\Containers\Domain\Exceptions\ContainerNotFoundException;
use App\Modules\Containers\Domain\Repositories\ContainerRepositoryInterface;
use App\Modules\Containers\Infrastructure\Models\Container;

class UpdateContainerAction
{
    public function __construct(
        private readonly ContainerRepositoryInterface $repository,
    ) {}

    public function execute(int $id, UpdateContainerDTO $dto): Container
    {
        $container = $this->repository->findById($id);

        if (! $container) {
            throw new ContainerNotFoundException($id);
        }

        $data = [];

        if ($dto->code !== null) {
            $code = $this->normalizeCode($dto->code);

            if ($this->repository->existsByCode($code, $container->id)) {
                throw new ContainerCodeAlreadyExistsException($code);
            }

            $data['code'] = $code;
        }

        if ($dto->type !== null) {
            $data['type'] = ContainerType::from($dto->type);
        }

        if ($data === []) {
            return $container;
        }

        return $this->repository->update($container, $data);
    }

    private function normalizeCode(string $code): string
    {
        return strtoupper(trim($code));
    }
}
