<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Features\User\Models\User;

abstract class Controller
{
    public function resolveUser(): ?User
    {
        /** @var ?User $user */
        $user = request()->user;

        return $user;
    }
}
