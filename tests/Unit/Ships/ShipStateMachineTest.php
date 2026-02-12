<?php

use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Domain\Exceptions\InvalidShipStateTransitionException;
use App\Modules\Ships\Domain\Services\ShipStateMachine;

it('allows valid ship status transitions', function () {
    $machine = new ShipStateMachine();

    expect($machine->canTransition(ShipStatus::Scheduled, ShipStatus::Docked))->toBeTrue();
    expect($machine->canTransition(ShipStatus::Docked, ShipStatus::Finalized))->toBeTrue();
});

it('rejects invalid ship status transitions', function () {
    $machine = new ShipStateMachine();

    $machine->assertCanTransition(ShipStatus::Scheduled, ShipStatus::Finalized);
})->throws(InvalidShipStateTransitionException::class);
