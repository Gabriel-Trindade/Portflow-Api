<?php

namespace App\Modules\Containers\Application\Actions;

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Exceptions\ContainerNotFoundException;
use App\Modules\Containers\Domain\Repositories\ContainerRepositoryInterface;
use App\Modules\Containers\Domain\Services\ContainerStateMachine;
use App\Modules\Containers\Infrastructure\Models\Container;

class ChangeContainerStatusAction
{
    public function __construct(
        private readonly ContainerRepositoryInterface $repository,
        private readonly ContainerStateMachine $stateMachine,
    ) {}

    public function execute(int $id, ContainerStatus $status): Container
    {
        $container = $this->repository->findById($id);

        if (! $container) {
            throw new ContainerNotFoundException($id);
        }

        $this->stateMachine->assertCanTransition($container->status, $status);

        return $this->repository->update($container, [
            'status' => $status,
        ]);
    }
}
