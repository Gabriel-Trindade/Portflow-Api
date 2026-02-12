<?php

namespace App\Modules\Containers\Http\Controllers;

use App\Modules\Containers\Application\Actions\ChangeContainerStatusAction;
use App\Modules\Containers\Application\Actions\CreateContainerAction;
use App\Modules\Containers\Application\Actions\GetContainerAction;
use App\Modules\Containers\Application\Actions\ListContainersAction;
use App\Modules\Containers\Application\Actions\UpdateContainerAction;
use App\Modules\Containers\Application\DTOs\CreateContainerDTO;
use App\Modules\Containers\Application\DTOs\UpdateContainerDTO;
use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Enums\ContainerType;
use App\Modules\Containers\Domain\Exceptions\ContainerCodeAlreadyExistsException;
use App\Modules\Containers\Domain\Exceptions\ContainerNotFoundException;
use App\Modules\Containers\Domain\Exceptions\InvalidContainerStateTransitionException;
use App\Modules\Containers\Http\Requests\ChangeContainerStatusRequest;
use App\Modules\Containers\Http\Requests\StoreContainerRequest;
use App\Modules\Containers\Http\Requests\UpdateContainerRequest;
use App\Modules\Containers\Http\Resources\ContainerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ContainerController extends Controller
{
    public function index(Request $request, ListContainersAction $action): AnonymousResourceCollection
    {
        $containers = $action->execute(
            perPage: (int) $request->query('per_page', 15),
            type: $request->query('type') ? ContainerType::from($request->query('type')) : null,
            status: $request->query('status') ? ContainerStatus::from($request->query('status')) : null,
            search: $request->query('search'),
        );

        return ContainerResource::collection($containers);
    }

    public function store(StoreContainerRequest $request, CreateContainerAction $action): JsonResponse
    {
        try {
            $container = $action->execute(
                new CreateContainerDTO(...$request->validated()),
            );

            return (new ContainerResource($container))
                ->response()
                ->setStatusCode(201);
        } catch (ContainerCodeAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function show(int $id, GetContainerAction $action): JsonResponse
    {
        try {
            $container = $action->execute($id);

            return (new ContainerResource($container))->response();
        } catch (ContainerNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function update(
        int $id,
        UpdateContainerRequest $request,
        UpdateContainerAction $action,
    ): JsonResponse {
        try {
            $container = $action->execute(
                $id,
                new UpdateContainerDTO(...$request->validated()),
            );

            return (new ContainerResource($container))->response();
        } catch (ContainerNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (ContainerCodeAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function changeStatus(
        int $id,
        ChangeContainerStatusRequest $request,
        ChangeContainerStatusAction $action,
    ): JsonResponse {
        try {
            $container = $action->execute(
                $id,
                ContainerStatus::from($request->validated('status')),
            );

            return (new ContainerResource($container))->response();
        } catch (ContainerNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (InvalidContainerStateTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
