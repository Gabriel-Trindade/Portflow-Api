<?php

namespace App\Modules\Ships\Domain\Exceptions;

use RuntimeException;

class ShipImoAlreadyExistsException extends RuntimeException
{
    public function __construct(string $imo)
    {
        parent::__construct("Ship with IMO [{$imo}] already exists.");
    }
}
