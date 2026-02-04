<?php

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Exceptions\InvalidContainerStateTransitionException;
use App\Modules\Containers\Domain\Services\ContainerStateMachine;

it('allows valid container status transitions', function () {
    $machine = new ContainerStateMachine();

    expect($machine->canTransition(ContainerStatus::AwaitingUnloading, ContainerStatus::Unloaded))->toBeTrue();
    expect($machine->canTransition(ContainerStatus::Unloaded, ContainerStatus::InYard))->toBeTrue();
    expect($machine->canTransition(ContainerStatus::InYard, ContainerStatus::Released))->toBeTrue();
});

it('rejects invalid container status transitions', function () {
    $machine = new ContainerStateMachine();

    $machine->assertCanTransition(ContainerStatus::AwaitingUnloading, ContainerStatus::Released);
})->throws(InvalidContainerStateTransitionException::class);
