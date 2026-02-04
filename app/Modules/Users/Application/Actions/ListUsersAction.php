<?php

namespace App\Modules\Users\Application\Actions;

use App\Modules\Users\Domain\Enums\UserRole;
use App\Modules\Users\Domain\Enums\UserStatus;
use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsersAction
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function execute(
        int $perPage = 15,
        ?UserRole $role = null,
        ?UserStatus $status = null,
        ?string $search = null,
    ): LengthAwarePaginator {
        return $this->repository->paginate($perPage, $role, $status, $search);
    }
}
