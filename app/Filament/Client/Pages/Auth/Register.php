<?php

declare(strict_types=1);

namespace App\Filament\Client\Pages\Auth;

use App\Features\User\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Schemas\Schema;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(array_merge(User::nameSchema(), [
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]));
    }
}
