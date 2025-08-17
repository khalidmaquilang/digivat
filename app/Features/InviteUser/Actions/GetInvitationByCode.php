<?php

declare(strict_types=1);

namespace App\Features\InviteUser\Actions;

use App\Features\InviteUser\Models\InviteUser;

class GetInvitationByCode
{
    public function handle(string $code): ?InviteUser
    {
        return InviteUser::where('code', $code)->first();
    }
}
