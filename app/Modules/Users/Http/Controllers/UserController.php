<?php

namespace App\Modules\Users\Http\Controllers;

use App\Modules\Users\Application\Actions\AssignRoleAction;
use App\Modules\Users\Application\Actions\ChangePasswordAction;
use App\Modules\Users\Application\Actions\GetUserProfileAction;
use App\Modules\Users\Application\Actions\ListUsersAction;
use App\Modules\Users\Application\Actions\UpdateUserProfileAction;
use App\Modules\Users\Application\DTOs\ChangePasswordDTO;
use App\Modules\Users\Application\DTOs\UpdateUserDTO;
use App\Modules\Users\Domain\Enums\UserRole;
use App\Modules\Users\Domain\Enums\UserStatus;
use App\Modules\Users\Domain\Exceptions\UserAlreadyExistsException;
use App\Modules\Users\Domain\Exceptions\UserNotFoundException;
use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Http\Requests\AssignRoleRequest;
use App\Modules\Users\Http\Requests\ChangePasswordRequest;
use App\Modules\Users\Http\Requests\UpdateUserRequest;
use App\Modules\Users\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function profile(GetUserProfileAction $action): UserResource
    {
        $user = $action->execute(request()->user()->id);

        return new UserResource($user);
    }

    public function updateProfile(
        UpdateUserRequest $request,
        UpdateUserProfileAction $action,
    ): JsonResponse {
        try {
            $user = $action->execute(
                $request->user(),
                new UpdateUserDTO(...$request->validated()),
            );

            return (new UserResource($user))
                ->response();
        } catch (UserAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function changePassword(
        ChangePasswordRequest $request,
        ChangePasswordAction $action,
    ): JsonResponse {
        $action->execute(
            $request->user(),
            new ChangePasswordDTO(
                currentPassword: $request->validated('current_password'),
                newPassword: $request->validated('new_password'),
            ),
        );

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function index(Request $request, ListUsersAction $action): AnonymousResourceCollection
    {
        $users = $action->execute(
            perPage: (int) $request->query('per_page', 15),
            role: $request->query('role') ? UserRole::from($request->query('role')) : null,
            status: $request->query('status') ? UserStatus::from($request->query('status')) : null,
            search: $request->query('search'),
        );

        return UserResource::collection($users);
    }

    public function show(int $id, GetUserProfileAction $action): JsonResponse
    {
        try {
            $user = $action->execute($id);

            return (new UserResource($user))->response();
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function assignRole(
        int $id,
        AssignRoleRequest $request,
        AssignRoleAction $action,
    ): JsonResponse {
        try {
            $user = $action->execute(
                targetUserId: $id,
                role: UserRole::from($request->validated('role')),
                currentUser: $request->user(),
            );

            return (new UserResource($user))->response();
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function deactivate(
        int $id,
        UserRepositoryInterface $repository,
    ): JsonResponse {
        $user = $repository->findById($id);

        if (! $user) {
            return response()->json(['message' => "User with ID [{$id}] not found."], 404);
        }

        if ($user->id === request()->user()->id) {
            return response()->json(['message' => 'You cannot deactivate your own account.'], 422);
        }

        $repository->update($user, ['status' => UserStatus::Inactive]);

        return response()->json(['message' => 'User deactivated successfully.']);
    }
}
