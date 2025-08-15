<?php

declare(strict_types=1);

namespace App\Features\Shared\Helpers;

class MoneyHelper
{
    /**
     * This will limit 2 decimals
     */
    public static function evaluate(float $amount): float
    {
        return round($amount, 2, PHP_ROUND_HALF_DOWN);
    }
}
