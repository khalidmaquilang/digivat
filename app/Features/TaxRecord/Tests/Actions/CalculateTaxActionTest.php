<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\TaxRecord\Actions\CalculateTaxAction;
use App\Features\TaxRecord\Data\CalculateTaxRecordData;
use App\Features\TaxRecord\Enums\CalculateTaxRecordModeEnum;
use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\TaxRecordItem\Data\TaxRecordItemData;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CalculateTaxActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_calculate_tax_and_create_records(): void
    {
        $user = User::factory()->create();

        $items = [
            new TaxRecordItemData(
                item_name: 'Test Item 1',
                unit_price: 50.0,
                quantity: 2
            ),
            new TaxRecordItemData(
                item_name: 'Test Item 2',
                unit_price: 30.0,
                quantity: 1
            ),
        ];

        $salesDate = now();
        $data = new CalculateTaxRecordData(
            mode: CalculateTaxRecordModeEnum::Acknowledge,
            category_type: CategoryTypeEnum::DIGITAL_STREAMING,
            transaction_reference: 'TX12345678',
            sales_date: $salesDate,
            items: $items,
            order_discount: 10.0
        );

        $action = app(CalculateTaxAction::class);
        $result = $action->handle($data, $user->id, 'https://example.com');

        // Verify the result structure
        $this->assertIsArray($result);
        $this->assertArrayHasKey('transaction_reference', $result);
        $this->assertArrayHasKey('gross_amount', $result);
        $this->assertArrayHasKey('taxable_amount', $result);
        $this->assertArrayHasKey('tax_amount', $result);
        $this->assertEquals('TX12345678', $result['transaction_reference']);

        // Verify tax record was created in database
        $this->assertDatabaseHas('tax_records', [
            'user_id' => $user->id,
            'transaction_reference' => 'TX12345678',
            'gross_amount' => $this->convertMoney(130.0), // (50*2) + (30*1)
            'order_discount' => $this->convertMoney(10.0),
            'taxable_amount' => $this->convertMoney(120.0), // 130 - 10
            'tax_amount' => $this->convertMoney(14.4), // 12% of 120
            'total_amount' => $this->convertMoney(134.4), // 120 + 14.4
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
        ]);

        // Verify tax record items were created in database
        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Test Item 1',
            'unit_price' => $this->convertMoney(50.0),
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Test Item 2',
            'unit_price' => $this->convertMoney(30.0),
            'quantity' => 1,
        ]);
    }

    public function test_calculates_correct_amounts_with_discount(): void
    {
        $user = User::factory()->create();

        $items = [
            new TaxRecordItemData(
                item_name: 'Premium Service',
                unit_price: 100.0,
                quantity: 1
            ),
        ];

        $data = new CalculateTaxRecordData(
            mode: CalculateTaxRecordModeEnum::Acknowledge,
            category_type: CategoryTypeEnum::DIGITAL_STREAMING,
            transaction_reference: 'TX87654321',
            sales_date: now(),
            items: $items,
            order_discount: 20.0
        );

        $action = app(CalculateTaxAction::class);
        $result = $action->handle($data, $user->id, 'https://example.com');

        // Verify calculation results
        $this->assertIsArray($result);
        $this->assertEquals(100.0, $result['gross_amount']); // 100*1
        $this->assertEquals(80.0, $result['taxable_amount']); // 100 - 20 (discount)
        $this->assertEquals(9.6, $result['tax_amount']); // 12% of 80

        // Verify tax record was created in database
        $this->assertDatabaseHas('tax_records', [
            'user_id' => $user->id,
            'transaction_reference' => 'TX87654321',
            'gross_amount' => $this->convertMoney(100.0),
            'order_discount' => $this->convertMoney(20.0),
            'taxable_amount' => $this->convertMoney(80.0),
            'tax_amount' => $this->convertMoney(9.6),
            'total_amount' => $this->convertMoney(89.6), // 80 + 9.6
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
        ]);

        // Verify tax record item was created in database
        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Premium Service',
            'unit_price' => $this->convertMoney(100.0),
            'quantity' => 1,
        ]);
    }

    public function test_handles_multiple_items_correctly(): void
    {
        $user = User::factory()->create();

        $items = [
            new TaxRecordItemData(
                item_name: 'Item 1',
                unit_price: 25.0,
                quantity: 3
            ),
            new TaxRecordItemData(
                item_name: 'Item 2',
                unit_price: 15.0,
                quantity: 2,
                discount_amount: 5.0
            ),
            new TaxRecordItemData(
                item_name: 'Item 3',
                unit_price: 40.0,
                quantity: 1
            ),
        ];

        $data = new CalculateTaxRecordData(
            mode: CalculateTaxRecordModeEnum::Acknowledge,
            category_type: CategoryTypeEnum::DIGITAL_STREAMING,
            transaction_reference: 'TX99999999',
            sales_date: now(),
            items: $items,
            order_discount: 0.0
        );

        $action = app(CalculateTaxAction::class);
        $result = $action->handle($data, $user->id, 'https://example.com');

        $this->assertIsArray($result);

        // Verify tax record was created in database
        $this->assertDatabaseHas('tax_records', [
            'user_id' => $user->id,
            'transaction_reference' => 'TX99999999',
            'order_discount' => $this->convertMoney(0.0),
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
        ]);

        // Verify all three items were created in database
        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Item 1',
            'unit_price' => $this->convertMoney(25.0),
            'quantity' => 3,
        ]);

        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Item 2',
            'unit_price' => $this->convertMoney(15.0),
            'quantity' => 2,
            'discount_amount' => $this->convertMoney(5.0),
        ]);

        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Item 3',
            'unit_price' => $this->convertMoney(40.0),
            'quantity' => 1,
        ]);

        // Verify we have exactly 3 items in the database
        $this->assertDatabaseCount('tax_record_items', 3);
    }

    public function test_preview_mode_does_not_create_database_records(): void
    {
        $user = User::factory()->create();

        $items = [
            new TaxRecordItemData(
                item_name: 'Preview Item',
                unit_price: 50.0,
                quantity: 1
            ),
        ];

        $data = new CalculateTaxRecordData(
            mode: CalculateTaxRecordModeEnum::Preview, // Using Preview mode
            category_type: CategoryTypeEnum::DIGITAL_STREAMING,
            transaction_reference: 'TX-PREVIEW',
            sales_date: now(),
            items: $items
        );

        $action = app(CalculateTaxAction::class);
        $result = $action->handle($data, $user->id, 'https://example.com');

        // Verify the result structure is returned
        $this->assertIsArray($result);
        $this->assertArrayHasKey('transaction_reference', $result);
        $this->assertArrayHasKey('gross_amount', $result);
        $this->assertArrayHasKey('taxable_amount', $result);
        $this->assertArrayHasKey('tax_amount', $result);

        // Verify NO records were created in database (Preview mode)
        $this->assertDatabaseMissing('tax_records', [
            'transaction_reference' => 'TX-PREVIEW',
        ]);

        $this->assertDatabaseMissing('tax_record_items', [
            'item_name' => 'Preview Item',
        ]);

        // Verify database is still empty
        $this->assertDatabaseCount('tax_records', 0);
        $this->assertDatabaseCount('tax_record_items', 0);
    }

    public function test_successful_transaction_creates_all_records(): void
    {
        $user = User::factory()->create();

        $items = [
            new TaxRecordItemData(
                item_name: 'Success Item',
                unit_price: 75.0,
                quantity: 1
            ),
        ];

        $data = new CalculateTaxRecordData(
            mode: CalculateTaxRecordModeEnum::Acknowledge,
            category_type: CategoryTypeEnum::DIGITAL_STREAMING,
            transaction_reference: 'TX22222222',
            sales_date: now(),
            items: $items
        );

        $action = app(CalculateTaxAction::class);
        $result = $action->handle($data, $user->id, 'https://example.com');

        $this->assertIsArray($result);
        $this->assertEquals('TX22222222', $result['transaction_reference']);
        $this->assertEquals(75.0, $result['gross_amount']);
        $this->assertEquals(9.0, $result['tax_amount']); // 12% of 75

        // Verify both tax record and item were successfully created (transaction committed)
        $this->assertDatabaseHas('tax_records', [
            'user_id' => $user->id,
            'transaction_reference' => 'TX22222222',
            'gross_amount' => $this->convertMoney(75.0),
            'taxable_amount' => $this->convertMoney(75.0),
            'tax_amount' => $this->convertMoney(9.0),
        ]);

        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Success Item',
            'unit_price' => $this->convertMoney(75.0),
            'quantity' => 1,
        ]);

        // Verify exactly one record of each was created
        $this->assertDatabaseCount('tax_records', 1);
        $this->assertDatabaseCount('tax_record_items', 1);
    }

    public function test_sets_valid_until_date_correctly(): void
    {
        $user = User::factory()->create();

        $items = [
            new TaxRecordItemData(
                item_name: 'Date Test Item',
                unit_price: 100.0,
                quantity: 1
            ),
        ];

        $salesDate = now()->subDay();
        now()->addMonth();

        $data = new CalculateTaxRecordData(
            mode: CalculateTaxRecordModeEnum::Acknowledge,
            category_type: CategoryTypeEnum::DIGITAL_STREAMING,
            transaction_reference: 'TX33333333',
            sales_date: $salesDate,
            items: $items
        );

        $action = app(CalculateTaxAction::class);
        $result = $action->handle($data, $user->id, 'https://example.com');

        $this->assertIsArray($result);

        // Verify tax record was created with correct dates in database
        $this->assertDatabaseHas('tax_records', [
            'user_id' => $user->id,
            'transaction_reference' => 'TX33333333',
            'sales_date' => $salesDate,
            'gross_amount' => $this->convertMoney(100.0),
            'taxable_amount' => $this->convertMoney(100.0),
            'tax_amount' => $this->convertMoney(12.0), // 12% of 100
        ]);

        // Get the created record to verify valid_until is approximately one month from now
        $createdRecord = TaxRecord::where('transaction_reference', 'TX33333333')->first();
        $this->assertNotNull($createdRecord);
        $this->assertTrue($createdRecord->valid_until->isAfter(now()->addDays(25)));
        $this->assertTrue($createdRecord->valid_until->isBefore(now()->addDays(35)));

        // Verify tax record item was also created
        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Date Test Item',
            'unit_price' => $this->convertMoney(100.0),
            'quantity' => 1,
        ]);
    }
}
