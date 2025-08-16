<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\TaxRecord\Actions\GetTotalGrossAmountAction;
use App\Features\TaxRecordItem\Data\TaxRecordItemData;
use Tests\TestCase;

final class GetTotalGrossAmountActionTest extends TestCase
{
    private GetTotalGrossAmountAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = app(GetTotalGrossAmountAction::class);
    }

    public function test_returns_zero_for_empty_array(): void
    {
        $result = $this->action->handle([]);

        $this->assertEquals(0.0, $result);
    }

    public function test_can_calculate_total_with_single_tax_record_item_data(): void
    {
        $item = new TaxRecordItemData(
            unit_price: 100.0,
            item_name: 'Test Item',
            quantity: 2,
            discount_amount: 10.0
        );

        $result = $this->action->handle([$item]);

        // (100 * 2) - 10 = 190
        $this->assertEquals(190.0, $result);
    }

    public function test_can_calculate_total_with_multiple_tax_record_item_data(): void
    {
        $items = [
            new TaxRecordItemData(
                unit_price: 50.0,
                item_name: 'Item 1',
                quantity: 2,
                discount_amount: 5.0
            ),
            new TaxRecordItemData(
                unit_price: 30.0,
                item_name: 'Item 2',
                quantity: 3,
                discount_amount: 10.0
            ),
            new TaxRecordItemData(
                unit_price: 20.0,
                item_name: 'Item 3',
                quantity: 1,
                discount_amount: 0.0
            ),
        ];

        $result = $this->action->handle($items);

        // Item 1: (50 * 2) - 5 = 95
        // Item 2: (30 * 3) - 10 = 80
        // Item 3: (20 * 1) - 0 = 20
        // Total: 95 + 80 + 20 = 195
        $this->assertEquals(195.0, $result);
    }

    public function test_can_handle_array_data_conversion(): void
    {
        $items = [
            [
                'unit_price' => 25.0,
                'item_name' => 'Converted Item 1',
                'quantity' => 4,
                'discount_amount' => 5.0,
            ],
            [
                'unit_price' => 15.0,
                'item_name' => 'Converted Item 2',
                'quantity' => 2,
                'discount_amount' => 2.0,
            ],
        ];

        $result = $this->action->handle($items);

        // Item 1: (25 * 4) - 5 = 95
        // Item 2: (15 * 2) - 2 = 28
        // Total: 95 + 28 = 123
        $this->assertEquals(123.0, $result);
    }

    public function test_handles_zero_amounts_correctly(): void
    {
        $items = [
            new TaxRecordItemData(
                unit_price: 0.0,
                item_name: 'Zero Price Item',
                quantity: 5,
                discount_amount: 0.0
            ),
            new TaxRecordItemData(
                unit_price: 100.0,
                item_name: 'Normal Item',
                quantity: 0,
                discount_amount: 0.0
            ),
        ];

        $result = $this->action->handle($items);

        // Item 1: (0 * 5) - 0 = 0
        // Item 2: (100 * 0) - 0 = 0
        // Total: 0
        $this->assertEquals(0.0, $result);
    }

    public function test_handles_discount_greater_than_item_total(): void
    {
        $item = new TaxRecordItemData(
            unit_price: 50.0,
            item_name: 'Heavily Discounted Item',
            quantity: 1,
            discount_amount: 100.0
        );

        $result = $this->action->handle([$item]);

        // (50 * 1) - 100 = -50, but total should be 0 based on CalculateTaxRecordItemAction logic
        $this->assertEquals(0.0, $result);
    }

    public function test_can_handle_decimal_calculations(): void
    {
        $items = [
            new TaxRecordItemData(
                unit_price: 12.99,
                item_name: 'Decimal Item 1',
                quantity: 3,
                discount_amount: 2.50
            ),
            new TaxRecordItemData(
                unit_price: 8.75,
                item_name: 'Decimal Item 2',
                quantity: 2,
                discount_amount: 1.25
            ),
        ];

        $result = $this->action->handle($items);

        // Item 1: (12.99 * 3) - 2.50 = 36.47
        // Item 2: (8.75 * 2) - 1.25 = 16.25
        // Total: 36.47 + 16.25 = 52.72
        $this->assertEquals(52.72, $result);
    }

    public function test_can_handle_large_quantities(): void
    {
        $item = new TaxRecordItemData(
            unit_price: 1.0,
            item_name: 'Bulk Item',
            quantity: 1000,
            discount_amount: 50.0
        );

        $result = $this->action->handle([$item]);

        // (1.0 * 1000) - 50 = 950
        $this->assertEquals(950.0, $result);
    }

    public function test_mixed_positive_and_negative_item_totals(): void
    {
        $items = [
            new TaxRecordItemData(
                unit_price: 100.0,
                item_name: 'Positive Item',
                quantity: 2,
                discount_amount: 10.0
            ),
            new TaxRecordItemData(
                unit_price: 50.0,
                item_name: 'Negative Item',
                quantity: 1,
                discount_amount: 100.0
            ),
            new TaxRecordItemData(
                unit_price: 30.0,
                item_name: 'Another Positive Item',
                quantity: 3,
                discount_amount: 5.0
            ),
        ];

        $result = $this->action->handle($items);

        // Item 1: (100 * 2) - 10 = 190
        // Item 2: (50 * 1) - 100 = -50
        // Item 3: (30 * 3) - 5 = 85
        // Total: 190 + (-50) + 85 = 225
        // The CalculateTaxRecordItemAction sums all individual totals and only returns 0 if final sum <= 0
        $this->assertEquals(225.0, $result);
    }

    public function test_can_handle_single_array_item_conversion(): void
    {
        $items = [
            [
                'unit_price' => 99.99,
                'item_name' => 'Single Array Item',
                'quantity' => 1,
                'discount_amount' => 9.99,
            ],
        ];

        $result = $this->action->handle($items);

        // (99.99 * 1) - 9.99 = 90.00
        $this->assertEquals(90.0, $result);
    }
}
