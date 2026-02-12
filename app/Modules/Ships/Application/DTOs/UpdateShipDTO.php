<?php

namespace App\Modules\Ships\Application\DTOs;

final readonly class UpdateShipDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $imo = null,
        public ?string $eta = null,
        public ?string $etd = null,
        public ?bool $has_pending_operations = null,
    ) {}
}
