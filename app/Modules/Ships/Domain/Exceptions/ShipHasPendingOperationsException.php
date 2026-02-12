<?php

namespace App\Modules\Ships\Domain\Exceptions;

use RuntimeException;

class ShipHasPendingOperationsException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Ship with ID [{$id}] has pending operations.");
    }
}
