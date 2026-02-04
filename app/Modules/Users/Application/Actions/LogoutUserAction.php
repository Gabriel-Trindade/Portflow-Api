<?php

namespace App\Modules\Users\Application\Actions;

use App\Modules\Users\Infrastructure\Models\User;

class LogoutUserAction
{
    public function execute(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
