<?php

declare(strict_types=1);

namespace App\Features\User\Actions;

use App\Features\Business\Models\Business;
use App\Features\User\Models\User;

class KickUserFromCompanyAction
{
    public function handle(Business $business, User $user): void
    {
        $business->members()->detach($user);
    }
}
