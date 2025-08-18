<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;

class CancelTaxRecordAction
{
    public function __construct(protected UpdateTaxRecordStatusAction $action) {}

    public function handle(TaxRecord $tax_record, string $cancel_reason): TaxRecord
    {
        if ($tax_record->status !== TaxRecordStatusEnum::Acknowledged) {
            return $tax_record;
        }

        $this->action->handle($tax_record, TaxRecordStatusEnum::Cancelled, $cancel_reason);

        return $tax_record->refresh();
    }
}
