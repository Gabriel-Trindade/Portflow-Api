<?php

namespace App\Modules\Containers\Application\Actions;

use App\Modules\Containers\Domain\Exceptions\ContainerNotFoundException;
use App\Modules\Containers\Domain\Repositories\ContainerRepositoryInterface;
use App\Modules\Containers\Infrastructure\Models\Container;

class GetContainerAction
{
    public function __construct(
        private readonly ContainerRepositoryInterface $repository,
    ) {}

    public function execute(int $id): Container
    {
        $container = $this->repository->findById($id);

        if (! $container) {
            throw new ContainerNotFoundException($id);
        }

        return $container;
    }
}
