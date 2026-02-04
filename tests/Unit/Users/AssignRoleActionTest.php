<?php

use App\Modules\Users\Application\Actions\AssignRoleAction;
use App\Modules\Users\Domain\Enums\UserRole;
use App\Modules\Users\Domain\Enums\UserStatus;
use App\Modules\Users\Domain\Exceptions\UserNotFoundException;
use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Infrastructure\Models\User;
use Illuminate\Validation\ValidationException;

it('assigns a role to a target user', function () {
    $repository = Mockery::mock(UserRepositoryInterface::class);

    $currentUser = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $currentUser->id = 1;

    $targetUser = new User(['name' => 'Operator', 'email' => 'op@test.com']);
    $targetUser->id = 2;

    $updatedUser = new User(['name' => 'Operator', 'email' => 'op@test.com', 'role' => UserRole::Viewer]);
    $updatedUser->id = 2;

    $repository->shouldReceive('findById')->with(2)->andReturn($targetUser);
    $repository->shouldReceive('update')
        ->with($targetUser, ['role' => UserRole::Viewer])
        ->andReturn($updatedUser);

    $action = new AssignRoleAction($repository);
    $result = $action->execute(2, UserRole::Viewer, $currentUser);

    expect($result->role)->toBe(UserRole::Viewer);
});

it('throws exception when changing own role', function () {
    $repository = Mockery::mock(UserRepositoryInterface::class);

    $currentUser = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $currentUser->id = 1;

    $action = new AssignRoleAction($repository);
    $action->execute(1, UserRole::Operator, $currentUser);
})->throws(ValidationException::class);

it('throws exception when target user not found', function () {
    $repository = Mockery::mock(UserRepositoryInterface::class);

    $currentUser = new User(['name' => 'Admin', 'email' => 'admin@test.com']);
    $currentUser->id = 1;

    $repository->shouldReceive('findById')->with(999)->andReturn(null);

    $action = new AssignRoleAction($repository);
    $action->execute(999, UserRole::Viewer, $currentUser);
})->throws(UserNotFoundException::class);
