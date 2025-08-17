<?php

declare(strict_types=1);

namespace App\Features\User\Actions;

use App\Features\User\Models\User;

class GetUserByEmail
{
    public function handle(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
