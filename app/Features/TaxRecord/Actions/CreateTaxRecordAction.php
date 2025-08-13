<?php

declare(strict_types=1);

namespace Features\TaxRecord\Actions;

use Features\TaxRecord\Data\TaxRecordData;
use Features\TaxRecord\Models\TaxRecord;

class CreateTaxRecordAction
{
    public function handle(TaxRecordData $data): TaxRecord
    {
        return TaxRecord::create($data->toArray());
    }
}
