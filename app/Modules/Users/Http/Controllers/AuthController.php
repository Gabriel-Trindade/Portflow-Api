<?php

namespace App\Modules\Users\Http\Controllers;

use App\Modules\Users\Application\Actions\AuthenticateUserAction;
use App\Modules\Users\Application\Actions\LogoutUserAction;
use App\Modules\Users\Application\Actions\RegisterUserAction;
use App\Modules\Users\Application\DTOs\LoginUserDTO;
use App\Modules\Users\Application\DTOs\RegisterUserDTO;
use App\Modules\Users\Domain\Exceptions\UserAlreadyExistsException;
use App\Modules\Users\Http\Requests\LoginRequest;
use App\Modules\Users\Http\Requests\RegisterRequest;
use App\Modules\Users\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        try {
            $user = $action->execute(
                new RegisterUserDTO(...$request->validated()),
            );

            return (new UserResource($user))
                ->response()
                ->setStatusCode(201);
        } catch (UserAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function login(LoginRequest $request, AuthenticateUserAction $action): JsonResponse
    {
        $result = $action->execute(
            new LoginUserDTO(...$request->validated()),
        );

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function logout(LogoutUserAction $action): JsonResponse
    {
        $action->execute($this->getAuthenticatedUser());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    private function getAuthenticatedUser(): \App\Modules\Users\Infrastructure\Models\User
    {
        return request()->user();
    }
}
