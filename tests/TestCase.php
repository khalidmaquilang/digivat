<?php

namespace Tests;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function convertMoney(float|Money $amount): int
    {
        if ($amount instanceof Money) {
            return $amount->getMinorAmount()->toInt();
        }

        return Money::of(round($amount, 2, PHP_ROUND_HALF_DOWN), 'PHP', roundingMode: RoundingMode::DOWN)->getMinorAmount()->toInt();
    }
}
