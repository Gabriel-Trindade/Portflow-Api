<?php

namespace App\Modules\Containers\Application\DTOs;

final readonly class UpdateContainerDTO
{
    public function __construct(
        public ?string $code = null,
        public ?string $type = null,
    ) {}
}
