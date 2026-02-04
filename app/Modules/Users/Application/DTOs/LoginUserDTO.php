<?php

namespace App\Modules\Users\Application\DTOs;

final readonly class LoginUserDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
