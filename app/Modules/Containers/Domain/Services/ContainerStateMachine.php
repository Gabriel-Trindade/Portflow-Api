<?php

namespace App\Modules\Containers\Domain\Services;

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Exceptions\InvalidContainerStateTransitionException;

final class ContainerStateMachine
{
    private const TRANSITIONS = [
        'awaiting_unloading' => ['unloaded'],
        'unloaded' => ['in_yard'],
        'in_yard' => ['released'],
        'released' => [],
    ];

    public function canTransition(ContainerStatus $from, ContainerStatus $to): bool
    {
        return in_array($to->value, self::TRANSITIONS[$from->value] ?? [], true);
    }

    public function assertCanTransition(ContainerStatus $from, ContainerStatus $to): void
    {
        if (! $this->canTransition($from, $to)) {
            throw new InvalidContainerStateTransitionException($from, $to);
        }
    }
}
