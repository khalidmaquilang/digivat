<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;

class CancelTaxRecordAction
{
    public function handle(TaxRecord $tax_record): TaxRecord
    {
        if ($tax_record->status === TaxRecordStatusEnum::Cancelled) {
            return $tax_record;
        }

        $tax_record->update(['status' => TaxRecordStatusEnum::Cancelled]);

        return $tax_record->refresh();
    }
}
