<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Jobs;

use App\Features\Shared\Enums\QueueEnum;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Jobs\UpdateBulkExpiredTaxRecords;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class UpdateBulkExpiredTaxRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_is_dispatched_to_correct_queue(): void
    {
        Queue::fake();

        $tax_record_ids = ['1', '2', '3'];
        UpdateBulkExpiredTaxRecords::dispatch($tax_record_ids);

        Queue::assertPushed(UpdateBulkExpiredTaxRecords::class, fn ($job): bool => $job->queue === QueueEnum::ShortRunning->value);
    }

    public function test_updates_acknowledged_records_to_expired(): void
    {
        $user = User::factory()->create();

        // Create tax records with Acknowledged status
        $acknowledged_records = TaxRecord::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $tax_record_ids = $acknowledged_records->pluck('id')->toArray();
        $job = new UpdateBulkExpiredTaxRecords($tax_record_ids);
        $job->handle();

        // All records should be updated to Expired status
        foreach ($acknowledged_records as $record) {
            $this->assertDatabaseHas('tax_records', [
                'id' => $record->id,
                'status' => TaxRecordStatusEnum::Expired->value,
            ]);
        }
    }

    public function test_ignores_records_with_different_status(): void
    {
        $user = User::factory()->create();

        // Create tax records with different statuses
        $cancelled_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Cancelled,
            'valid_until' => Carbon::yesterday(),
        ]);

        $expired_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Expired,
            'valid_until' => Carbon::yesterday(),
        ]);

        $acknowledged_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $tax_record_ids = [$cancelled_record->id, $expired_record->id, $acknowledged_record->id];
        $job = new UpdateBulkExpiredTaxRecords($tax_record_ids);
        $job->handle();

        // Only the Acknowledged record should be updated
        $this->assertDatabaseHas('tax_records', [
            'id' => $cancelled_record->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
        ]);

        $this->assertDatabaseHas('tax_records', [
            'id' => $expired_record->id,
            'status' => TaxRecordStatusEnum::Expired->value,
        ]);

        $this->assertDatabaseHas('tax_records', [
            'id' => $acknowledged_record->id,
            'status' => TaxRecordStatusEnum::Expired->value,
        ]);
    }

    public function test_handles_empty_tax_record_ids_array(): void
    {
        $this->expectNotToPerformAssertions();

        $job = new UpdateBulkExpiredTaxRecords([]);

        // Should not throw any exceptions
        $job->handle();
    }

    public function test_handles_nonexistent_tax_record_ids(): void
    {
        $user = User::factory()->create();

        // Create one real record
        $real_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        // Include real record ID and non-existent IDs
        $tax_record_ids = [$real_record->id, 99999, 88888];
        $job = new UpdateBulkExpiredTaxRecords($tax_record_ids);
        $job->handle();

        // Only the real record should be updated
        $this->assertDatabaseHas('tax_records', [
            'id' => $real_record->id,
            'status' => TaxRecordStatusEnum::Expired->value,
        ]);
    }

    public function test_handles_large_batch_of_records(): void
    {
        $user = User::factory()->create();

        // Create 500 tax records with Acknowledged status
        $acknowledged_records = TaxRecord::factory()->count(500)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $tax_record_ids = $acknowledged_records->pluck('id')->toArray();
        $job = new UpdateBulkExpiredTaxRecords($tax_record_ids);
        $job->handle();

        // All records should be updated to Expired status
        $this->assertEquals(
            500,
            TaxRecord::where('status', TaxRecordStatusEnum::Expired)->count()
        );
    }

    public function test_handles_mixed_status_records(): void
    {
        $user = User::factory()->create();

        // Create records with various statuses
        $acknowledged_records = TaxRecord::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $cancelled_records = TaxRecord::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Cancelled,
            'valid_until' => Carbon::yesterday(),
        ]);

        $already_expired_records = TaxRecord::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Expired,
            'valid_until' => Carbon::yesterday(),
        ]);

        // Include all record IDs in the job
        $all_record_ids = $acknowledged_records->pluck('id')
            ->merge($cancelled_records->pluck('id'))
            ->merge($already_expired_records->pluck('id'))
            ->toArray();

        $job = new UpdateBulkExpiredTaxRecords($all_record_ids);
        $job->handle();

        // Only Acknowledged records should be updated to Expired
        $this->assertEquals(7, TaxRecord::where('status', TaxRecordStatusEnum::Expired)->count()); // 5 + 2 already expired
        $this->assertEquals(3, TaxRecord::where('status', TaxRecordStatusEnum::Cancelled)->count());
    }

    public function test_handles_batch_cancellation(): void
    {
        $user = User::factory()->create();

        $acknowledged_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        // Create a mock batch that returns cancelled state
        /** @var \Mockery\MockInterface|\Illuminate\Bus\Batch $batch */
        $batch = \Mockery::mock(\Illuminate\Bus\Batch::class);
        $batch->shouldReceive('cancelled')->andReturn(true); // @phpstan-ignore-line

        // Create an anonymous job class that overrides the batch() method
        $tax_record_ids = [$acknowledged_record->id];
        $job = new class($tax_record_ids) extends UpdateBulkExpiredTaxRecords
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

        $job->handle();

        // Record should not be updated due to batch cancellation
        $this->assertDatabaseHas('tax_records', [
            'id' => $acknowledged_record->id,
            'status' => TaxRecordStatusEnum::Acknowledged->value,
        ]);
    }

    public function test_job_constructor_sets_properties_correctly(): void
    {
        $tax_record_ids = ['1', '2', '3'];
        $job = new UpdateBulkExpiredTaxRecords($tax_record_ids);

        $this->assertEquals($tax_record_ids, $job->tax_record_ids);
        $this->assertEquals(QueueEnum::ShortRunning->value, $job->queue);
    }

    public function test_bulk_update_uses_correct_query_conditions(): void
    {
        $user = User::factory()->create();

        // Create records with different statuses and different users
        $user2 = User::factory()->create();

        $target_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $different_user_record = TaxRecord::factory()->create([
            'user_id' => $user2->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'valid_until' => Carbon::yesterday(),
        ]);

        $different_status_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Cancelled,
            'valid_until' => Carbon::yesterday(),
        ]);

        // Job should only update records that match both ID and Acknowledged status
        $tax_record_ids = [
            $target_record->id,
            $different_user_record->id,
            $different_status_record->id,
        ];

        $job = new UpdateBulkExpiredTaxRecords($tax_record_ids);
        $job->handle();

        // Only the target record and different_user_record should be updated (both have Acknowledged status)
        $this->assertDatabaseHas('tax_records', [
            'id' => $target_record->id,
            'status' => TaxRecordStatusEnum::Expired->value,
        ]);

        $this->assertDatabaseHas('tax_records', [
            'id' => $different_user_record->id,
            'status' => TaxRecordStatusEnum::Expired->value,
        ]);

        // Different status record should remain unchanged
        $this->assertDatabaseHas('tax_records', [
            'id' => $different_status_record->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
        ]);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
