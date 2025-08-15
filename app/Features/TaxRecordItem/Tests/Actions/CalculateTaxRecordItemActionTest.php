<?php

declare(strict_types=1);

namespace App\Features\TaxRecordItem\Tests\Actions;

use App\Features\TaxRecordItem\Actions\CalculateTaxRecordItemAction;
use App\Features\TaxRecordItem\Data\TaxRecordItemData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CalculateTaxRecordItemActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_calculate_single_item_total(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Test Item',
                unit_price: 100.0,
                quantity: 2,
                discount_amount: 0.0
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        $this->assertEquals(200.0, $result); // 100 * 2 - 0
    }

    public function test_can_calculate_single_item_with_discount(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Discounted Item',
                unit_price: 100.0,
                quantity: 1,
                discount_amount: 15.0
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        $this->assertEquals(85.0, $result); // 100 * 1 - 15
    }

    public function test_can_calculate_multiple_items_total(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Item 1',
                unit_price: 50.0,
                quantity: 2,
                discount_amount: 0.0
            ),
            new TaxRecordItemData(
                item_name: 'Item 2',
                unit_price: 30.0,
                quantity: 3,
                discount_amount: 10.0
            ),
            new TaxRecordItemData(
                item_name: 'Item 3',
                unit_price: 75.0,
                quantity: 1,
                discount_amount: 5.0
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        // Item 1: 50 * 2 - 0 = 100
        // Item 2: 30 * 3 - 10 = 80
        // Item 3: 75 * 1 - 5 = 70
        // Total: 100 + 80 + 70 = 250
        $this->assertEquals(250.0, $result);
    }

    public function test_can_calculate_with_various_quantities(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Bulk Item',
                unit_price: 25.0,
                quantity: 10,
                discount_amount: 50.0
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        $this->assertEquals(200.0, $result); // 25 * 10 - 50 = 200
    }

    public function test_handles_zero_discount(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'No Discount Item',
                unit_price: 45.50,
                quantity: 4,
                discount_amount: 0.0
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        $this->assertEquals(182.0, $result); // 45.50 * 4 = 182.0
    }

    public function test_handles_decimal_values(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Decimal Item',
                unit_price: 12.99,
                quantity: 3,
                discount_amount: 2.50
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        $this->assertEquals(36.47, $result); // 12.99 * 3 - 2.50 = 36.47
    }

    public function test_returns_zero_for_negative_total(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Over-discounted Item',
                unit_price: 50.0,
                quantity: 1,
                discount_amount: 75.0 // Discount > total
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        $this->assertEquals(0.0, $result); // Should return 0 when total would be negative
    }

    public function test_returns_zero_for_zero_total(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Fully Discounted Item',
                unit_price: 100.0,
                quantity: 1,
                discount_amount: 100.0 // Discount equals total
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        $this->assertEquals(0.0, $result); // Should return 0 when total is exactly zero
    }

    public function test_handles_empty_items_array(): void
    {
        $items = [];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        $this->assertEquals(0.0, $result); // Should return 0 for empty array
    }

    public function test_handles_mixed_positive_and_negative_items(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Positive Item',
                unit_price: 100.0,
                quantity: 2,
                discount_amount: 20.0
            ),
            new TaxRecordItemData(
                item_name: 'Negative Item',
                unit_price: 30.0,
                quantity: 1,
                discount_amount: 50.0 // Results in negative
            ),
            new TaxRecordItemData(
                item_name: 'Another Positive Item',
                unit_price: 75.0,
                quantity: 1,
                discount_amount: 0.0
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        // Item 1: 100 * 2 - 20 = 180
        // Item 2: 30 * 1 - 50 = -20 (negative, contributes negatively to subtotal)
        // Item 3: 75 * 1 - 0 = 75
        // Subtotal: 180 + (-20) + 75 = 235
        // Since subtotal > 0, should return 235
        $this->assertEquals(235.0, $result);
    }

    public function test_handles_large_quantities_and_amounts(): void
    {
        $items = [
            new TaxRecordItemData(
                item_name: 'Large Quantity Item',
                unit_price: 999.99,
                quantity: 100,
                discount_amount: 5000.0
            ),
        ];

        $action = app(CalculateTaxRecordItemAction::class);
        $result = $action->handle($items);

        // 999.99 * 100 - 5000 = 99999 - 5000 = 94999
        $this->assertEquals(94999.0, $result);
    }
}
