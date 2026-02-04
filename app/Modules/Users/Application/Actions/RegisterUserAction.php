<?php

namespace App\Modules\Users\Application\Actions;

use App\Modules\Users\Application\DTOs\RegisterUserDTO;
use App\Modules\Users\Domain\Enums\UserRole;
use App\Modules\Users\Domain\Enums\UserStatus;
use App\Modules\Users\Domain\Exceptions\UserAlreadyExistsException;
use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Infrastructure\Models\User;

class RegisterUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function execute(RegisterUserDTO $dto): User
    {
        if ($this->repository->existsByEmail($dto->email)) {
            throw new UserAlreadyExistsException($dto->email);
        }

        return $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
            'role' => UserRole::Operator,
            'status' => UserStatus::Active,
        ]);
    }
}
