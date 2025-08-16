<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Enums\CategoryTypeEnum;

class GetTaxByCategoryAction
{
    public function handle(CategoryTypeEnum $category, float $taxable_amount): float
    {
        $tax_class = $category->toTaxClass();

        return $tax_class->calculate($taxable_amount);
    }
}
