<?php

declare(strict_types=1);

namespace Features\TaxRecord\Tests\Controllers\Api;

use Features\TaxRecord\Enums\CalculateTaxRecordModeEnum;
use Features\TaxRecord\Enums\CategoryTypeEnum;
use Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CalculateTaxRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_calculate_tax_with_authenticated_user(): void
    {
        $user = User::factory()->create();

        $requestData = [
            'mode' => CalculateTaxRecordModeEnum::Acknowledge->value,
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
            'transaction_reference' => 'TX12345678',
            'sales_date' => now(),
            'items' => [
                [
                    'item_name' => 'Test Item 1',
                    'unit_price' => 50.0,
                    'quantity' => 2,
                    'discount_amount' => 0.0,
                ],
                [
                    'item_name' => 'Test Item 2',
                    'unit_price' => 30.0,
                    'quantity' => 1,
                    'discount_amount' => 0.0,
                ],
            ],
            'order_discount' => 10.0,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.tax.calculate'), $requestData);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'user_id',
            'sales_date',
            'transaction_reference',
            'gross_amount',
            'order_discount',
            'taxable_amount',
            'tax_amount',
            'total_amount',
            'valid_until',
            'status',
            'category_type',
        ]);

        // Verify response data
        $responseData = $response->json();
        $this->assertEquals($user->id, $responseData['user_id']);
        $this->assertEquals('TX12345678', $responseData['transaction_reference']);
        $this->assertEquals(130.0, $responseData['gross_amount']); // (50*2) + (30*1)
        $this->assertEquals(10.0, $responseData['order_discount']);
        $this->assertEquals(120.0, $responseData['taxable_amount']); // 130 - 10
        $this->assertEqualsWithDelta(14.4, $responseData['tax_amount'], 0.001); // 12% of 120

        // Verify tax record was created in database
        $this->assertDatabaseHas('tax_records', [
            'user_id' => $user->id,
            'transaction_reference' => 'TX12345678',
            'gross_amount' => $this->convertMoney(130.0),
            'order_discount' => $this->convertMoney(10.0),
            'taxable_amount' => $this->convertMoney(120.0),
            'tax_amount' => $this->convertMoney(14.4),
            'total_amount' => $this->convertMoney(134.4),
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
        ]);

        // Verify tax record items were created
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

    public function test_preview_mode_does_not_create_database_records(): void
    {
        $user = User::factory()->create();

        $requestData = [
            'mode' => CalculateTaxRecordModeEnum::Preview->value,
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
            'transaction_reference' => 'TX-PREVIEW',
            'sales_date' => now(),
            'items' => [
                [
                    'item_name' => 'Preview Item',
                    'unit_price' => 100.0,
                    'quantity' => 1,
                    'discount_amount' => 0.0,
                ],
            ],
            'order_discount' => 0.0,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.tax.calculate'), $requestData);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'user_id',
            'sales_date',
            'transaction_reference',
            'gross_amount',
            'order_discount',
            'taxable_amount',
            'tax_amount',
            'total_amount',
            'valid_until',
            'status',
            'category_type',
        ]);

        // Verify response data
        $responseData = $response->json();
        $this->assertEquals($user->id, $responseData['user_id']);
        $this->assertEquals('TX-PREVIEW', $responseData['transaction_reference']);
        $this->assertEquals(100.0, $responseData['gross_amount']);
        $this->assertEquals(12.0, $responseData['tax_amount']); // 12% of 100

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

    public function test_unauthenticated_request_returns_401(): void
    {
        $requestData = [
            'mode' => CalculateTaxRecordModeEnum::Acknowledge->value,
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
            'transaction_reference' => 'TX12345678',
            'sales_date' => now(),
            'items' => [
                [
                    'item_name' => 'Test Item',
                    'unit_price' => 50.0,
                    'quantity' => 1,
                    'discount_amount' => 0.0,
                ],
            ],
        ];

        $response = $this->postJson(route('api.tax.calculate'), $requestData);

        $response->assertUnauthorized();

        // Verify no records were created
        $this->assertDatabaseCount('tax_records', 0);
        $this->assertDatabaseCount('tax_record_items', 0);
    }

    public function test_calculates_tax_with_discounts(): void
    {
        $user = User::factory()->create();

        $requestData = [
            'mode' => CalculateTaxRecordModeEnum::Acknowledge->value,
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
            'transaction_reference' => 'TX87654321',
            'sales_date' => now(),
            'items' => [
                [
                    'item_name' => 'Premium Service',
                    'unit_price' => 100.0,
                    'quantity' => 1,
                    'discount_amount' => 5.0,
                ],
            ],
            'order_discount' => 15.0,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.tax.calculate'), $requestData);

        $response->assertSuccessful();

        // Verify response calculation
        $responseData = $response->json();
        $this->assertEquals(95.0, $responseData['gross_amount']); // 100 - 5 (item discount)
        $this->assertEquals(15.0, $responseData['order_discount']);
        $this->assertEquals(80.0, $responseData['taxable_amount']); // 95 - 15
        $this->assertEquals(9.6, $responseData['tax_amount']); // 12% of 80

        // Verify database records
        $this->assertDatabaseHas('tax_records', [
            'user_id' => $user->id,
            'transaction_reference' => 'TX87654321',
            'gross_amount' => $this->convertMoney(95.0),
            'order_discount' => $this->convertMoney(15.0),
            'taxable_amount' => $this->convertMoney(80.0),
            'tax_amount' => $this->convertMoney(9.6),
        ]);

        $this->assertDatabaseHas('tax_record_items', [
            'item_name' => 'Premium Service',
            'unit_price' => $this->convertMoney(100.0),
            'discount_amount' => $this->convertMoney(5.0),
            'quantity' => 1,
        ]);
    }

    public function test_handles_multiple_items(): void
    {
        $user = User::factory()->create();

        $requestData = [
            'mode' => CalculateTaxRecordModeEnum::Acknowledge->value,
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
            'transaction_reference' => 'TX99999999',
            'sales_date' => now(),
            'items' => [
                [
                    'item_name' => 'Item 1',
                    'unit_price' => 25.0,
                    'quantity' => 3,
                    'discount_amount' => 0.0,
                ],
                [
                    'item_name' => 'Item 2',
                    'unit_price' => 15.0,
                    'quantity' => 2,
                    'discount_amount' => 5.0,
                ],
                [
                    'item_name' => 'Item 3',
                    'unit_price' => 40.0,
                    'quantity' => 1,
                    'discount_amount' => 0.0,
                ],
            ],
            'order_discount' => 0.0,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.tax.calculate'), $requestData);

        $response->assertSuccessful();

        // Verify database records were created for all items
        $this->assertDatabaseHas('tax_records', [
            'user_id' => $user->id,
            'transaction_reference' => 'TX99999999',
            'order_discount' => $this->convertMoney(0.0),
        ]);

        // Verify all three items were created
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

        // Verify exactly 3 items were created
        $this->assertDatabaseCount('tax_record_items', 3);
    }

    public function test_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.tax.calculate'), []);

        $response->assertUnprocessable();

        // Verify no records were created
        $this->assertDatabaseCount('tax_records', 0);
        $this->assertDatabaseCount('tax_record_items', 0);
    }

    public function test_validates_item_structure(): void
    {
        $user = User::factory()->create();

        $requestData = [
            'mode' => CalculateTaxRecordModeEnum::Acknowledge->value,
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING->value,
            'transaction_reference' => 'TX12345678',
            'sales_date' => now(),
            'items' => [
                [
                    // Missing required fields
                    'unit_price' => 50.0,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.tax.calculate'), $requestData);

        $response->assertUnprocessable();

        // Verify no records were created
        $this->assertDatabaseCount('tax_records', 0);
        $this->assertDatabaseCount('tax_record_items', 0);
    }
}
