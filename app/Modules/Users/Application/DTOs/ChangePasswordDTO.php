<?php

namespace App\Modules\Users\Application\DTOs;

final readonly class ChangePasswordDTO
{
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
    ) {}
}
