<?php

namespace App\Modules\Users\Application\Actions;

use App\Modules\Users\Application\DTOs\UpdateUserDTO;
use App\Modules\Users\Domain\Exceptions\UserAlreadyExistsException;
use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Infrastructure\Models\User;

class UpdateUserProfileAction
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function execute(User $user, UpdateUserDTO $dto): User
    {
        $data = array_filter([
            'name' => $dto->name,
            'email' => $dto->email,
        ], fn ($value) => $value !== null);

        if (isset($data['email']) && $this->repository->existsByEmail($data['email'], $user->id)) {
            throw new UserAlreadyExistsException($data['email']);
        }

        return $this->repository->update($user, $data);
    }
}
