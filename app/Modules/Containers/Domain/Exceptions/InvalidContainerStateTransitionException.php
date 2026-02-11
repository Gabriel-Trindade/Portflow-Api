<?php

namespace App\Modules\Containers\Domain\Exceptions;

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use RuntimeException;

class InvalidContainerStateTransitionException extends RuntimeException
{
    public function __construct(ContainerStatus $from, ContainerStatus $to)
    {
        parent::__construct("Invalid container status transition from [{$from->value}] to [{$to->value}].");
    }
}
