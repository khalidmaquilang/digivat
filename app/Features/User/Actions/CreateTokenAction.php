<?php

declare(strict_types=1);

namespace App\Features\User\Actions;

use App\Features\User\Models\User;

class CreateTokenAction
{
    public function handle(User $user, string $name): void
    {
        $user->tokens()->create([
            'name' => $name,
        ]);
    }
}
