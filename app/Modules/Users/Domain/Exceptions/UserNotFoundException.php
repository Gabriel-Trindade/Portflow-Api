<?php

namespace App\Modules\Users\Domain\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("User with ID [{$id}] not found.");
    }
}
