<?php

namespace App\Modules\Users\Domain\Repositories;

use App\Modules\Users\Domain\Enums\UserRole;
use App\Modules\Users\Domain\Enums\UserStatus;
use App\Modules\Users\Infrastructure\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function existsByEmail(string $email, ?int $excludeId = null): bool;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function paginate(
        int $perPage = 15,
        ?UserRole $role = null,
        ?UserStatus $status = null,
        ?string $search = null,
    ): LengthAwarePaginator;
}
