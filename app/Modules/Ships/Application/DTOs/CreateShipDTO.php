<?php

namespace App\Modules\Ships\Application\DTOs;

final readonly class CreateShipDTO
{
    public function __construct(
        public string $name,
        public string $imo,
        public string $eta,
        public string $etd,
        public bool $has_pending_operations = false,
    ) {}
}
