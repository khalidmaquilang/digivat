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

class UpdateBulkExpiredTaxRecords implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3; // Or more

    public int $timeout = 120; // Avoid long-running DB locks

    /**
     * Create a new job instance.
     */
    /**
     * @param  array<int|string>  $tax_record_ids
     */
    public function __construct(public array $tax_record_ids)
    {
        $this->onQueue(QueueEnum::ShortRunning->value);
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

        // Update all records in bulk - only those that are still in Acknowledged status
        TaxRecord::query()
            ->whereIn('id', $this->tax_record_ids)
            ->where('status', TaxRecordStatusEnum::Acknowledged)
            ->update([
                'status' => TaxRecordStatusEnum::Expired,
            ]);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'UpdateBulkExpiredTaxRecords',
            'chunk_size:'.count($this->tax_record_ids),
        ];
    }

    public function backoff(): int
    {
        $base = 5 * 2 ** ($this->attempts() - 1); // Exponential

        return $base + random_int(1, 10); // Add randomness to each retry
    }
}
