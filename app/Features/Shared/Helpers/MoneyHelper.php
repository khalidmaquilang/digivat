<?php

declare(strict_types=1);

namespace App\Features\Shared\Helpers;

use Illuminate\Support\Number;

class MoneyHelper
{
    /**
     * This will limit 2 decimals
     */
    public static function evaluate(float $amount): float
    {
        return round($amount, 2, PHP_ROUND_HALF_DOWN);
    }

    public static function currency(int|float $amount): false|string
    {
        $evaluated_amount = self::evaluate($amount);

        return Number::currency($evaluated_amount, 'PHP');
    }
}
