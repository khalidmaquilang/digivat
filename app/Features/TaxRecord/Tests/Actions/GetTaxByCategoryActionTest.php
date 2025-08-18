<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\TaxRecord\Actions\GetTaxByCategoryAction;
use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use Tests\TestCase;

final class GetTaxByCategoryActionTest extends TestCase
{
    private GetTaxByCategoryAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = app(GetTaxByCategoryAction::class);
    }

    public function test_can_calculate_tax_for_digital_streaming(): void
    {
        $taxableAmount = 100.0;
        $expectedTax = 12.0; // 12% of 100

        $result = $this->action->handle(CategoryTypeEnum::DIGITAL_STREAMING, $taxableAmount);

        $this->assertEquals($expectedTax, $result);
    }

    public function test_can_calculate_tax_for_retail_goods(): void
    {
        $taxableAmount = 250.0;
        $expectedTax = 30.0; // 12% of 250

        $result = $this->action->handle(CategoryTypeEnum::RETAIL_GOODS, $taxableAmount);

        $this->assertEquals($expectedTax, $result);
    }

    public function test_can_calculate_tax_for_professional_services(): void
    {
        $taxableAmount = 500.0;
        $expectedTax = 60.0; // 12% of 500

        $result = $this->action->handle(CategoryTypeEnum::PROFESSIONAL_SERVICES, $taxableAmount);

        $this->assertEquals($expectedTax, $result);
    }

    public function test_returns_zero_for_zero_taxable_amount(): void
    {
        $result = $this->action->handle(CategoryTypeEnum::DIGITAL_STREAMING, 0.0);

        $this->assertEquals(0.0, $result);
    }

    public function test_returns_zero_for_negative_taxable_amount(): void
    {
        $result = $this->action->handle(CategoryTypeEnum::RETAIL_GOODS, -50.0);

        $this->assertEquals(0.0, $result);
    }

    public function test_can_calculate_tax_for_small_decimal_amounts(): void
    {
        $taxableAmount = 0.50;
        $expectedTax = 0.06; // 12% of 0.50

        $result = $this->action->handle(CategoryTypeEnum::SOFTWARE_LICENSE, $taxableAmount);

        $this->assertEquals($expectedTax, $result);
    }

    public function test_can_calculate_tax_for_large_amounts(): void
    {
        $taxableAmount = 10000.0;
        $expectedTax = 1200.0; // 12% of 10000

        $result = $this->action->handle(CategoryTypeEnum::AUTOMOBILES, $taxableAmount);

        $this->assertEquals($expectedTax, $result);
    }

    public function test_can_calculate_tax_for_export_category(): void
    {
        $taxableAmount = 100.0;
        $expectedTax = 12.0; // Currently all categories use 12% VAT

        $result = $this->action->handle(CategoryTypeEnum::EXPORT, $taxableAmount);

        $this->assertEquals($expectedTax, $result);
    }

    public function test_can_calculate_tax_for_education_category(): void
    {
        $taxableAmount = 300.0;
        $expectedTax = 36.0; // Currently all categories use 12% VAT

        $result = $this->action->handle(CategoryTypeEnum::EDUCATION, $taxableAmount);

        $this->assertEquals($expectedTax, $result);
    }

    public function test_precision_with_decimal_calculations(): void
    {
        $taxableAmount = 33.33;
        $expectedTax = 3.9996; // 12% of 33.33

        $result = $this->action->handle(CategoryTypeEnum::DIGITAL_DOWNLOADS, $taxableAmount);

        $this->assertEqualsWithDelta($expectedTax, $result, 0.0001);
    }
}
