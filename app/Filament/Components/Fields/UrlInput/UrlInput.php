<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields\UrlInput;

use Filament\Forms\Components\TextInput as BaseTextInput;

class UrlInput extends BaseTextInput
{
    public static function make(?string $name = null): static
    {
        /** @var static $static */
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        $static->url();

        return $static;
    }
}
