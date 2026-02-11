<?php

namespace App\Modules\Containers\Domain\Repositories;

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Enums\ContainerType;
use App\Modules\Containers\Infrastructure\Models\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContainerRepositoryInterface
{
    public function findById(int $id): ?Container;

    public function findByCode(string $code): ?Container;

    public function existsByCode(string $code, ?int $excludeId = null): bool;

    public function create(array $data): Container;

    public function update(Container $container, array $data): Container;

    public function paginate(
        int $perPage = 15,
        ?ContainerType $type = null,
        ?ContainerStatus $status = null,
        ?string $search = null,
    ): LengthAwarePaginator;
}
