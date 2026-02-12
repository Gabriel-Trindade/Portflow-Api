<?php

namespace App\Modules\Ships\Domain\Exceptions;

use App\Modules\Ships\Domain\Enums\ShipStatus;
use RuntimeException;

class InvalidShipStateTransitionException extends RuntimeException
{
    public function __construct(ShipStatus $from, ShipStatus $to)
    {
        parent::__construct("Invalid ship status transition from [{$from->value}] to [{$to->value}].");
    }
}
