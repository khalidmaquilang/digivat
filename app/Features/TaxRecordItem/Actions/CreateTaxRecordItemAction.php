<?php

declare(strict_types=1);

namespace Features\TaxRecordItem\Actions;

use Features\TaxRecordItem\Data\TaxRecordItemData;
use Features\TaxRecordItem\Models\TaxRecordItem;

class CreateTaxRecordItemAction
{
    public function handle(TaxRecordItemData $data): TaxRecordItem
    {
        return TaxRecordItem::create($data->toArray());
    }
}
