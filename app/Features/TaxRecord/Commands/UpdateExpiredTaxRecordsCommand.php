<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Commands;

use App\Features\TaxRecord\Jobs\UpdateExpiredTaxRecords;
use Illuminate\Console\Command;

class UpdateExpiredTaxRecordsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tax-record:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue job with batch processing to update expired tax records with Acknowledged status based on valid_until field';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Dispatching job to update expired tax records...');

        UpdateExpiredTaxRecords::dispatch();

        $this->info('Job dispatched successfully to long_running queue.');
        $this->comment('The job will process all Acknowledged tax records where valid_until is before today.');
        $this->comment('Records will be processed in batches of 1000 for optimal performance with millions of records.');

        return Command::SUCCESS;
    }
}
