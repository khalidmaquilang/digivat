<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecordItem\Actions\CalculateTaxRecordItemAction;
use App\Features\TaxRecordItem\Data\TaxRecordItemData;

class GetTotalGrossAmountAction
{
    public function __construct(protected CalculateTaxRecordItemAction $action) {}

    public function handle(array $items): float
    {
        if (count($items) <= 0) {
            return 0;
        }

        $first_element = reset($items);
        if ($first_element instanceof TaxRecordItemData) {
            return $this->action->handle($items);
        }

        $removed_keys = array_values($items);

        $items = TaxRecordItemData::collect($removed_keys);

        return $this->action->handle($items);
    }
}
