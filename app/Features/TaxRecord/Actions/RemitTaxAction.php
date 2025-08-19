<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Jobs\CreateTransactionJob;

class RemitTaxAction
{
    public function __construct(
        protected UpdateTaxRecordStatusAction $updateTaxRecordStatusAction
    ) {}

    public function handle(TaxRecord $tax_record): void
    {
        if ($tax_record->status === TaxRecordStatusEnum::Cancelled || $tax_record->status === TaxRecordStatusEnum::Paid) {
            return;
        }

        // Update tax record status to paid
        $this->updateTaxRecordStatusAction->handle($tax_record, TaxRecordStatusEnum::Paid);

        CreateTransactionJob::dispatch($tax_record->id, $tax_record->business_id);
    }
}
