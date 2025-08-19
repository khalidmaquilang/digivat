<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;

class UpdateTaxRecordStatusAction
{
    public function handle(TaxRecord $tax_record, TaxRecordStatusEnum $status, ?string $cancel_reason = null): void
    {
        $tax_record->update([
            'status' => $status,
            'cancel_reason' => $cancel_reason ?? $tax_record->cancel_reason,
        ]);
    }
}
