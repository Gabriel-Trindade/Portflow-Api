<?php

namespace App\Modules\Containers\Domain\Exceptions;

use RuntimeException;

class ContainerNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Container with ID [{$id}] not found.");
    }
}
