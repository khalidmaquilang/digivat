<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\Business\Models\Business;
use App\Features\TaxRecord\Actions\RemitTaxAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use App\Features\Transaction\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RemitTaxActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_remits_acknowledged_tax_record_and_creates_transaction(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
            'tax_amount' => 1500,
            'transaction_reference' => 'TR-987654321',
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($tax_record);

        // Assert tax record status is updated
        $tax_record->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Paid, $tax_record->status);

        // Assert transaction is created
        $this->assertDatabaseHas('transactions', [
            'tax_record_id' => $tax_record->id,
            'business_id' => $business->id,
            'amount' => $this->convertMoney($tax_record->tax_amount),
            'type' => TransactionTypeEnum::TaxRemittance->value,
            'status' => TransactionStatusEnum::Completed->value,
        ]);

        $transaction = Transaction::where('tax_record_id', $tax_record->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals($business->id, $transaction->business_id);
        $this->assertStringStartsWith('TXN-', $transaction->reference_number);
    }

    public function test_remits_expired_tax_record_and_creates_transaction(): void
    {
        // Arrange
        $tax_record = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Expired,
            'tax_amount' => 750.25,
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($tax_record);

        // Assert
        $tax_record->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Paid, $tax_record->status);

        $this->assertDatabaseHas('transactions', [
            'tax_record_id' => $tax_record->id,
            'business_id' => $tax_record->business_id,
            'amount' => $this->convertMoney($tax_record->tax_amount),
        ]);
    }

    public function test_does_not_remit_cancelled_tax_record(): void
    {
        // Arrange
        $tax_record = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Cancelled,
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($tax_record);

        // Assert - status remains unchanged and no transaction created
        $tax_record->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $tax_record->status);

        $this->assertDatabaseMissing('transactions', [
            'tax_record_id' => $tax_record->id,
        ]);
    }

    public function test_does_not_remit_already_paid_tax_record(): void
    {
        // Arrange
        Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Paid,
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($tax_record);

        // Assert - status remains unchanged and no transaction created
        $tax_record->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Paid, $tax_record->status);

        $this->assertDatabaseMissing('transactions', [
            'tax_record_id' => $tax_record->id,
        ]);
    }

    public function test_transaction_contains_correct_metadata(): void
    {
        // Arrange
        Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Acknowledged,
            'gross_amount' => $this->convertMoney(10000.00),
            'taxable_amount' => $this->convertMoney(9500.00),
            'tax_amount' => $this->convertMoney(950.00),
            'total_amount' => $this->convertMoney(10450.00),
            'transaction_reference' => 'TR-META-TEST',
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($tax_record);

        // Assert
        $transaction = Transaction::where('tax_record_id', $tax_record->id)->first();
        $this->assertNotNull($transaction);

        $expectedMetadata = [
            'tax_record_reference' => 'TR-META-TEST',
            'gross_amount' => $this->convertMoney(10000.00),
            'taxable_amount' => $this->convertMoney(9500.00),
            'tax_amount' => $this->convertMoney(950.00),
            'total_amount' => $this->convertMoney(10450.00),
        ];

        $this->assertEquals($expectedMetadata, $transaction->metadata);
    }
}
