<?php

namespace App\Modules\Ships\Domain\Services;

use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Domain\Exceptions\InvalidShipStateTransitionException;

final class ShipStateMachine
{
    private const TRANSITIONS = [
        'scheduled' => ['docked'],
        'docked' => ['finalized'],
        'finalized' => [],
    ];

    public function canTransition(ShipStatus $from, ShipStatus $to): bool
    {
        return in_array($to->value, self::TRANSITIONS[$from->value] ?? [], true);
    }

    public function assertCanTransition(ShipStatus $from, ShipStatus $to): void
    {
        if (! $this->canTransition($from, $to)) {
            throw new InvalidShipStateTransitionException($from, $to);
        }
    }
}
