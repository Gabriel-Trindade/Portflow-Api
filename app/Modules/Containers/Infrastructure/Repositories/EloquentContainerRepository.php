<?php

namespace App\Modules\Containers\Infrastructure\Repositories;

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Enums\ContainerType;
use App\Modules\Containers\Domain\Repositories\ContainerRepositoryInterface;
use App\Modules\Containers\Infrastructure\Models\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentContainerRepository implements ContainerRepositoryInterface
{
    public function findById(int $id): ?Container
    {
        return Container::find($id);
    }

    public function findByCode(string $code): ?Container
    {
        return Container::where('code', $code)->first();
    }

    public function existsByCode(string $code, ?int $excludeId = null): bool
    {
        return Container::where('code', $code)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->exists();
    }

    public function create(array $data): Container
    {
        return Container::create($data);
    }

    public function update(Container $container, array $data): Container
    {
        $container->update($data);

        return $container->refresh();
    }

    public function paginate(
        int $perPage = 15,
        ?ContainerType $type = null,
        ?ContainerStatus $status = null,
        ?string $search = null,
    ): LengthAwarePaginator {
        return Container::query()
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($search, fn ($query) => $query->where('code', 'like', "%{$search}%"))
            ->orderBy('code')
            ->paginate($perPage);
    }
}
