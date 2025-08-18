<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;

class RemitTaxAction
{
    public function __construct(protected UpdateTaxRecordStatusAction $action) {}

    public function handle(TaxRecord $tax_record): void
    {
        if ($tax_record->status !== TaxRecordStatusEnum::Acknowledged) {
            return;
        }

        $this->action->handle($tax_record, TaxRecordStatusEnum::Paid);
    }
}
