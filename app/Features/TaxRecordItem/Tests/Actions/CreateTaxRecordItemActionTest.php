<?php

declare(strict_types=1);

namespace App\Features\TaxRecordItem\Tests\Actions;

use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\TaxRecordItem\Actions\CreateTaxRecordItemAction;
use App\Features\TaxRecordItem\Data\TaxRecordItemData;
use App\Features\TaxRecordItem\Models\TaxRecordItem;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateTaxRecordItemActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_tax_record_item(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data = new TaxRecordItemData(
            item_name: 'Test Item',
            unit_price: 100.0,
            tax_record_id: $tax_record->id,
            quantity: 2,
            discount_amount: 10.0
        );

        $result = app(CreateTaxRecordItemAction::class)->handle($data);

        $this->assertInstanceOf(TaxRecordItem::class, $result);
        $this->assertEquals($tax_record->id, $result->tax_record_id);
        $this->assertEquals('Test Item', $result->item_name);

        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $data->tax_record_id,
            'item_name' => $data->item_name,
            'unit_price' => $this->convertMoney($data->unit_price),
            'quantity' => $data->quantity,
            'discount_amount' => $this->convertMoney($data->discount_amount),
            'total' => $this->convertMoney($data->total),
        ]);
    }

    public function test_can_create_item_without_discount(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data = new TaxRecordItemData(
            item_name: 'No Discount Item',
            unit_price: 75.0,
            tax_record_id: $tax_record->id,
            quantity: 3,
            discount_amount: 0.0
        );

        $result = app(CreateTaxRecordItemAction::class)->handle($data);

        $this->assertInstanceOf(TaxRecordItem::class, $result);

        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $data->tax_record_id,
            'item_name' => $data->item_name,
            'unit_price' => $this->convertMoney($data->unit_price),
            'quantity' => $data->quantity,
            'discount_amount' => $this->convertMoney(0.0),
            'total' => $this->convertMoney(225.0), // 75 * 3 = 225
        ]);
    }

    public function test_can_create_item_with_single_quantity(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data = new TaxRecordItemData(
            item_name: 'Single Item',
            unit_price: 50.0,
            tax_record_id: $tax_record->id,
            quantity: 1,
            discount_amount: 5.0
        );

        $result = app(CreateTaxRecordItemAction::class)->handle($data);

        $this->assertInstanceOf(TaxRecordItem::class, $result);

        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $data->tax_record_id,
            'item_name' => $data->item_name,
            'unit_price' => $this->convertMoney($data->unit_price),
            'quantity' => 1,
            'discount_amount' => $this->convertMoney($data->discount_amount),
            'total' => $this->convertMoney(45.0), // 50 * 1 - 5 = 45
        ]);
    }

    public function test_can_create_item_with_decimal_prices(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data = new TaxRecordItemData(
            item_name: 'Decimal Price Item',
            unit_price: 12.99,
            tax_record_id: $tax_record->id,
            quantity: 4,
            discount_amount: 2.50
        );

        $result = app(CreateTaxRecordItemAction::class)->handle($data);

        $this->assertInstanceOf(TaxRecordItem::class, $result);

        $expectedTotal = (12.99 * 4) - 2.50; // 51.96 - 2.50 = 49.46

        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $data->tax_record_id,
            'item_name' => $data->item_name,
            'unit_price' => $this->convertMoney($data->unit_price),
            'quantity' => $data->quantity,
            'discount_amount' => $this->convertMoney($data->discount_amount),
            'total' => $this->convertMoney($expectedTotal),
        ]);
    }

    public function test_can_create_item_with_high_quantity(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data = new TaxRecordItemData(
            item_name: 'Bulk Purchase',
            unit_price: 25.0,
            tax_record_id: $tax_record->id,
            quantity: 100,
            discount_amount: 50.0
        );

        $result = app(CreateTaxRecordItemAction::class)->handle($data);

        $this->assertInstanceOf(TaxRecordItem::class, $result);

        $expectedTotal = (25.0 * 100) - 50.0; // 2500 - 50 = 2450

        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $data->tax_record_id,
            'item_name' => $data->item_name,
            'unit_price' => $this->convertMoney($data->unit_price),
            'quantity' => 100,
            'discount_amount' => $this->convertMoney($data->discount_amount),
            'total' => $this->convertMoney($expectedTotal),
        ]);
    }

    public function test_can_create_item_with_zero_total(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data = new TaxRecordItemData(
            item_name: 'Free Item',
            unit_price: 100.0,
            tax_record_id: $tax_record->id,
            quantity: 1,
            discount_amount: 100.0 // Full discount
        );

        $result = app(CreateTaxRecordItemAction::class)->handle($data);

        $this->assertInstanceOf(TaxRecordItem::class, $result);

        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $data->tax_record_id,
            'item_name' => $data->item_name,
            'unit_price' => $this->convertMoney($data->unit_price),
            'quantity' => $data->quantity,
            'discount_amount' => $this->convertMoney($data->discount_amount),
            'total' => $this->convertMoney(0.0), // 100 - 100 = 0
        ]);
    }

    public function test_can_create_multiple_items_for_same_tax_record(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data1 = new TaxRecordItemData(
            item_name: 'First Item',
            unit_price: 50.0,
            tax_record_id: $tax_record->id,
            quantity: 1,
            discount_amount: 0.0
        );

        $data2 = new TaxRecordItemData(
            item_name: 'Second Item',
            unit_price: 75.0,
            tax_record_id: $tax_record->id,
            quantity: 2,
            discount_amount: 10.0
        );

        $action = app(CreateTaxRecordItemAction::class);

        $result1 = $action->handle($data1);
        $result2 = $action->handle($data2);

        $this->assertInstanceOf(TaxRecordItem::class, $result1);
        $this->assertInstanceOf(TaxRecordItem::class, $result2);

        // Verify both items were created for the same tax record
        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $tax_record->id,
            'item_name' => 'First Item',
            'unit_price' => $this->convertMoney(50.0),
            'total' => $this->convertMoney(50.0),
        ]);

        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $tax_record->id,
            'item_name' => 'Second Item',
            'unit_price' => $this->convertMoney(75.0),
            'total' => $this->convertMoney(140.0), // 75 * 2 - 10 = 140
        ]);

        // Verify we have exactly 2 items for this tax record
        $this->assertDatabaseCount('tax_record_items', 2);
    }

    public function test_creates_item_with_correct_timestamps(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data = new TaxRecordItemData(
            item_name: 'Timestamp Test Item',
            unit_price: 30.0,
            tax_record_id: $tax_record->id,
            quantity: 1,
            discount_amount: 0.0
        );

        $beforeCreation = now()->subSecond();
        $result = app(CreateTaxRecordItemAction::class)->handle($data);
        $afterCreation = now()->addSecond();

        $this->assertInstanceOf(TaxRecordItem::class, $result);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);

        // Verify timestamps are within expected range
        $this->assertTrue($result->created_at->between($beforeCreation, $afterCreation));
        $this->assertTrue($result->updated_at->between($beforeCreation, $afterCreation));
    }

    public function test_total_calculation_matches_data_object(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create(['user_id' => $user->id]);

        $data = new TaxRecordItemData(
            item_name: 'Calculation Test',
            unit_price: 33.33,
            tax_record_id: $tax_record->id,
            quantity: 3,
            discount_amount: 7.77
        );

        app(CreateTaxRecordItemAction::class)->handle($data);

        // Verify the calculated total in the data object matches the database record
        $expectedTotal = $data->total; // This is calculated in TaxRecordItemData constructor

        $this->assertDatabaseHas('tax_record_items', [
            'tax_record_id' => $data->tax_record_id,
            'item_name' => $data->item_name,
            'total' => $this->convertMoney($expectedTotal),
        ]);

        // Verify the calculated total in the data object
        $this->assertEquals($expectedTotal, $data->total);
    }
}
