<?php

namespace App\Modules\Containers\Application\Actions;

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Enums\ContainerType;
use App\Modules\Containers\Domain\Repositories\ContainerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListContainersAction
{
    public function __construct(
        private readonly ContainerRepositoryInterface $repository,
    ) {}

    public function execute(
        int $perPage = 15,
        ?ContainerType $type = null,
        ?ContainerStatus $status = null,
        ?string $search = null,
    ): LengthAwarePaginator {
        return $this->repository->paginate($perPage, $type, $status, $search);
    }
}
