<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Jobs;

use App\Features\Shared\Enums\QueueEnum;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

class UpdateExpiredTaxRecords implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue(QueueEnum::LongRunning->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if this job should continue processing
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Find expired records in chunks to handle millions efficiently
        TaxRecord::query()
            ->where('valid_until', '<', Carbon::today())
            ->where('status', TaxRecordStatusEnum::Acknowledged)
            ->select('id')
            ->chunkById(1000, function (Collection $records): void {
                // Extract record IDs and create a single bulk update job for 1000 records
                $tax_record_ids = $records->pluck('id')->toArray();

                // Create a bulk update job that processes all 1000 records at once
                $bulk_job = new UpdateBulkExpiredTaxRecords($tax_record_ids);

                // Create a batch for this chunk of records
                Bus::batch([$bulk_job])
                    ->name('Update Expired Tax Records Bulk Batch - '.now()->format('Y-m-d H:i:s'))
                    ->onQueue(QueueEnum::ShortRunning->value)
                    ->allowFailures()
                    ->dispatch();
            });
    }
}
