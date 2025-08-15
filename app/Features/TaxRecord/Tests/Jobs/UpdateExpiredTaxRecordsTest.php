<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Jobs;

use App\Features\Shared\Enums\QueueEnum;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Jobs\UpdateBulkExpiredTaxRecords;
use App\Features\TaxRecord\Jobs\UpdateExpiredTaxRecords;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class UpdateExpiredTaxRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_is_dispatched_to_correct_queue(): void
    {
        Queue::fake();

        UpdateExpiredTaxRecords::dispatch();

        Queue::assertPushed(UpdateExpiredTaxRecords::class, fn ($job): bool => $job->queue === QueueEnum::LongRunning->value);
    }

    public function test_handles_empty_expired_records(): void
    {
        Bus::fake();

        $user = User::factory()->create();

        // Create tax records that are NOT expired (valid_until is in future)
        TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::tomorrow(),
        ]);

        $job = new UpdateExpiredTaxRecords;
        $job->handle();

        // Should not create any batch jobs
        Bus::assertNothingBatched();
    }

    public function test_processes_expired_acknowledged_records_in_chunks(): void
    {
        Bus::fake();

        $user = User::factory()->create();

        // Create expired tax records with Acknowledged status
        $expired_records = TaxRecord::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        // Create non-expired records (should be ignored)
        TaxRecord::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::tomorrow(),
        ]);

        // Create expired records with different status (should be ignored)
        TaxRecord::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Cancelled,
            'valid_until' => Carbon::yesterday(),
        ]);

        $job = new UpdateExpiredTaxRecords;
        $job->handle();

        // Should create one batch with UpdateBulkExpiredTaxRecords job
        Bus::assertBatched(function ($batch) use ($expired_records): true {
            $jobs = $batch->jobs->toArray();
            $this->assertCount(1, $jobs);

            $bulk_job = $jobs[0];
            $this->assertInstanceOf(UpdateBulkExpiredTaxRecords::class, $bulk_job);
            $this->assertCount(5, $bulk_job->tax_record_ids);

            // Verify the job contains the correct record IDs
            $expected_ids = $expired_records->pluck('id')->sort()->values()->toArray();
            $actual_ids = collect($bulk_job->tax_record_ids)->sort()->values()->toArray();
            $this->assertEquals($expected_ids, $actual_ids);

            return true;
        });
    }

    public function test_creates_multiple_batches_for_large_datasets(): void
    {
        Bus::fake();

        $user = User::factory()->create();

        // Create 2500 expired tax records (should create 3 batches: 1000, 1000, 500)
        TaxRecord::factory()->count(2500)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $job = new UpdateExpiredTaxRecords;
        $job->handle();

        // Should create 3 batches
        Bus::assertBatchCount(3);

        // Each batch should contain one UpdateBulkExpiredTaxRecords job
        Bus::assertBatched(function ($batch): true {
            $jobs = $batch->jobs->toArray();
            $this->assertCount(1, $jobs);
            $this->assertInstanceOf(UpdateBulkExpiredTaxRecords::class, $jobs[0]);

            return true;
        });
    }

    public function test_batch_has_correct_configuration(): void
    {
        Bus::fake();

        $user = User::factory()->create();

        TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $job = new UpdateExpiredTaxRecords;
        $job->handle();

        Bus::assertBatched(function ($batch): true {
            // Check batch configuration
            $this->assertStringContainsString('Update Expired Tax Records Bulk Batch', $batch->name);

            return true;
        });
    }

    public function test_handles_batch_cancellation(): void
    {
        // Set up Bus fake before creating any data
        Bus::fake();

        $user = User::factory()->create();

        TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        // Create a mock batch that returns cancelled state
        /** @var \Mockery\MockInterface|\Illuminate\Bus\Batch $batch */
        $batch = \Mockery::mock(\Illuminate\Bus\Batch::class);
        $batch->shouldReceive('cancelled')->andReturn(true); // @phpstan-ignore-line

        // Create an anonymous job class that overrides the batch() method
        $job = new class extends UpdateExpiredTaxRecords
        {
            private ?\Illuminate\Bus\Batch $mockBatch = null;

            public function setMockBatch(mixed $batch): void
            {
                $this->mockBatch = $batch;
            }

            public function batch(): ?\Illuminate\Bus\Batch
            {
                return $this->mockBatch;
            }
        };

        $job->setMockBatch($batch);

        // Should exit early without processing
        $job->handle();

        Bus::assertNothingBatched();
    }

    public function test_ignores_already_expired_records(): void
    {
        Bus::fake();

        $user = User::factory()->create();

        // Create records that are expired but already have Expired status
        TaxRecord::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Expired,
            'valid_until' => Carbon::yesterday(),
        ]);

        $job = new UpdateExpiredTaxRecords;
        $job->handle();

        // Should not create any batch jobs since records are already Expired
        Bus::assertNothingBatched();
    }

    public function test_only_processes_records_valid_until_before_today(): void
    {
        Bus::fake();

        $user = User::factory()->create();

        // Create records expiring today (should not be processed)
        TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::today(),
        ]);

        // Create records expiring yesterday (should be processed)
        TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $job = new UpdateExpiredTaxRecords;
        $job->handle();

        // Should create one batch with one record
        Bus::assertBatched(function ($batch): true {
            $jobs = $batch->jobs->toArray();
            $this->assertCount(1, $jobs);

            $bulk_job = $jobs[0];
            $this->assertInstanceOf(UpdateBulkExpiredTaxRecords::class, $bulk_job);
            $this->assertCount(1, $bulk_job->tax_record_ids);

            return true;
        });
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
