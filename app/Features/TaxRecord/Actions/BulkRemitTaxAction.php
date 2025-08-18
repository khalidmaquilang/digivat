<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Jobs\CreateTransactionJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BulkRemitTaxAction
{
    /**
     * @param  Collection<int, TaxRecord>  $tax_records
     */
    public function handle(Collection $tax_records): int
    {
        if ($tax_records->isEmpty()) {
            return 0;
        }

        // Filter records that can be remitted
        $remittable_records = $tax_records
            ->filter(fn (TaxRecord $tax_record): bool => $tax_record->status === TaxRecordStatusEnum::Acknowledged ||
                $tax_record->status === TaxRecordStatusEnum::Expired
            );

        if ($remittable_records->isEmpty()) {
            return 0;
        }

        return DB::transaction(function () use ($remittable_records): int {
            $record_ids = $remittable_records->pluck('id')->toArray();

            // Perform bulk update using query builder for efficiency
            $updated_count = TaxRecord::whereIn('id', $record_ids)
                ->update([
                    'status' => TaxRecordStatusEnum::Paid,
                ]);

            // Dispatch jobs to create transaction records for each remitted tax record
            foreach ($remittable_records as $tax_record) {
                CreateTransactionJob::dispatch($tax_record->id, $tax_record->business_id);
            }

            return $updated_count;
        });
    }
}
