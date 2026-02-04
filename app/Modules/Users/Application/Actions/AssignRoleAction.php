<?php

namespace App\Modules\Users\Application\Actions;

use App\Modules\Users\Domain\Enums\UserRole;
use App\Modules\Users\Domain\Exceptions\UserNotFoundException;
use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Infrastructure\Models\User;
use Illuminate\Validation\ValidationException;

class AssignRoleAction
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function execute(int $targetUserId, UserRole $role, User $currentUser): User
    {
        if ($currentUser->id === $targetUserId) {
            throw ValidationException::withMessages([
                'role' => ['You cannot change your own role.'],
            ]);
        }

        $targetUser = $this->repository->findById($targetUserId);

        if (! $targetUser) {
            throw new UserNotFoundException($targetUserId);
        }

        return $this->repository->update($targetUser, [
            'role' => $role,
        ]);
    }
}
