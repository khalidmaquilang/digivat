<?php

declare(strict_types=1);

namespace App\Features\TaxType\Interfaces;

interface TaxInterface
{
    public function calculate(float $taxable_amount): float;
}
