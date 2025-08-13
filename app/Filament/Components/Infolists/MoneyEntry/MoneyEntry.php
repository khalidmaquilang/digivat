<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolists\MoneyEntry;

use Filament\Infolists\Components\TextEntry;

class MoneyEntry extends TextEntry
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
