<?php

declare(strict_types=1);

namespace Features\TaxRecord\Data;

use Carbon\Carbon;
use Features\TaxRecord\Enums\CalculateTaxRecordModeEnum;
use Features\TaxRecord\Enums\CategoryTypeEnum;
use Features\TaxRecord\Enums\TaxRecordStatusEnum;
use Features\TaxRecordItem\Data\TaxRecordItemData;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class CalculateTaxRecordData extends Data
{
    public function __construct(
        public CalculateTaxRecordModeEnum $mode,
        public CategoryTypeEnum $category_type,
        public string $transaction_reference,
        public Carbon $sales_date,
        /**
         * @var array<TaxRecordItemData>
         */
        public array $items,
        #[Min(0)]
        public float $order_discount = 0
    ) {}

    public function toTaxRecordData(
        string $user_id,
        float $gross_amount,
        float $taxable_amount,
        float $tax_amount,
        Carbon $valid_until,
    ): TaxRecordData {
        return new TaxRecordData(
            user_id: $user_id,
            sales_date: $this->sales_date,
            transaction_reference: $this->transaction_reference,
            gross_amount: $gross_amount,
            order_discount: $this->order_discount,
            taxable_amount: $taxable_amount,
            tax_amount: $tax_amount,
            valid_until: $valid_until,
            status: TaxRecordStatusEnum::Acknowledged,
            category_type: $this->category_type,
        );
    }
}
