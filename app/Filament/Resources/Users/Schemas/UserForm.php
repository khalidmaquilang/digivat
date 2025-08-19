<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Components\Fields\TextInput\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('suffix'),
                TextInput::make('email')
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('email_verified_at'),
            ]);
    }
}
