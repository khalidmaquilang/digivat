<?php

declare(strict_types=1);

namespace App\Features\Transaction\Tests\Actions;

use App\Features\Business\Models\Business;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Actions\CreateTransactionAction;
use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use App\Features\Transaction\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateTransactionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_transaction_with_correct_data(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'tax_amount' => 1200.50,
            'gross_amount' => 10000.00,
            'taxable_amount' => 10000.00,
            'total_amount' => 11200.50,
            'transaction_reference' => 'TR-123456789',
        ]);

        $action = app(CreateTransactionAction::class);

        // Act
        $transaction = $action->handle($tax_record, $business);

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertTrue($transaction->exists);

        // Check database record
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'tax_record_id' => $tax_record->id,
            'business_id' => $business->id,
            'amount' => $this->convertMoney(1200.50),
            'type' => TransactionTypeEnum::TaxRemittance->value,
            'status' => TransactionStatusEnum::Completed->value,
        ]);

        // Check field values
        $this->assertEquals($tax_record->id, $transaction->tax_record_id);
        $this->assertEquals($business->id, $transaction->business_id);
        $this->assertEquals('1200.50', $transaction->amount);
        $this->assertEquals(TransactionTypeEnum::TaxRemittance, $transaction->type);
        $this->assertEquals(TransactionStatusEnum::Completed, $transaction->status);
        $this->assertStringStartsWith('TXN-', $transaction->reference_number);
        $this->assertNotNull($transaction->transaction_date);

        // Check metadata
        $expected_metadata = [
            'tax_record_reference' => 'TR-123456789',
            'gross_amount' => 10000.00,
            'taxable_amount' => 10000.00,
            'tax_amount' => 1200.50,
            'total_amount' => 11200.50,
        ];
        $this->assertEquals($expected_metadata, $transaction->metadata);
    }

    public function test_creates_transaction_with_custom_parameters(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create(['tax_amount' => 500.00]);

        $action = app(CreateTransactionAction::class);
        $custom_description = 'Custom transaction description';
        $custom_metadata = ['custom_field' => 'custom_value'];

        // Act
        $transaction = $action->handle(
            $tax_record,
            $business,
            TransactionTypeEnum::Refund,
            null, // reference_number
            $custom_description,
            $custom_metadata
        );

        // Assert
        $this->assertEquals(TransactionTypeEnum::Refund, $transaction->type);
        $this->assertEquals($custom_description, $transaction->description);
        $this->assertIsArray($transaction->metadata);
        $this->assertArrayHasKey('custom_field', $transaction->metadata);
        $this->assertEquals('custom_value', $transaction->metadata['custom_field']);
    }

    public function test_relationships_work_correctly(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create();

        $action = app(CreateTransactionAction::class);

        // Act
        $transaction = $action->handle($tax_record, $business);

        // Assert relationships
        $this->assertTrue($transaction->taxRecord->is($tax_record));
        $this->assertTrue($transaction->business->is($business));
    }
}
