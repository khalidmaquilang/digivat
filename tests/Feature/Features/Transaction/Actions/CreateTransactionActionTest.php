<?php

declare(strict_types=1);

namespace Tests\Feature\Features\Transaction\Actions;

use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Actions\CreateTransactionAction;
use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use App\Features\Transaction\Models\Transaction;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTransactionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_transaction_with_correct_data(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'tax_amount' => 1200.50,
            'gross_amount' => 10000.00,
            'taxable_amount' => 10000.00,
            'total_amount' => 11200.50,
            'transaction_reference' => 'TR-123456789',
        ]);

        $action = new CreateTransactionAction;

        // Act
        $transaction = $action->handle($taxRecord, $user);

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertTrue($transaction->exists);

        // Check database record
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'tax_record_id' => $taxRecord->id,
            'user_id' => $user->id,
            'amount' => '1200.50',
            'type' => TransactionTypeEnum::TaxRemittance->value,
            'status' => TransactionStatusEnum::Completed->value,
        ]);

        // Check field values
        $this->assertEquals($taxRecord->id, $transaction->tax_record_id);
        $this->assertEquals($user->id, $transaction->user_id);
        $this->assertEquals('1200.50', $transaction->amount);
        $this->assertEquals(TransactionTypeEnum::TaxRemittance, $transaction->type);
        $this->assertEquals(TransactionStatusEnum::Completed, $transaction->status);
        $this->assertStringStartsWith('TXN-', $transaction->reference_number);
        $this->assertNotNull($transaction->transaction_date);

        // Check metadata
        $expectedMetadata = [
            'tax_record_reference' => 'TR-123456789',
            'gross_amount' => 10000.00,
            'taxable_amount' => 10000.00,
            'tax_amount' => 1200.50,
            'total_amount' => 11200.50,
        ];
        $this->assertEquals($expectedMetadata, $transaction->metadata);
    }

    public function test_creates_transaction_with_custom_parameters(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create(['tax_amount' => 500.00]);

        $action = new CreateTransactionAction;
        $customDescription = 'Custom transaction description';
        $customMetadata = ['custom_field' => 'custom_value'];

        // Act
        $transaction = $action->handle(
            $taxRecord,
            $user,
            TransactionTypeEnum::Refund,
            $customDescription,
            $customMetadata
        );

        // Assert
        $this->assertEquals(TransactionTypeEnum::Refund, $transaction->type);
        $this->assertEquals($customDescription, $transaction->description);
        $this->assertArrayHasKey('custom_field', $transaction->metadata);
        $this->assertEquals('custom_value', $transaction->metadata['custom_field']);
    }

    public function test_relationships_work_correctly(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create();

        $action = new CreateTransactionAction;

        // Act
        $transaction = $action->handle($taxRecord, $user);

        // Assert relationships
        $this->assertTrue($transaction->taxRecord->is($taxRecord));
        $this->assertTrue($transaction->user->is($user));
    }

    public function test_reference_number_is_unique(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord1 = TaxRecord::factory()->create();
        $taxRecord2 = TaxRecord::factory()->create();

        $action = new CreateTransactionAction;

        // Act
        $transaction1 = $action->handle($taxRecord1, $user);
        $transaction2 = $action->handle($taxRecord2, $user);

        // Assert
        $this->assertNotEquals($transaction1->reference_number, $transaction2->reference_number);
        $this->assertStringStartsWith('TXN-', $transaction1->reference_number);
        $this->assertStringStartsWith('TXN-', $transaction2->reference_number);
    }
}
