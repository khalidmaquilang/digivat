<?php

declare(strict_types=1);

namespace App\Filament\Components\TableColumns\MoneyColumn;

use Filament\Tables\Columns\TextColumn;

class MoneyColumn extends TextColumn
{
    public static function make(?string $name = null): static
    {
        /** @var static $static */
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        $static->money('PHP');

        return $static;
    }
}
