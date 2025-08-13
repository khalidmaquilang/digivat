<?php

declare(strict_types=1);

namespace Features\User\Actions;

use Features\User\Models\User;

class CreateTokenAction
{
    public function handle(User $user, string $name): void
    {
        $user->tokens()->create([
            'name' => $name,
        ]);
    }
}
