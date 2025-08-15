<?php

declare(strict_types=1);

namespace App\Features\TaxRecordItem\Actions;

use App\Features\TaxRecordItem\Data\TaxRecordItemData;

class CalculateTaxRecordItemAction
{
    /**
     * @param  array<TaxRecordItemData>  $items
     */
    public function handle(array $items): float
    {
        $sub_total = 0;

        foreach ($items as $item) {
            $sub_total += (($item->unit_price * $item->quantity) - $item->discount_amount);
        }

        if ($sub_total <= 0) {
            return 0;
        }

        return $sub_total;
    }
}
