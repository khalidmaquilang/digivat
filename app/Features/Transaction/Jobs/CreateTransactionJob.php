<?php

declare(strict_types=1);

namespace App\Features\Transaction\Jobs;

use App\Features\Business\Models\Business;
use App\Features\Shared\Enums\QueueEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Actions\CreateTransactionAction;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateTransactionJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $tax_record_id,
        public string $business_id,
        public TransactionTypeEnum $type = TransactionTypeEnum::TaxRemittance,
        public ?string $description = null,
        public array $metadata = []
    ) {
        $this->onQueue(QueueEnum::ShortRunning->value);
    }

    /**
     * Execute the job.
     */
    public function handle(CreateTransactionAction $create_transaction_action): void
    {
        $tax_record = TaxRecord::find($this->tax_record_id);
        $business = Business::find($this->business_id);

        if (! $tax_record || ! $business) {
            $this->fail('Tax record or business not found');

            return;
        }

        $create_transaction_action->handle(
            $tax_record,
            $business,
            $this->type,
            null, // reference_number
            $this->description,
            $this->metadata
        );
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'CreateTransactionJob',
        ];
    }

    public function backoff(): int
    {
        $base = 5 * 2 ** ($this->attempts() - 1); // Exponential

        return $base + random_int(1, 10); // Add randomness to each retry
    }
}
