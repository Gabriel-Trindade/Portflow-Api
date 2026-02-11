<?php

namespace App\Modules\Containers\Domain\Exceptions;

use RuntimeException;

class ContainerCodeAlreadyExistsException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Container with code [{$code}] already exists.");
    }
}
