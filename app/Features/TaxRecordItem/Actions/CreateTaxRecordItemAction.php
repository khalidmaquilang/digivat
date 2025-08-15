<?php

declare(strict_types=1);

namespace App\Features\TaxRecordItem\Actions;

use App\Features\TaxRecordItem\Data\TaxRecordItemData;
use App\Features\TaxRecordItem\Models\TaxRecordItem;

class CreateTaxRecordItemAction
{
    public function handle(TaxRecordItemData $data): TaxRecordItem
    {
        return TaxRecordItem::create($data->toArray());
    }
}
