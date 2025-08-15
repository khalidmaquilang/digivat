<?php

declare(strict_types=1);

namespace App\Features\TaxType;

use App\Features\TaxType\Interfaces\TaxInterface;

class Vat implements TaxInterface
{
    public function calculate(float $taxable_amount): float
    {
        if ($taxable_amount <= 0) {
            return 0;
        }

        return $taxable_amount * 0.12;
    }
}
