<?php

declare(strict_types=1);

use App\Filament\Client\Pages\Auth\RegisterInvited;

Route::get('client/invite-user/register', RegisterInvited::class)
    ->name('filament.client.register.user-invite')
    ->middleware(['signed']);
