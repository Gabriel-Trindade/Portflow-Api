<?php

namespace App\Modules\Containers\Application\DTOs;

final readonly class CreateContainerDTO
{
    public function __construct(
        public string $code,
        public string $type,
    ) {}
}
