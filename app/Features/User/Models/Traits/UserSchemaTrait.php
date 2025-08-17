<?php

declare(strict_types=1);

namespace App\Features\User\Models\Traits;

use App\Filament\Components\Fields\TextInput\TextInput;

trait UserSchemaTrait
{
    /**
     * @return array<TextInput>
     */
    public static function nameSchema(): array
    {
        return [
            TextInput::make('first_name')
                ->required(),
            TextInput::make('middle_name'),
            TextInput::make('last_name')
                ->required(),
            TextInput::make('suffix'),
        ];
    }
}
