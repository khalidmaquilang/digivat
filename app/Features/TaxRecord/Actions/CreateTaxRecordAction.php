<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Data\TaxRecordData;
use App\Features\TaxRecord\Models\TaxRecord;

class CreateTaxRecordAction
{
    public function handle(TaxRecordData $data): TaxRecord
    {
        return TaxRecord::create($data->toArray());
    }
}
