<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\TaxRecord\Actions\RemitTaxAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use App\Features\Transaction\Models\Transaction;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RemitTaxActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_remits_acknowledged_tax_record_and_creates_transaction(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Acknowledged,
            'tax_amount' => 1500.00,
            'transaction_reference' => 'TR-987654321',
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($taxRecord);

        // Assert tax record status is updated
        $taxRecord->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Paid, $taxRecord->status);

        // Assert transaction is created
        $this->assertDatabaseHas('transactions', [
            'tax_record_id' => $taxRecord->id,
            'user_id' => $user->id,
            'amount' => '1500.00',
            'type' => TransactionTypeEnum::TaxRemittance->value,
            'status' => TransactionStatusEnum::Completed->value,
        ]);

        $transaction = Transaction::where('tax_record_id', $taxRecord->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals($user->id, $transaction->user_id);
        $this->assertStringStartsWith('TXN-', $transaction->reference_number);
    }

    public function test_remits_expired_tax_record_and_creates_transaction(): void
    {
        // Arrange
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Expired,
            'tax_amount' => 750.25,
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($taxRecord);

        // Assert
        $taxRecord->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Paid, $taxRecord->status);

        $this->assertDatabaseHas('transactions', [
            'tax_record_id' => $taxRecord->id,
            'user_id' => $user->id,
            'amount' => '750.25',
        ]);
    }

    public function test_does_not_remit_cancelled_tax_record(): void
    {
        // Arrange
        User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Cancelled,
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($taxRecord);

        // Assert - status remains unchanged and no transaction created
        $taxRecord->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $taxRecord->status);

        $this->assertDatabaseMissing('transactions', [
            'tax_record_id' => $taxRecord->id,
        ]);
    }

    public function test_does_not_remit_already_paid_tax_record(): void
    {
        // Arrange
        User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Paid,
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($taxRecord);

        // Assert - status remains unchanged and no transaction created
        $taxRecord->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Paid, $taxRecord->status);

        $this->assertDatabaseMissing('transactions', [
            'tax_record_id' => $taxRecord->id,
        ]);
    }

    public function test_uses_authenticated_user_when_no_user_provided(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $taxRecord = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $action = app(RemitTaxAction::class);

        // Act - no user parameter provided
        $action->handle($taxRecord);

        // Assert transaction is created with authenticated user
        $this->assertDatabaseHas('transactions', [
            'tax_record_id' => $taxRecord->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_transaction_contains_correct_metadata(): void
    {
        // Arrange
        User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Acknowledged,
            'gross_amount' => 10000.00,
            'taxable_amount' => 9500.00,
            'tax_amount' => 950.00,
            'total_amount' => 10450.00,
            'transaction_reference' => 'TR-META-TEST',
        ]);

        $action = app(RemitTaxAction::class);

        // Act
        $action->handle($taxRecord);

        // Assert
        $transaction = Transaction::where('tax_record_id', $taxRecord->id)->first();
        $this->assertNotNull($transaction);

        $expectedMetadata = [
            'tax_record_reference' => 'TR-META-TEST',
            'gross_amount' => 10000.00,
            'taxable_amount' => 9500.00,
            'tax_amount' => 950.00,
            'total_amount' => 10450.00,
        ];

        $this->assertEquals($expectedMetadata, $transaction->metadata);
    }
}
