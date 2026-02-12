<?php

namespace App\Modules\Ships\Domain\Exceptions;

use RuntimeException;

class BerthNotAvailableException extends RuntimeException
{
    public function __construct(string $berthCode)
    {
        parent::__construct("Berth [{$berthCode}] is not available.");
    }
}
