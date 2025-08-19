<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use Illuminate\Database\Eloquent\Collection;

class BulkCancelTaxRecordAction
{
    /**
     * @param  Collection<int, TaxRecord>  $tax_records
     */
    public function handle(Collection $tax_records, string $cancel_reason): int
    {
        if ($tax_records->isEmpty()) {
            return 0;
        }

        // Extract IDs of records that are acknowledged status
        $record_ids = $tax_records
            ->filter(fn (TaxRecord $tax_record): bool => $tax_record->status === TaxRecordStatusEnum::Acknowledged)
            ->pluck('id')
            ->toArray();

        if (empty($record_ids)) {
            return 0;
        }

        // Perform bulk update using query builder for efficiency
        return TaxRecord::whereIn('id', $record_ids)
            ->update([
                'status' => TaxRecordStatusEnum::Cancelled,
                'cancel_reason' => $cancel_reason,
            ]);
    }
}
