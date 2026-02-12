<?php

namespace App\Modules\Ships\Http\Controllers;

use App\Modules\Ships\Application\Actions\CreateShipAction;
use App\Modules\Ships\Application\Actions\DockShipAction;
use App\Modules\Ships\Application\Actions\FinalizeShipAction;
use App\Modules\Ships\Application\Actions\GetShipAction;
use App\Modules\Ships\Application\Actions\ListShipsAction;
use App\Modules\Ships\Application\Actions\UpdateShipAction;
use App\Modules\Ships\Application\DTOs\CreateShipDTO;
use App\Modules\Ships\Application\DTOs\UpdateShipDTO;
use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Domain\Exceptions\BerthNotAvailableException;
use App\Modules\Ships\Domain\Exceptions\InvalidShipScheduleException;
use App\Modules\Ships\Domain\Exceptions\InvalidShipStateTransitionException;
use App\Modules\Ships\Domain\Exceptions\ShipHasPendingOperationsException;
use App\Modules\Ships\Domain\Exceptions\ShipImoAlreadyExistsException;
use App\Modules\Ships\Domain\Exceptions\ShipNotFoundException;
use App\Modules\Ships\Http\Requests\DockShipRequest;
use App\Modules\Ships\Http\Requests\StoreShipRequest;
use App\Modules\Ships\Http\Requests\UpdateShipRequest;
use App\Modules\Ships\Http\Resources\ShipResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ShipController extends Controller
{
    public function index(Request $request, ListShipsAction $action): AnonymousResourceCollection
    {
        $ships = $action->execute(
            perPage: (int) $request->query('per_page', 15),
            status: $request->query('status') ? ShipStatus::from($request->query('status')) : null,
            search: $request->query('search'),
        );

        return ShipResource::collection($ships);
    }

    public function store(StoreShipRequest $request, CreateShipAction $action): JsonResponse
    {
        try {
            $ship = $action->execute(
                new CreateShipDTO(...$request->validated()),
            );

            return (new ShipResource($ship))
                ->response()
                ->setStatusCode(201);
        } catch (ShipImoAlreadyExistsException) {
            return response()->json(['message' => 'A ship with this IMO already exists.'], 409);
        } catch (InvalidShipScheduleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(int $id, GetShipAction $action): JsonResponse
    {
        try {
            $ship = $action->execute($id);

            return (new ShipResource($ship))->response();
        } catch (ShipNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function update(
        int $id,
        UpdateShipRequest $request,
        UpdateShipAction $action,
    ): JsonResponse {
        try {
            $ship = $action->execute(
                $id,
                new UpdateShipDTO(...$request->validated()),
            );

            return (new ShipResource($ship))->response();
        } catch (ShipNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (ShipImoAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (InvalidShipScheduleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function dock(
        int $id,
        DockShipRequest $request,
        DockShipAction $action,
    ): JsonResponse {
        try {
            $ship = $action->execute($id, $request->validated('berth_code'));

            return (new ShipResource($ship))->response();
        } catch (ShipNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (InvalidShipStateTransitionException|BerthNotAvailableException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function finalize(int $id, FinalizeShipAction $action): JsonResponse
    {
        try {
            $ship = $action->execute($id);

            return (new ShipResource($ship))->response();
        } catch (ShipNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (ShipHasPendingOperationsException|InvalidShipStateTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
