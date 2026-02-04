<?php

namespace App\Modules\Users\Application\DTOs;

final readonly class UpdateUserDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
    ) {}
}
