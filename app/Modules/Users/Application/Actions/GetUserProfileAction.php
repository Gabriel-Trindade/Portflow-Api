<?php

namespace App\Modules\Users\Application\Actions;

use App\Modules\Users\Domain\Exceptions\UserNotFoundException;
use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Infrastructure\Models\User;

class GetUserProfileAction
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function execute(int $userId): User
    {
        $user = $this->repository->findById($userId);

        if (! $user) {
            throw new UserNotFoundException($userId);
        }

        return $user;
    }
}
