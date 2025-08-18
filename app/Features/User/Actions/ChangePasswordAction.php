<?php

declare(strict_types=1);

namespace App\Features\User\Actions;

use App\Features\User\Models\User;

class ChangePasswordAction
{
    public function handle(User $user, string $password): void
    {
        $user->update(['password' => bcrypt($password)]);
    }
}
