<?php

namespace App\Modules\Ships\Domain\Exceptions;

use RuntimeException;

class InvalidShipScheduleException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('ETD must be greater than or equal to ETA.');
    }
}
