<?php

use App\Modules\Users\Application\Actions\RegisterUserAction;
use App\Modules\Users\Application\DTOs\RegisterUserDTO;
use App\Modules\Users\Domain\Enums\UserRole;
use App\Modules\Users\Domain\Enums\UserStatus;
use App\Modules\Users\Domain\Exceptions\UserAlreadyExistsException;
use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Infrastructure\Models\User;

it('creates a user with operator role and active status', function () {
    $repository = Mockery::mock(UserRepositoryInterface::class);

    $repository->shouldReceive('existsByEmail')
        ->with('john@example.com')
        ->andReturn(false);

    $expectedUser = new User([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => UserRole::Operator,
        'status' => UserStatus::Active,
    ]);

    $repository->shouldReceive('create')
        ->withArgs(function (array $data) {
            return $data['name'] === 'John Doe'
                && $data['email'] === 'john@example.com'
                && $data['role'] === UserRole::Operator
                && $data['status'] === UserStatus::Active;
        })
        ->andReturn($expectedUser);

    $action = new RegisterUserAction($repository);

    $result = $action->execute(new RegisterUserDTO(
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password123',
    ));

    expect($result->name)->toBe('John Doe');
    expect($result->email)->toBe('john@example.com');
});

it('throws exception when email already exists', function () {
    $repository = Mockery::mock(UserRepositoryInterface::class);

    $repository->shouldReceive('existsByEmail')
        ->with('taken@example.com')
        ->andReturn(true);

    $action = new RegisterUserAction($repository);

    $action->execute(new RegisterUserDTO(
        name: 'Jane',
        email: 'taken@example.com',
        password: 'password123',
    ));
})->throws(UserAlreadyExistsException::class);
