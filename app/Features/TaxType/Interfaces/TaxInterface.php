<?php

declare(strict_types=1);

namespace Features\TaxType\Interfaces;

interface TaxInterface
{
    public function calculate(float $taxable_amount): float;
}
