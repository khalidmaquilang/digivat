<?php

declare(strict_types=1);

namespace Features\Shared\Enums;

trait EnumArrayTrait
{
    /**
     * @return array<int, string|int>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
