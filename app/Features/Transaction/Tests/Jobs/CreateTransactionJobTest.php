<?php

declare(strict_types=1);

namespace App\Features\Transaction\Tests\Jobs;

use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use App\Features\Transaction\Jobs\CreateTransactionJob;
use App\Features\Transaction\Models\Transaction;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class CreateTransactionJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_creates_transaction_correctly(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'tax_amount' => 1500.00,
            'transaction_reference' => 'TR-TEST-123',
        ]);

        // Act
        $job = new CreateTransactionJob($taxRecord->id, $user->id);
        $job->handle(app(\App\Features\Transaction\Actions\CreateTransactionAction::class));

        // Assert
        $this->assertDatabaseHas('transactions', [
            'tax_record_id' => $taxRecord->id,
            'user_id' => $user->id,
            'amount' => '1500.00',
            'type' => TransactionTypeEnum::TaxRemittance->value,
            'status' => TransactionStatusEnum::Completed->value,
        ]);

        $transaction = Transaction::where('tax_record_id', $taxRecord->id)->first();
        $this->assertNotNull($transaction);
        $this->assertStringStartsWith('TXN-', $transaction->reference_number);
    }

    public function test_job_creates_transaction_with_custom_parameters(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create(['tax_amount' => 750.50]);
        $customDescription = 'Custom refund transaction';
        $customMetadata = ['refund_reason' => 'overpayment'];

        // Act
        $job = new CreateTransactionJob(
            $taxRecord->id,
            $user->id,
            TransactionTypeEnum::Refund,
            $customDescription,
            $customMetadata
        );
        $job->handle(app(\App\Features\Transaction\Actions\CreateTransactionAction::class));

        // Assert
        $transaction = Transaction::where('tax_record_id', $taxRecord->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(TransactionTypeEnum::Refund, $transaction->type);
        $this->assertEquals($customDescription, $transaction->description);
        $this->assertArrayHasKey('refund_reason', $transaction->metadata);
        $this->assertEquals('overpayment', $transaction->metadata['refund_reason']);
    }

    public function test_job_has_correct_queue_configuration(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create();

        // Act
        $job = new CreateTransactionJob($taxRecord->id, $user->id);

        // Assert
        $this->assertEquals('transactions', $job->queue);
        $this->assertEquals(3, $job->tries);
        $this->assertEquals(60, $job->timeout);
    }

    public function test_job_dispatch_queues_correctly(): void
    {
        // Arrange
        Queue::fake();
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create();

        // Act
        CreateTransactionJob::dispatch($taxRecord->id, $user->id);

        // Assert
        Queue::assertPushed(CreateTransactionJob::class, fn ($job): bool => $job->taxRecordId === $taxRecord->id &&
               $job->userId === $user->id &&
               $job->type === TransactionTypeEnum::TaxRemittance &&
               $job->description === null &&
               $job->metadata === []);
    }

    public function test_job_dispatch_with_custom_parameters(): void
    {
        // Arrange
        Queue::fake();
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create();
        $customDescription = 'Test description';
        $customMetadata = ['test' => 'data'];

        // Act
        CreateTransactionJob::dispatch(
            $taxRecord->id,
            $user->id,
            TransactionTypeEnum::Adjustment,
            $customDescription,
            $customMetadata
        );

        // Assert
        Queue::assertPushed(CreateTransactionJob::class, fn ($job): bool => $job->taxRecordId === $taxRecord->id &&
               $job->userId === $user->id &&
               $job->type === TransactionTypeEnum::Adjustment &&
               $job->description === $customDescription &&
               $job->metadata === $customMetadata);
    }

    public function test_job_handles_missing_tax_record(): void
    {
        // Arrange
        $user = User::factory()->create();
        $nonExistentTaxRecordId = 'non-existent-id';

        // Act
        $job = new CreateTransactionJob($nonExistentTaxRecordId, $user->id);
        $job->handle(app(\App\Features\Transaction\Actions\CreateTransactionAction::class));

        // Assert - no transaction should be created
        $this->assertDatabaseMissing('transactions', [
            'tax_record_id' => $nonExistentTaxRecordId,
            'user_id' => $user->id,
        ]);
    }

    public function test_job_handles_missing_user(): void
    {
        // Arrange
        $taxRecord = TaxRecord::factory()->create();
        $nonExistentUserId = 'non-existent-id';

        // Act
        $job = new CreateTransactionJob($taxRecord->id, $nonExistentUserId);
        $job->handle(app(\App\Features\Transaction\Actions\CreateTransactionAction::class));

        // Assert - no transaction should be created
        $this->assertDatabaseMissing('transactions', [
            'tax_record_id' => $taxRecord->id,
            'user_id' => $nonExistentUserId,
        ]);
    }
}
