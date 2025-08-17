<?php

declare(strict_types=1);

use App\Features\InviteUser\Filament\Pages\RegisterInvited;

Route::get('invite-user/register', RegisterInvited::class)
    ->name('register.user-invite')
    ->middleware(['signed']);
